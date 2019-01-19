<?php

namespace WPLibs\Form\Helper;

use Illuminate\Support\Arr;

class Values implements \ArrayAccess, \Countable, \JsonSerializable {
	/**
	 * An array of the data.
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * Constructor.
	 *
	 * @param array $data
	 */
	public function __construct( array $data ) {
		$this->data = $data;
	}

	/**
	 * Gets a value of a given key.
	 *
	 * @param string $key
	 * @param mixed  $default
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		return Arr::get( $this->data, $key, $default );
	}

	/**
	 * Sets a key/value.
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	public function set( $key, $value ) {
		Arr::set( $this->data, $key, $value );
	}

	/**
	 * Gets all data.
	 *
	 * @return array
	 */
	public function all() {
		return $this->data;
	}

	/**
	 * Count the number of attributes.
	 *
	 * @return int
	 */
	public function count() {
		return count( $this->data );
	}

	/**
	 * Convert the object into something JSON serializable.
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return $this->data;
	}

	/**
	 * Determine if the given offset exists.
	 *
	 * @param  string $offset The offset key.
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		return Arr::exists( $this->data, $offset );
	}

	/**
	 * Get the value for a given offset.
	 *
	 * @param  string $offset The offset key.
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		return $this->get( $offset );
	}

	/**
	 * Set the value at the given offset.
	 *
	 * @param  string $offset The offset key.
	 * @param  mixed  $value  The offset value.
	 * @return void
	 */
	public function offsetSet( $offset, $value ) {
		$this->set( $offset, $value );
	}

	/**
	 * Unset the value at the given offset.
	 *
	 * @param  string $offset The offset key.
	 * @return void
	 */
	public function offsetUnset( $offset ) {
		unset( $this->data[ $offset ] );
	}

	/**
	 * Dynamically retrieve the value of an attribute.
	 *
	 * @param  string $key The key name.
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->get( $key );
	}

	/**
	 * Dynamically set the value of an attribute.
	 *
	 * @param  string $key   The key name.
	 * @param  mixed  $value The key value.
	 * @return void
	 */
	public function __set( $key, $value ) {
		$this->offsetSet( $key, $value );
	}

	/**
	 * Dynamically check if an attribute is set.
	 *
	 * @param  string $key The key name.
	 * @return bool
	 */
	public function __isset( $key ) {
		return $this->offsetExists( $key );
	}

	/**
	 * Dynamically unset an attribute.
	 *
	 * @param  string $key The key name.
	 * @return void
	 */
	public function __unset( $key ) {
		$this->offsetUnset( $key );
	}
}
