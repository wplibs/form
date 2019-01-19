<?php

namespace WPLibs\Form\Contracts;

interface Form_Config {
	/**
	 * Returns the ID of the form.
	 *
	 * @return string
	 */
	public function get_id();

	/**
	 * Get the storage.
	 *
	 * @return \WPLibs\Form\Contracts\Storage
	 */
	public function get_storage();

	/**
	 * Set the HTTP request instance.
	 *
	 * @return \WPLibs\Http\Request|null
	 */
	public function get_request();

	/**
	 * Get the session store implementation.
	 *
	 * @return \WPLibs\Session\Store
	 */
	public function get_session_store();

	/**
	 * Returns whether the form should be initialized upon creation.
	 *
	 * @return bool
	 */
	public function get_auto_initialize();

	/**
	 * Return a list skip fill types.
	 *
	 * @return array
	 */
	public function get_skip_fill_types();

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
	 * @return $this
	 */
	public function get_option( $name, $default = null );

	/**
	 * Builds and returns the form configuration.
	 *
	 * @return \WPLibs\Form\Contracts\Form_Config
	 */
	public function get_form_config();
}
