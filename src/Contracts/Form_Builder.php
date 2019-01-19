<?php

namespace WPLibs\Form\Contracts;

use WPLibs\Http\Request;
use WPLibs\Session\Store;

interface Form_Builder extends \Traversable, \Countable, Form_Config, Fields_Stack {
	/**
	 * Sets the storage.
	 *
	 * @param \WPLibs\Form\Contracts\Storage $storage
	 * @return $this
	 */
	public function set_storage( Storage $storage );

	/**
	 * Get the HTTP request instance.
	 *
	 * @param \WPLibs\Http\Request $request
	 * @return $this
	 */
	public function set_request( Request $request );

	/**
	 * Set the session store implementation.
	 *
	 * @param \WPLibs\Session\Store $session
	 * @return $this
	 */
	public function set_session_store( Store $session );

	/**
	 * Sets whether the form should be initialized automatically.
	 *
	 * @param  bool $initialize
	 * @return $this
	 */
	public function auto_initialize( $initialize );

	/**
	 * Sets skip fill values on types.
	 *
	 * @param  array $types
	 * @return $this
	 */
	public function skip_fill_types( $types );

	/**
	 * Sets the value of a specific option.
	 *
	 * @param  string $name  The option name.
	 * @param  mixed  $value The option value.
	 * @return $this
	 */
	public function set_option( $name, $value );

	/**
	 * Creates the form.
	 *
	 * @return \WPLibs\Form\Contracts\Form
	 */
	public function get_form();
}
