<?php

namespace WPLibs\Form\Contracts;

interface Storage {
	/**
	 * Get an item in the storage.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function get( $key, $default = null );

	/**
	 * Set a item in the storage.
	 *
	 * @param  string $key
	 * @param  mixed  $value
	 * @return bool
	 */
	public function set( $key, $value );

	/**
	 * Delete an item in the storage.
	 *
	 * @param string $key
	 * @return bool
	 */
	public function delete( $key );
}
