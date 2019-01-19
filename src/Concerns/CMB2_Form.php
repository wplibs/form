<?php

namespace WPLibs\Form\Concerns;

use WPLibs\Form\Field;
use WPLibs\Form\Field_Group;
use WPLibs\Form\Helper\Utils;
use WPLibs\Form\Contracts\Field as Field_Contract;

trait CMB2_Form {
	/**
	 * {@inheritdoc}
	 */
	public function object_id( $object_id = 0 ) {
		return 0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function object_type( $object_type = '' ) {
		return 'suru_form';
	}

	/**
	 * {@inheritdoc}
	 */
	public function mb_object_type() {
		return 'suru_form';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_field( $field, $field_group = null, $deprecated = false ) {
		$field = is_array( $field ) ? $field['id'] : (string) $field;

		// Just for back-compat.
		if ( $field_group ) {
			return $this->get_field_in_group( $field_group, $field );
		}

		return $this->get_new_field(
			$this->get_field_raw( $field )
		);
	}

	/**
	 * Gets a field in a group.
	 *
	 * @param \WPLibs\Form\Contracts\Field|string $field_group
	 * @param string                              $sub_field
	 * @return \WPLibs\Form\Contracts\Field|null
	 */
	public function get_field_in_group( $field_group, $sub_field ) {
		if ( ! $field_group instanceof Field_Contract ) {
			$field_group = $this->get_field( $field_group );
		}

		if ( ! $field_group || 'group' !== $field_group->get_type() ) {
			return null;
		}

		$sub_fields = $field_group->prop( 'fields', [] );

		if ( ! array_key_exists( $sub_field, $sub_fields ) ) {
			return null;
		}

		return $this->get_new_field( $sub_fields[ $sub_field ], $field_group );
	}

	/**
	 * Return the field raw
	 *
	 * @param  string $field
	 * @return array|null
	 */
	public function get_field_raw( $field ) {
		if ( array_key_exists( $field, $this->prop( 'fields' ) ) ) {
			return $this->prop( 'fields' )[ $field ];
		}

		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_new_field( $field_args, $field_group = null ) {
		if ( 'group' === $field_args['type'] ) {
			return new Field_Group( $this, $this->get_default_args( $field_args ) );
		}

		return new Field( $this, $this->get_default_args( $field_args, $field_group ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function render_group_callback( $field_args, $group ) {
		if ( ! $group || ! $group->should_show() ) {
			return;
		}

		include dirname( __DIR__ ) . '/Resources/html/html-group.php';
	}

	/**
	 * {@inheritdoc}
	 */
	public function render_group_row( $group ) {
		include dirname( __DIR__ ) . '/Resources/html/html-group-row.php';
	}

	/**
	 * {@inheritdoc}
	 */
	public function show_form( $object_id = 0, $object_type = '' ) {
		Utils::deprecated_cmb2_method( __FUNCTION__ );
	}

	/**
	 * {@inheritdoc}
	 */
	public function render_field( $field_args ) {
		Utils::deprecated_cmb2_method( __FUNCTION__ );
	}

	/**
	 * {@inheritdoc}
	 */
	public function add_hidden_field( $field_args, $field_group = null ) {
		Utils::deprecated_cmb2_method( __FUNCTION__ );
	}

	/**
	 * {@inheritdoc}
	 */
	public function save_fields( $object_id = 0, $object_type = '', $data_to_save = [] ) {
		Utils::deprecated_cmb2_method( __FUNCTION__ );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_sanitized_values( array $data_to_sanitize ) {
		Utils::deprecated_cmb2_method( __FUNCTION__ );
	}

	/**
	 * {@inheritdoc}
	 */
	public function init_options_mb() {
		Utils::deprecated_cmb2_method( __FUNCTION__ );
	}

	/**
	 * {@inheritdoc}
	 */
	public function options_page_keys() {
		Utils::deprecated_cmb2_method( __FUNCTION__ );
	}
}
