<?php

namespace WPLibs\Form\Storages;

use WPLibs\Form\Contracts\Storage;

class Theme_Mod_Storage implements Storage {
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
		return get_theme_mod( $key, $default );
	}

	/**
	 * {@inheritdoc}
	 */
	public function set( $key, $value ) {
		return (bool) set_theme_mod( $key, $value );
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete( $key ) {
		return (bool) remove_theme_mod( $key );
	}

	/**
	 * {@inheritdoc}
	 */
	public function data() {
		return get_theme_mods();
	}
}
