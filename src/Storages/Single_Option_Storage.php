<?php

namespace WPLibs\Form\Storages;

use WPLibs\Form\Contracts\Deferred_Storage;

class Single_Option_Storage extends Array_Storage implements Deferred_Storage {
	/**
	 * Name of option.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Whether to load the option when WordPress starts up.
	 *
	 * @var bool
	 */
	protected $autoload;

	/**
	 * Constructor.
	 *
	 * @param string $name
	 * @param bool   $autoload
	 */
	public function __construct( $name, $autoload = true ) {
		$this->name     = $name;
		$this->autoload = $autoload;
	}

	/**
	 * Read data from storage.
	 *
	 * @return void
	 */
	public function read() {
		$data = get_option( $this->name, [] );

		$this->data = is_array( $data ) ? $data : [];
	}

	/**
	 * Save data to the storage.
	 *
	 * @return bool
	 */
	public function save() {
		if ( empty( $this->data ) ) {
			return delete_option( $this->name );
		}

		$updated = update_option( $this->name, $this->data, $this->autoload );

		// Reload the current data.
		$this->read();

		return $updated;
	}
}
