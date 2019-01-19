<?php

namespace WPLibs\Form\Contracts;

interface Field {
	/**
	 * Initializes the field.
	 *
	 * @return $this
	 */
	public function initialize();

	/**
	 * Display the field.
	 *
	 * @return void
	 */
	public function display();

	/**
	 * Returns the field ID.
	 *
	 * @return string The name of the form
	 */
	public function get_id();

	/**
	 * Returns the field type.
	 *
	 * @return string
	 */
	public function get_type();

	/**
	 * Returns the key for store in the storage.
	 *
	 * @return mixed
	 */
	public function get_map_key();

	/**
	 * Gets the form builder instance.
	 *
	 * @return \WPLibs\Form\Contracts\Form
	 */
	public function get_form();

	/**
	 * Sets the form builer instance.
	 *
	 * @param \WPLibs\Form\Contracts\Form $form
	 * @return $this
	 */
	public function set_form( Form $form );

	/**
	 * Returns the group field instance.
	 *
	 * @return \WPLibs\Form\Contracts\Field|null
	 */
	public function get_group();

	/**
	 * Sets the group field instance.
	 *
	 * @param \WPLibs\Form\Contracts\Field $group
	 * @return $this
	 */
	public function set_group( Field $group );

	/**
	 * Returns the storage instance.
	 *
	 * @return \WPLibs\Form\Contracts\Storage
	 */
	public function get_storage();

	/**
	 * Sets the storage implementation.
	 *
	 * @param \WPLibs\Form\Contracts\Storage $storage
	 * @return $this
	 */
	public function set_storage( Storage $storage );

	/**
	 * Returns whether the field is group field.
	 *
	 * @return bool
	 */
	public function is_group();

	/**
	 * Returns whether the field is repeatable.
	 *
	 * @return bool
	 */
	public function is_repeatable();

	/**
	 * Returns whether the field is required to be filled out.
	 *
	 * If the field has a parent and the parent is not required, this method
	 * will always return false.
	 *
	 * @return bool
	 */
	public function is_required();

	/**
	 * Returns whether this field is disabled.
	 *
	 * The content of a disabled form is displayed, but not allowed to be
	 * modified. The validation of modified disabled forms should fail.
	 *
	 * Forms whose parents are disabled are considered disabled regardless of
	 * their own state.
	 *
	 * @return bool
	 */
	public function is_disabled();

	/**
	 * Gets the field attributes.
	 *
	 * @return array
	 */
	public function get_attributes();

	/**
	 * Gets the field attribute.
	 *
	 * @param  string $key     The attribute key.
	 * @param  mixed  $default Default value will be return if key not exists.
	 * @return mixed
	 */
	public function get_attribute( $key, $default = null );

	/**
	 * Sets the field attribute.
	 *
	 * @param  string|array $attribute Attribute key name.
	 * @param  string       $value     Attribute value.
	 * @return $this
	 */
	public function set_attribute( $attribute, $value = '' );

	/**
	 * Gets the field value.
	 *
	 * @return mixed
	 */
	public function get_value();

	/**
	 * Sets the field value.
	 *
	 * @param  mixed $value
	 * @return $this
	 */
	public function set_value( $value );

	/**
	 * Returns all options passed during the construction of the form.
	 *
	 * @return array The passed options
	 */
	public function get_options();

	/**
	 * Returns whether a specific option exists.
	 *
	 * @param  string $name The option name.
	 * @return bool
	 */
	public function has_option( $name );

	/**
	 * Returns the value of a specific option.
	 *
	 * @param  string $name    The option name.
	 * @param  mixed  $default The value returned if the option does not exist.
	 * @return mixed
	 */
	public function get_option( $name, $default = null );

	/**
	 * Sets the value of a specific option.
	 *
	 * @param  string $name  The option name.
	 * @param  mixed  $value The option value.
	 * @return $this
	 */
	public function set_option( $name, $value );

	/**
	 * Check whether field is active to current screen.
	 *
	 * @return bool
	 */
	public function active();

	/**
	 * Checks required user capabilities and whether the theme has the
	 * feature support required by the field.
	 *
	 * @return bool
	 */
	public function check_capabilities();
}
