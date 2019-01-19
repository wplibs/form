<?php

namespace WPLibs\Form;

use WP_Error;
use WPLibs\Form\Helper\Utils;
use WPLibs\Form\Helper\Values;
use WPLibs\Form\Helper\Dependency;
use WPLibs\Form\Helper\Rules_Parser;
use WPLibs\Form\Contracts\Form_Config;
use WPLibs\Form\Contracts\Deferred_Storage;
use WPLibs\Form\Contracts\Field as Field_Contract;
use WPLibs\Form\Helper\Validator;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

class Form implements Contracts\Form, \ArrayAccess, \Countable, \IteratorAggregate {
	/**
	 * The form config instance.
	 *
	 * @var Form_Config
	 */
	protected $config;

	/**
	 * The http request implementation.
	 *
	 * @var \WPLibs\Http\Request
	 */
	protected $request;

	/**
	 * The session store implementation.
	 *
	 * @var \WPLibs\Session\Store
	 */
	protected $session;

	/**
	 * Store the fields.
	 *
	 * @var \Illuminate\Support\Collection|\WPLibs\Form\Contracts\Field[]
	 */
	protected $fields;

	/**
	 * The form data.
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * The submitted data.
	 *
	 * @var array
	 */
	protected $submitted_data = [];

	/**
	 * The errors of this form.
	 *
	 * @var \WP_Error|null
	 */
	protected $errors;

	/**
	 * Whether this form was submitted.
	 *
	 * @var bool
	 */
	protected $submitted = false;

	/**
	 * Whether this form has been initialized.
	 *
	 * @var bool
	 */
	protected $initialized = false;

	/**
	 * Fields will be skipped in the process.
	 *
	 * @var array
	 */
	protected $skip_proccess_fields = [];

	/**
	 * The field types will be skipped in the process.
	 *
	 * @var array
	 */
	protected static $skip_proccess_types = [
		'title',
	];

	/**
	 * Whether the registry was initialized.
	 *
	 * @var bool
	 */
	protected static $registry_initialized = false;

	/**
	 * Constructor.
	 *
	 * @param Form_Config $config
	 */
	public function __construct( Form_Config $config ) {
		$this->config = $config;
		$this->fields = new Collection;

		if ( $request = $this->config->get_request() ) {
			$this->request = $request;
		}

		if ( $session = $this->config->get_session_store() ) {
			$this->session = $session;
		}
	}

