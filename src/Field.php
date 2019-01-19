<?php

namespace WPLibs\Form;

use WPLibs\Form\Helper\Utils;
use WPLibs\Form\Contracts\Storage;
use WPLibs\Form\Contracts\Form_Builder;
use WPLibs\Form\Storages\Option_Storage;
use WPLibs\Form\Storages\Theme_Mod_Storage;
use WPLibs\Form\Contracts\Deferred_Storage;

class Field extends \CMB2_Field implements Contracts\Field, \ArrayAccess {
	use Concerns\CMB2_Field,
		Concerns\Field_Builder;

	/**
	 * The form builder implementation.
	 *
	 * @var \WPLibs\Form\Contracts\Form_Builder|\WPLibs\Form\Builder
	 */
	protected $builder;

	/**
	 * Store the field type instance.
	 *
	 * @var \WPLibs\Form\Contracts\Field_Type|null
	 */
	protected $field_type;

	/**
	 * Store the storage was read.
	 *
	 * @var array
	 */
	protected static $storages_was_read = [];

	/**
	 * Constructor.
	 *
	 * @param \WPLibs\Form\Contracts\Form_Builder $builder
	 * @param array                               $args
	 */
	public function __construct( Form_Builder $builder, $args ) {
		parent::__construct( $args );

		$this->builder = $builder;

		if ( $this->group ) {
			$this->set_group( $this->group );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function initialize() {
		$types = Registry::get_field_types();

		if ( array_key_exists( $type = $this->get_type(), $types ) ) {
			$this->field_type = $types[ $type ];
		}

		if ( ! $this->group ) {
			$this->setup_storage();
		}

		if ( ! $this->storage ) {
			$this->set_storage( $this->builder->get_storage() );
		}

		$this->setup_data();

		return $this;
	}

	/**
	 * Setup the field storage.
	 *
	 * @retun void
	 */
	protected function setup_storage() {
		$storage = $this->get_option( 'storage' );

		if ( 'option' === $storage ) {
			$storage = Option_Storage::get_instance();
		} elseif ( 'thememod' === $storage ) {
			$storage = Theme_Mod_Storage::get_instance();
		} elseif ( is_string( $storage ) && is_subclass_of( $storage, Storage::class ) ) {
			$storage = new $storage;
		} elseif ( is_callable( $storage ) ) {
			$storage = $storage( $this );
		}

		if ( $storage instanceof Storage ) {
			$this->set_storage( $storage );
		}
	}

	/**
	 * Setup field data from the storage.
	 *
	 * @return void
	 */
	protected function setup_data() {
		if ( ! $storage = $this->get_storage() ) {
			return;
		}

		$_hash = spl_object_hash( $storage );

		if ( $storage instanceof Deferred_Storage && ! isset( static::$storages_was_read[ $_hash ] ) ) {
			$storage->read();
			static::$storages_was_read[ $_hash ] = true;
		}

		$this->set_value(
			$storage->get( $this->get_map_key() )
		);
	}

	/**
	 * Display the field.
	 *
	 * @return void
	 */
	public function display() {
		$this->register_js_data();

		$this->render_field();
	}

	/**
	 * Render the field input.
	 *
	 * @retun void
	 */
	public function render() {
		$this->peform_param_callback( 'before' );

		( new Field_Render( $this ) )->render();

		$this->peform_param_callback( 'after' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_id() {
		return $this->id( true );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_type() {
		return $this->get_option( 'type' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_map_key() {
		return $this->get_option( 'map_key' ) ?: $this->get_id();
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_group() {
		return 'group' === $this->get_type();
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_repeatable() {
		return (bool) $this->get_option( 'repeatable' ) && ! $this->is_group();
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_required() {
		if ( $this->group && $this->group->is_required() ) {
			return true;
		}

		return $this->get_option( 'required' ) || $this->get_attribute( 'required' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_disabled() {
		if ( $this->group && $this->group->is_disabled() ) {
			return true;
		}

		return $this->get_option( 'disabled' ) || $this->get_attribute( 'disabled' );
	}

	/**
	 * Gets the field value.
	 *
	 * @return mixed
	 */
	public function get_value() {
		if ( $this->group && null === $this->value ) {
			return $this->value = $this->get_value_in_group();
		}

		return $this->value;
	}

	/**
	 * Sets the field value.
	 *
	 * @param  mixed $value
	 * @return $this
	 */
	public function set_value( $value ) {
		$this->value = $value;

		return $this;
	}

	/**
	 * Returns current value in group.
	 *
	 * @return mixed
	 */
	protected function get_value_in_group() {
		$id = $this->get_id();

		$data = $this->group->get_value();

		return is_array( $data ) && isset( $data[ $this->group->index ][ $id ] )
			? $data[ $this->group->index ][ $id ]
			: null;
	}

	/**
	 * Perform the sanitization a given value.
	 *
	 * @param  mixed $value
	 * @return mixed
	 */
	public function get_sanitization_value( $value ) {
		if ( $this->is_repeatable() && is_callable( $sanitizer = $this->get_option( 'sanitization_cb' ) ) ) {
			$sanitized = is_array( $value ) ? array_map( $sanitizer, $value ) : [];

			return Utils::filter_blank( $sanitized );
		}

		return $this->sanitization_cb( $value );
	}

	/**
	 * {@inheritdoc}
	 */
	public function active() {
		return (bool) $this->get_option( 'active', true );
	}

	/**
	 * {@inheritdoc}
	 */
	public function check_capabilities() {
		$caps = $this->get_option( 'capability' );

		if ( $caps && ! call_user_func_array( 'current_user_can', (array) $caps ) ) {
			return false;
		}

		$theme_supports = $this->get_option( 'current_theme_supports' );

		/* @noinspection IfReturnReturnSimplificationInspection */
		if ( $theme_supports && ! call_user_func_array( 'current_theme_supports', (array) $theme_supports ) ) {
			return false;
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function js_data() {
		$json = array_merge( parent::js_data(), [
			'repeatable' => $this->is_repeatable(),
			'required'   => $this->is_required(),
			'disabled'   => $this->is_disabled(),
			'active'     => $this->active(),
			'deps'       => $this->get_option( 'deps' ) ?: null,
			'value'      => $this->get_value(),
		] );

		// Merge data from the field type.
		if ( $this->field_type && method_exists( $this->field_type, 'js_data' ) ) {
			$json = array_merge( $json, (array) $this->field_type->js_data( $this ) );
		}

		return $json;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_default_field_args( $args ) {
		return array_merge( parent::get_default_field_args( $args ), [
			'deps'                   => [],
			'validate'               => [],
			'row_attributes'         => [],
			'required'               => false,
			'disabled'               => false,
			'capability'             => null,
			'current_theme_supports' => null,
		] );
	}

	/**
	 * Whether a offset exists.
	 *
	 * @param  mixed $offset
	 * @return boolean
	 */
	public function offsetExists( $offset ) {
		return $this->has_option( $offset );
	}

	/**
	 * Offset to retrieve.
	 *
	 * @param  string $offset
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		return $this->get_option( $offset );
	}

	/**
	 * Offset to set.
	 *
	 * @param  string $offset
	 * @param  mixed  $value
	 * @return void
	 */
	public function offsetSet( $offset, $value ) {
		$this->set_option( $offset, $value );
	}

	/**
	 * Offset to unset.
	 *
	 * @param  mixed $offset
	 * @return void
	 */
	public function offsetUnset( $offset ) {
		$this->set_option( $offset, null );
	}
}
