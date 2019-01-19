<?php

namespace WPLibs\Form\Storages;

use WPLibs\Form\Contracts\Storage;

class Option_Storage implements Storage {
	/**
	 * Store the class instance.
	 *
	 * @var static
	 */
	protected static $instance;

	/**
	 * Gets the storage instance.
	 *
	 * @return static
	 */
	public static function get_instance() {
		if ( ! static::$instance ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get( $key, $default = null ) {
		return get_option( $key, $default );
	}

	/**
	 * {@inheritdoc}
	 */
	public function set( $key, $value ) {
		return update_option( $key, $value );
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete( $key ) {
		return delete_option( $key );
	}
}