	/**
	 * Clone fields when the form is clone.
	 *
	 * @return void
	 */
	public function __clone() {
		$this->fields = clone $this->fields;

		foreach ( $this->fields as $key => $field ) {
			$this->fields[ $key ] = clone $field;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_id() {
		return $this->config->get_id();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_config() {
		return $this->config;
	}

	/**
	 * Get the session store implementation.
	 *
	 * @return \WPLibs\Session\Store
	 */
	public function get_session() {
		return $this->session;
	}

	/**
	 * Set the HTTP request instance.
	 *
	 * @return \WPLibs\Http\Request|null
	 */
	public function get_request() {
		return $this->request;
	}

	/**
	 * {@inheritdoc}
	 */
	public function initialize() {
		static::initialize_registry();

		$this->prepare_form_data();

		$this->make_fields_validation();

		Dependency::check( $this->fields->all(), $this->data );

		if ( $error_name = $this->config->get_option( 'resolve_errors' ) ) {
			$this->resolve_flash_errors( $error_name );
		}

		if ( $this->config->get_option( 'old_values' ) ) {
			$this->fill_old_values();
		}

		$this->initialized = true;
	}

	/**
	 * Initialize the custom field types.
	 *
	 * @return void
	 */
	public static function initialize_registry() {
		if ( ! static::$registry_initialized ) {
			Registry::initialize();
		}

		static::$registry_initialized = true;
	}

	/**
	 * Prepare data from the storage.
	 *
	 * @return void
	 */
	protected function prepare_form_data() {
		$this->data = [];

		foreach ( $this->fields as $field ) {
			$this->data[ $field->get_id() ] = $field->get_value();
		}
	}

	/**
	 * HTML5 validation attributes based on "validate".
	 *
	 * @return void
	 */
	protected function make_fields_validation() {
		foreach ( $this->fields as $key => $field ) {
			if ( $this->maybe_skip_field( $field ) ) {
				continue;
			}

			if ( $rules = $field->get_option( 'validate' ) ) {
				$attributes = ( new Rules_Parser( $field ) )->parse( $rules );
				$field->set_attribute( $attributes );
			}
		}
	}

	/**
	 * Resolve flash errors for the display.
	 *
	 * @param string $name
	 */
	protected function resolve_flash_errors( $name ) {
		if ( ! $this->session ) {
			return;
		}

		if ( ! $this->session->get( $name ) instanceof WP_Error ) {
			return;
		}

		$this->errors = new WP_Error;

		/* @var $errors \WP_Error */
		$errors = $this->session->get( $name );

		foreach ( $errors->errors as $key => $error ) {
			$this->add_error( $key, $error );
		}
	}

	/**
	 * Fill old input values.
	 *
	 * @return void
	 */
	protected function fill_old_values() {
		if ( ! $this->session ) {
			return;
		}

		if ( 0 === count( (array) $this->session->get_old_input() ) ) {
			return;
		}

		foreach ( $this->fields as $key => $field ) {
			if ( ! is_null( $old = $this->session->get_old_input( $key ) ) ) {
				$field->set_value( $old );
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function save( array $merge_data = [] ) {
		if ( ! $this->submitted ) {
			// @codingStandardsIgnoreLine
			_doing_it_wrong( get_class( $this ) . '::' . __FUNCTION__, 'Save form can only works after the form is submitted.', '1.0.0' );

			return $this;
		}

		$data = array_merge( $this->submitted_data, $merge_data );

		/* @var $deferred_storages \WPLibs\Form\Contracts\Deferred_Storage[] */
		$deferred_storages = [];

		foreach ( $data as $key => $_data ) {
			if ( ! $this->has( $key ) || $this->has_error( $key ) ) {
				continue;
			}

			$field = $this->get( $key );
			if ( $this->maybe_skip_field( $field ) ) {
				continue;
			}

			$storage = $field->get_storage();

			if ( Utils::is_not_blank( $_data ) ) {
				$storage->set( $field->get_map_key(), $_data );
			} else {
				$storage->delete( $field->get_map_key() );
			}

			if ( $storage instanceof Deferred_Storage &&
				 ! array_key_exists( $_hash = spl_object_hash( $storage ), $deferred_storages ) ) {
				$deferred_storages[ $_hash ] = $storage;
			}
		}

		// Peform save the deferred_storages.
		foreach ( $deferred_storages as $storage ) {
			$storage->save();
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function submit( $data, $clear_missing = false ) {
		if ( $this->submitted ) {
			// @codingStandardsIgnoreLine
			_doing_it_wrong( get_class( $this ) . '::' . __FUNCTION__, 'A form can only be submitted once', '1.0.0' );

			return $this;
		}

		// Prepare data for the submission.
		$this->submitted_data = $this->prepare_submitted_data(
			$this->fields, $data, $clear_missing
		);

		// Initialize errors in the very beginning so that we don't lose any
		// errors added during listeners.
		$this->errors = new WP_Error;

		$this->validate( $this->submitted_data );

		// Remove submitted data have any errors.
		if ( count( $errors = $this->errors->errors ) > 0 ) {
			Arr::forget( $this->submitted_data, array_keys( $errors ) );
		}

		$this->submitted = true;

		return $this;
	}

	/**
	 * Returns submitted data from input.
	 *
	 * @param  array $fields
	 * @param  array $data
	 * @param  bool  $clear_missing
	 * @return array
	 */
	protected function prepare_submitted_data( $fields, array $data, $clear_missing ) {
		$submitted = [];

		foreach ( $fields as $field ) {
			if ( $this->maybe_skip_field( $field ) ) {
				continue;
			}

			$value = array_key_exists( $field->get_id(), $data )
				? $data[ $field->get_id() ]
				: null;

			// Treat false as NULL to support binding false to checkboxes.
			// Don't convert NULL to a string here in order to determine later
			// whether an empty value has been submitted or whether no value has
			// been submitted at all. This is important for processing checkboxes
			// and radio buttons with empty values.
			if ( false === $value ) {
				$value = null;
			} elseif ( is_scalar( $value ) ) {
				$value = (string) $value;
			}

			// Perform the sanitization to get sanitized value.
			$sanitized = $field->get_sanitization_value( $value );

			if ( $field->is_group() ) {
				$submitted[ $field->get_id() ] = $this->get_group_values( $field, $sanitized );
			} else {
				$submitted[ $field->get_id() ] = $sanitized;
			}
		}

		// Clear "null" input data.
		if ( $clear_missing ) {
			$submitted = array_filter( $submitted, function ( $value ) {
				return ! is_null( $value );
			} );
		}

		return $submitted;
	}

	/**
	 * Gets values of a group with given data.
	 *
	 * @param  Field_Contract $group
	 * @param  array          $data
	 * @return array|null
	 */
	protected function get_group_values( Field_Contract $group, $data ) {
		if ( ! $group->is_group() || ! $group->get_option( 'fields' ) ) {
			return null;
		}

		if ( ! is_array( $data ) ) {
			return null;
		}

		// We will working on clone of the $group, to prevent touching
		// on real object.
		$group = clone $group;

		$i = 0;
		$values = [];

		foreach ( array_values( $data ) as $_data ) {
			if ( ! is_array( $_data ) || empty( $_data ) ) {
				continue;
			}

			$group->index = $i;

			$fields = array_map( function ( $args ) use ( $group ) {
				return $this->config->get_field_in_group( $group, $args['id'] );
			}, $group->get_option( 'fields' ) );

			$values[ $i ] = Utils::filter_blank(
				$this->prepare_submitted_data( $fields, $_data, true )
			);

			$i++;
		}

		return array_filter( $values );
	}

	/**
	 * Check if given field is skipped in the process.
	 *
	 * @param  Field_Contract $field
	 * @return bool
	 */
	protected function maybe_skip_field( Field_Contract $field ) {
		// Don't process fields in $skip_proccess_types.
		if ( in_array( $field->get_type(), static::$skip_proccess_types ) ) {
			return true;
		}

		if ( $field->is_disabled() || false === $field->get_option( 'save_field', true ) ) {
			return true;
		}

		$id = $field->get_id();

		if ( $group = $field->get_group() ) {
			$id = $group->get_id() . '.' . $id;
		}

		if ( in_array( $id, $this->skip_proccess_fields ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Validate the form data.
	 *
	 * @param array $data
	 */
	public function validate( array $data ) {
		// Validate use Validator.
		try {
			$this->validate_use_validator( $data, $this->get_validate_rules() );
		} catch ( \Exception $e ) {
			trigger_error( $e->getMessage(), E_USER_WARNING ); // @WPCS: XSS OK.
		}

		// Validate use custom callback.
		$callbacks = $this->fields->pluck( 'validate_cb', 'id' )->filter( 'is_callable' );

		if ( $callbacks->isNotEmpty() ) {
			$this->validate_by_callbacks( $data, $callbacks->all() );
		}
	}

	/**
	 * Returns the validator rules.
	 *
	 * @return array
	 */
	protected function get_validate_rules() {
		$rules = [];

		foreach ( $this->fields as $key => $field ) {
			// TODO: Group field cannot validate at moment.
			if ( 'group' === $field->get_type() ) {
				continue;
			}

			if ( $rule = $field->get_option( 'validate' ) ) {
				$key           = $field->is_repeatable() ? $key . '.*' : $key;
				$rules[ $key ] = $rule;
			}
		}

		return $rules;
	}

	/**
	 * Validate fields use "validate" parameter.
	 *
	 * @param array $data
	 * @param array $rules
	 */
	protected function validate_use_validator( array $data, array $rules ) {
		if ( empty( $rules ) ) {
			return;
		}

		$validator = new Validator( $data, $rules );

		$validator->labels(
			$this->fields->pluck( 'name', 'id' )->all()
		);

		if ( $validator->fails() ) {
			foreach ( $rules as $key => $rule ) {
				$this->add_error( $key, $validator->errors( $key ) );
			}
		}
	}

	/**
	 * Validate fields use "validate_cb" parameter.
	 *
	 * @param array $data
	 * @param array $callbacks
	 */
	protected function validate_by_callbacks( array $data, array $callbacks ) {
		foreach ( $callbacks as $key => $callback ) {
			$validity = new WP_Error;

			$value = array_key_exists( $key, $data ) ? $data[ $key ] : null;
			$callback( $validity, $value );

			if ( is_wp_error( $validity ) && count( $validity->errors ) > 0 ) {
				$this->add_error( $key, $validity->get_error_messages() );
			}
		}
	}

	/**
	 * Add an error message into the $errors.
	 *
	 * @param string $key
	 * @param array  $_errors
	 */
	public function add_error( $key, $_errors ) {
		if ( ! $_errors || ! $this->errors instanceof WP_Error ) {
			return;
		}

		if ( ! $this->fields->has( $key ) ) {
			return;
		}

		foreach ( is_array( $_errors ) ? $_errors : [ $_errors ] as $message ) {
			$this->errors->add( $key, $message );
		}
	}

	/**
	 * Determnies if given field has any errors.
	 *
	 * @param  string $key
	 * @return bool
	 */
	public function has_error( $key ) {
		return $this->errors
				&& array_key_exists( $key, $this->errors->errors )
				&& count( $this->errors->errors[ $key ] ) > 0;
	}

	/**
	 * Returns field error(s) if any.
	 *
	 * @param  string $key
	 * @param  bool   $all
	 * @return string|array
	 */
	public function get_error( $key, $all = true ) {
		if ( ! $this->has_error( $key ) ) {
			return null;
		}

		$errors = $this->errors->errors[ $key ];

		return $all ? Arr::first( $errors ) : $errors;
	}

	/**
	 * Gets the form errors.
	 *
	 * @return \WP_Error|null
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * {@inheritdoc}
	 */
	public function show( $field ) {
		$field = ( ! $field instanceof Field_Contract ) ? $this->get( $field ) : $field;

		if ( ! $field ) {
			trigger_error( 'Cannot display the field', E_USER_WARNING );
			return;
		}

		// Inherit show_names property.
		if ( ! $this->config->get_option( 'show_names' ) ) {
			$field->set_option( 'show_names', false );
		}

		$field->display();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_data() {
		return new Values( $this->data );
	}

	/**
	 * Gets the submitted data.
	 *
	 * @return \WPLibs\Form\Helper\Values
	 */
	public function get_submitted_data() {
		return new Values( $this->submitted_data );
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_data( array $data ) {
		if ( $this->submitted ) {
			// @codingStandardsIgnoreLine
			_doing_it_wrong( get_class( $this ) . '::' .  __FUNCTION__, 'You cannot change the data of a submitted form.', '1.0.0' );

			return $this;
		}

		$this->data = array_merge( $this->data, $data );

		foreach ( $data as $key => $value ) {
			if ( ! $this->has( $key ) ) {
				continue;
			}

			$this->fields[ $key ]->set_value( $value );
		}

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_valid() {
		if ( ! $this->submitted ) {
			return false;
		}

		return ! $this->errors || 0 === count( $this->errors->errors );
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_empty() {
		return $this->fields->isEmpty();
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_submitted() {
		return $this->submitted;
	}

	/**
	 * {@inheritdoc}
	 */
	public function add( Field_Contract $field ) {
		if ( $this->submitted ) {
			// @codingStandardsIgnoreLine
			_doing_it_wrong( get_class( $this ) . '::' .  __FUNCTION__, 'You cannot add fields to a submitted form', '1.0.0' );

			return $this;
		}

		// Prevent add field doesn't have right permission.
		if ( ! $field->check_capabilities() ) {
			return $this;
		}

		$field->set_form( $this );
		$this->fields[ $field->get_id() ] = $field;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function has( $name ) {
		return $this->fields->has( $name );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get( $name ) {
		return $this->fields->get( $name );
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove( $name ) {
		if ( $this->submitted ) {
			// @codingStandardsIgnoreLine
			_doing_it_wrong( get_class( $this ) . '::' .  __FUNCTION__, 'You cannot remove fields from a submitted form', '1.0.0' );

			return $this;
		}

		if ( isset( $this->fields[ $name ] ) ) {
			unset( $this->fields[ $name ] );
		}

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function all() {
		return $this->fields->all();
	}

	/**
	 * Returns the number of form fields
	 *
	 * @return int
	 */
	public function count() {
		return count( $this->fields );
	}

	/**
	 * Returns the iterator for this group.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator() {
		return $this->fields->getIterator();
	}

	/**
	 * Returns whether a field with the given name exists.
	 *
	 * @param  string $name
	 * @return bool
	 */
	public function offsetExists( $name ) {
		return $this->has( $name );
	}

	/**
	 * Returns the field with the given name.
	 *
	 * @param  string $name
	 * @return \WPLibs\Form\Contracts\Form|null
	 */
	public function offsetGet( $name ) {
		return $this->get( $name );
	}

	/**
	 * Adds a field to the form.
	 *
	 * @param string                       $name
	 * @param \WPLibs\Form\Contracts\Field $field
	 */
	public function offsetSet( $name, $field ) {
		$this->add( $field );
	}

	/**
	 * Removes the field with the given name from the form.
	 *
	 * @param string $name
	 */
	public function offsetUnset( $name ) {
		$this->remove( $name );
	}
}
