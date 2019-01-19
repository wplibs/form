<?php

namespace WPLibs\Form\Contracts;

interface Form {
	/**
	 * Initializes the form tree.
	 *
	 * Should be called on the root form after constructing the form.
	 *
	 * @return $this
	 */
	public function initialize();

	/**
	 * Returns the name by which the form is identified in forms.
	 *
	 * @return string The name of the form
	 */
	public function get_id();

	/**
	 * Returns the form's configuration.
	 *
	 * @return \WPLibs\Form\Contracts\Form_Config
	 */
	public function get_config();

	/**
	 * Returns the data in the format needed for the underlying object.
	 *
	 * @return mixed
	 */
	public function get_data();

	/**
	 * Updates the form with default data.
	 *
	 * @param  array $data
	 * @return $this
	 */
	public function set_data( array $data );

	/**
	 * Submits data to the form, transforms and validates it.
	 *
	 * @param mixed $data          The submitted data.
	 * @param bool  $clear_missing Whether to set fields to NULL when they
	 *                             are missing in the submitted data.
	 * @return array
	 */
	public function submit( $data, $clear_missing = false );

	/**
	 * Handler save form data into the store.
	 *
	 * @param  array $merge_data
	 * @return bool
	 */
	public function save( array $merge_data = [] );

	/**
	 * Display a field in the form.
	 *
	 * @param \WPLibs\Form\Contracts\Field|string $field
	 */
	public function show( $field );

	/**
	 * Returns whether the form and all fields are valid.
	 *
	 * If the form is not submitted, this method always returns false.
	 *
	 * @return bool
	 */
	public function is_valid();

	/**
	 * Returns whether the form is empty.
	 *
	 * @return bool
	 */
	public function is_empty();

	/**
	 * Returns whether the form is submitted.
	 *
	 * @return bool true if the form is submitted, false otherwise
	 */
	public function is_submitted();

	/**
	 * Adds or replaces a field to the form.
	 *
	 * @param \WPLibs\Form\Contracts\Field $field
	 * @return $this
	 */
	public function add( Field $field );

	/**
	 * Returns the field with the given name.
	 *
	 * @param  string $name
	 * @return \WPLibs\Form\Contracts\Field|null
	 */
	public function get( $name );

	/**
	 * Returns whether a field with the given name exists.
	 *
	 * @param  string $name
	 * @return bool
	 */
	public function has( $name );

	/**
	 * Removes a field from the form.
	 *
	 * @param  string $name
	 * @return $this
	 */
	public function remove( $name );

	/**
	 * Returns all field in this form.
	 *
	 * @return \WPLibs\Form\Contracts\Field[]
	 */
	public function all();
}
