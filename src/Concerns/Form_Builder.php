<?php

namespace WPLibs\Form\Concerns;

use WPLibs\Http\Request;
use WPLibs\Session\Store;
use WPLibs\Form\Group_Builder;
use WPLibs\Form\Contracts\Storage;

trait Form_Builder {
	/**
	 * The form storage.
	 *
	 * @var \WPLibs\Form\Contracts\Storage
	 */
	protected $storage;

	/**
	 * Indicator the form data should be initialized upon creation.
	 *
	 * @var bool
	 */
	protected $auto_initialize = true;

	/**
	 * The http request implementation.
	 *
	 * @var \WPLibs\Http\Request|null
	 */
	protected $request;

	/**
	 * The session store implementation.
	 *
	 * @var \WPLibs\Session\Store|null
	 */
	protected $session;

	/**
	 * The types of inputs to not fill values on by default.
	 *
	 * @var array
	 */
	protected $skip_fill_types = [ 'file', 'password', 'checkbox', 'radio' ];

	/**
	 * Indicator the form is locked.
	 *
	 * @var bool
	 */
	protected $locked = false;

	/**
	 * {@inheritdoc}
	 */
	public function add_group( $args, $position = 0 ) {
		$args['type'] = 'group';

		$id = $this->add_field( $args, $position );

		return new Group_Builder( $this, $id );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_storage() {
		return $this->storage;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_storage( Storage $storage ) {
		$this->throws_if_locked();

		$this->storage = $storage;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_request() {
		return $this->request;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_request( Request $request ) {
		$this->throws_if_locked();

		$this->request = $request;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_session_store() {
		return $this->session;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_session_store( Store $session ) {
		$this->throws_if_locked();

		$this->session = $session;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_auto_initialize() {
		return $this->auto_initialize;
	}

	/**
	 * {@inheritdoc}
	 */
	public function auto_initialize( $initialize ) {
		$this->throws_if_locked();

		$this->auto_initialize = (bool) $initialize;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_skip_fill_types() {
		return $this->skip_fill_types;
	}

	/**
	 * {@inheritdoc}
	 */
	public function skip_fill_types( $types ) {
		$this->skip_fill_types = array_unique( array_merge( $this->skip_fill_types, $types ) );

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_options() {
		return $this->meta_box ?: [];
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
		$this->throws_if_locked();

		$this->set_prop( $name, $value );

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_prop( $property, $value ) {
		$this->throws_if_locked();

		return parent::set_prop( $property, $value );
	}

	/**
	 * Throws an exception if builder is locked.
	 *
	 * @param  string $action
	 * @return void
	 */
	protected function throws_if_locked( $action = 'modify' ) {
		if ( $this->locked ) {
			$message = 'modify' === $action
				? 'The Form builder cannot be modified anymore.'
				: 'Form Builder methods cannot be accessed anymore once the builder is turned into a Form Configuration instance.';

			throw new \BadMethodCallException( $message );
		}
	}
}
