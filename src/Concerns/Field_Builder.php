<?php

namespace WPLibs\Form\Concerns;

use WPLibs\Form\Contracts\Form;
use WPLibs\Form\Contracts\Field;
use WPLibs\Form\Contracts\Storage;

trait Field_Builder {
	/**
	 * Store the form implementation.
	 *
	 * @var \WPLibs\Form\Contracts\Form
	 */
	protected $form;

	/**
	 * The storage implementation for current field.
	 *
	 * @var \WPLibs\Form\Contracts\Storage
	 */
	protected $storage;

	/**
	 * {@inheritdoc}
	 */
	public function get_form() {
		return $this->form;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_form( Form $form ) {
		$this->form = $form;

		return $this;
	}

	/**
	 * Returns the storage instance.
	 *
	 * @return \WPLibs\Form\Contracts\Storage|null
	 */
	public function get_storage() {
		return $this->storage;
	}

	/**
	 * Sets the storage implementation.
	 *
	 * @param \WPLibs\Form\Contracts\Storage $storage
	 * @return $this
	 */
	public function set_storage( Storage $storage ) {
		$this->storage = $storage;

		return $this;
	}

	/**
	 * Return the group field instance.
	 *
	 * @return \WPLibs\Form\Contracts\Field|null
	 */
	public function get_group() {
		return $this->group;
	}

	/**
	 * Sets the group field instance.
	 *
	 * @param \WPLibs\Form\Contracts\Field $group
	 * @return $this
	 */
	public function set_group( Field $group ) {
		$this->group = $group;

		$this->form    = $this->group->get_form();
		$this->storage = $this->group->get_storage();

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_attributes() {
		return $this->get_option( 'attributes', [] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_attribute( $key, $default = null ) {
		$attributes = $this->get_attributes();

		return array_key_exists( $key, $attributes ) ? $attributes[ $key ] : $default;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_attribute( $attribute, $value = '' ) {
		$attributes = is_array( $attribute ) ? $attribute : [ $attribute => $value ];

		$this->set_option( 'attributes', array_merge( $this->get_attributes(), $attributes ) );

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_options() {
		return $this->args ?: [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function has_option( $name ) {
		return array_key_exists( $name, $this->get_options() );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_option( $name, $default = null ) {
		return $this->prop( $name, $default );
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_option( $name, $value ) {
		$this->set_prop( $name, $value );

		return $this;
	}
}
