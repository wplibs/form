<?php

namespace WPLibs\Form\Storages;

use WPLibs\Form\Helper\Values;
use WPLibs\Form\Contracts\Storage;
use Illuminate\Support\Collection;

class Array_Storage implements Storage {
	/**
	 * An array of data.
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Constructor.
	 *
	 * @param array $data
	 */
	public function __construct( $data ) {
		if ( $data instanceof Collection || $data instanceof Values ) {
			$data = $data->all();
		} elseif ( $data instanceof \Traversable ) {
			$data = iterator_to_array( $data );
		}

		$this->data = (array) $data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get( $key, $default = null ) {
		return array_key_exists( $key, $this->data ) ? $this->data[ $key ] : $default;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set( $key, $value ) {
		$this->data[ $key ] = $value;

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete( $key ) {
		unset( $this->data[ $key ] );

		return true;
	}

	/**
	 * Return all data.
	 *
	 * @return array
	 */
	public function all() {
		return $this->data;
	}
}
