<?php

namespace WPLibs\Form\Storages;

use WPLibs\Form\Contracts\Storage;

class Metadata_Storage implements Storage {
	/**
	 * ID of the object metadata is for.
	 *
	 * @var int
	 */
	protected $object_id;

	/**
	 * Type of object metadata is for (e.g., comment, post, or user)
	 *
	 * @var string
	 */
	protected $meta_type;

	/**
	 * Constructor.
	 *
	 * @param int    $object_id
	 * @param string $meta_type
	 */
	public function __construct( $object_id, $meta_type = 'post' ) {
		$this->object_id = $object_id;
		$this->meta_type = $meta_type;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get( $key, $default = null ) {
		$metadata = get_metadata( $this->meta_type, $this->object_id, $key, true );

		return false !== $metadata ? $metadata : $default;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set( $key, $value ) {
		return (bool) update_metadata( $this->meta_type, $this->object_id, $key, $value );
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete( $key ) {
		return delete_metadata( $this->meta_type, $this->object_id, $key );
	}
}
