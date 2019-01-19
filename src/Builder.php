<?php

namespace WPLibs\Form;

use WPLibs\Form\Contracts\Storage;
use WPLibs\Form\Storages\Array_Storage;

class Builder extends \CMB2 implements \ArrayAccess, \IteratorAggregate, Contracts\Form_Builder {
	use Concerns\CMB2_Form,
		Concerns\Form_Builder;

	/**
	 * Constructor.
	 *
	 * @param string                               $name
	 * @param array                                $config
	 * @param \WPLibs\Form\Contracts\Storage|array $storage
	 */
	public function __construct( $name, $config = [], $storage = null ) {
		if ( ! $storage instanceof Storage ) {
			$storage = new Array_Storage( (array) $storage );
		}

		$this->cmb_id  = $name;
		$this->storage = $storage;

		$this->meta_box = array_merge( $this->mb_defaults, $config + [
			'id'     => $this->cmb_id,
			'fields' => [],
		] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_id() {
		return $this->cmb_id;
	}

	/**
	 * Creates the form.
	 *
	 * @return \WPLibs\Form\Form
	 */
	public function get_form() {
		$this->throws_if_locked( 'access' );

		$form = new Form( $this->get_form_config() );

		// Create the field object instance.
		foreach ( $this->prop( 'fields' ) as $args ) {
			$form->add( $this->get_field( $args )->initialize() );
		}

		// Automatically initialize the form if it is configured so.
		if ( $this->get_auto_initialize() ) {
			$form->initialize();
		}

		return $form;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_form_config() {
		$this->throws_if_locked( 'access' );

		$config = clone $this;
		$config->locked = true;

		return $config;
	}

	/**
	 * {@inheritdoc}
	 */
	public function count() {
		return count( $this->prop( 'fields' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator() {
		return new \ArrayIterator( $this->prop( 'fields' ) );
	}

	/**
	 * Determine if an field exists.
	 *
	 * @param  mixed $key The field key ID.
	 * @return bool
	 */
	public function offsetExists( $key ) {
		return array_key_exists( $key, $this->prop( 'fields' ) );
	}

	/**
	 * Get an field at a given offset.
	 *
	 * @param  mixed $key The field key ID.
	 * @return array|null
	 */
	public function offsetGet( $key ) {
		return $this->get_field_raw( $key );
	}

	/**
	 * Set the item at a given offset.
	 *
	 * @param  mixed $key  Field ID.
	 * @param  mixed $args Field args.
	 * @return void
	 */
	public function offsetSet( $key, $args ) {
		$this->add_field( array_merge( (array) $args, [ 'id' => $key ] ) );
	}

	/**
	 * Unset the item at a given offset.
	 *
	 * @param  string $key Field key ID.
	 * @return void
	 */
	public function offsetUnset( $key ) {
		$this->remove_field( $key );
	}

	/**
	 * Magic getter object property.
	 *
	 * @param  string $property Object property.
	 * @return mixed
	 *
	 * @throws \Exception
	 */
	public function __get( $property ) {
		switch ( $property ) {
			case 'id':
			case 'form_id':
				return $this->get_id();
			default:
				return parent::__get( $property );
		}
	}
}
