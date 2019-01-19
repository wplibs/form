<?php

namespace WPLibs\Form\Concerns;

use WPLibs\Form\Helper\Utils;

trait CMB2_Field {
	/**
	 * {@inheritdoc}
	 */
	public function value( $key = '' ) {
		$value = $this->get_value();

		// For old systax support.
		if ( $key && is_array( $value ) ) {
			return array_key_exists( $key, $value ) ? $value[ $key ] : null;
		}

		return $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default() {
		if ( is_null( $this->args['default'] ) && ! is_callable( $this->args['default_cb'] ) ) {
			return null;
		}

		return parent::get_default();
	}

	/**
	 * {@inheritdoc}
	 */
	public function escaped_value( $func = null, $meta_value = null ) {
		return parent::escaped_value( $func ?: [ Utils::class, 'esc_value' ], $meta_value );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_new_field( $field_args, $field_group = null ) {
		return new static( $this->form->get_config(), $this->get_default_args( $field_args, $field_group ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function render_field_callback() {
		if ( $this->should_show() ) {
			include dirname( __DIR__ ) . '/Resources/html/html-field-row.php';
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_cmb() {
		return $this->get_form();
	}

	/**
	 * {@inheritdoc}
	 */
	public function save_field( $meta_value ) {
		Utils::deprecated_cmb2_method( __FUNCTION__ );
	}

	/**
	 * {@inheritdoc}
	 */
	public function save_field_from_data( array $data_to_save ) {
		Utils::deprecated_cmb2_method( __FUNCTION__ );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_data( $field_id = '', $args = [] ) {
		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function update_data( $new_value, $single = true ) {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove_data( $old = '' ) {
		return true;
	}
}
