<?php

namespace WPLibs\Form\Core\Field_Types;

use WPLibs\Form\Helper\Utils;

abstract class Field_Type implements \WPLibs\Form\Contracts\Field_Type {
	/**
	 * Default field values.
	 *
	 * @var mixed
	 */
	protected $defaults;

	/**
	 * {@inheritdoc}
	 */
	public function sanitization( $value ) {
		return Utils::sanitize_recursive( $this->parse_value( $value ), function ( $_value ) {
			return sanitize_text_field( wp_unslash( $_value ) );
		} );
	}

	/**
	 * Parse the value, merge with $defaults.
	 *
	 * @param  mixed $value
	 * @return array|mixed
	 */
	public function parse_value( $value ) {
		if ( is_array( $this->defaults ) ) {
			$value = is_array( $value )
				? wp_parse_args( $value, $this->defaults )
				: $this->defaults;
		}

		return $value;
	}

	/**
	 * Print JS control container.
	 *
	 * @return void
	 */
	protected function print_control_container() {
		echo '<div class="wplibs-form__control-area"></div>';
	}
}
