<?php

namespace WPLibs\Form\Contracts;

interface Deferred_Storage extends Storage {
	/**
	 * Read data from storage.
	 *
	 * @return mixed
	 */
	public function read();

	/**
	 * Save data to the storage.
	 *
	 * @return bool
	 */
	public function save();
}
