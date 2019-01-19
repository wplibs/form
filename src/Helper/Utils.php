<?php

namespace WPLibs\Form\Helper;

class Utils {
	/**
	 * Enqueue the CMB2 scripts & styles.
	 *
	 * @return void
	 */
	public static function enqueue_scripts() {
		\CMB2_hookup::enqueue_cmb_css();
		\CMB2_hookup::enqueue_cmb_js();

		wp_enqueue_style( 'wplibs-form' );
		wp_enqueue_script( 'wplibs-form' );
	}

	/**
	 * Transform key from array to dot syntax.
	 *
	 * @param  string $key
	 * @return mixed
	 */
	public static function to_dots_key( $key ) {
		return str_replace( [ '.', '[]', '[', ']' ], [ '_', '', '.', '' ], $key );
	}

	/**
	 * Determine if the given value is "blank".
	 *
	 * Still accepts 0.
	 *
	 * @param  mixed $value The given value.
	 * @return bool
	 */
	public static function is_blank( $value ) {
		if ( is_null( $value ) ) {
			return true;
		}

		if ( is_string( $value ) ) {
			return trim( $value ) === '';
		}

		if ( $value instanceof \Countable ) {
			return count( $value ) === 0;
		}

		/* @noinspection TypeUnsafeComparisonInspection */
		if ( 0 === $value || '0' === $value ) {
			return false;
		}

		return empty( $value );
	}

	/**
	 * Determine if the given value is "not blank".
	 *
	 * @param  mixed $value The given value.
	 * @return bool
	 */
	public static function is_not_blank( $value ) {
		return ! static::is_blank( $value );
	}

	/**
	 * Filter and remove blank values.
	 *
	 * @param  array $values
	 * @return array
	 */
	public static function filter_blank( array $values ) {
		return array_filter( $values, [ static::class, 'is_not_blank' ] );
	}

	/**
	 * Recursive sanitize a given values.
	 *
	 * @param  mixed  $values    The values.
	 * @param  string $sanitizer The sanitizer callback.
	 * @return mixed
	 */
	public static function sanitize_recursive( $values, $sanitizer ) {
		if ( ! is_array( $values ) ) {
			return is_scalar( $values ) ? $sanitizer( $values ) : $values;
		}

		foreach ( $values as $key => $value ) {
			if ( is_array( $value ) ) {
				$values[ $key ] = static::sanitize_recursive( $value, $sanitizer );
			} else {
				$values[ $key ] = is_scalar( $value ) ? $sanitizer( $value ) : $value;
			}
		}

		return $values;
	}

	/**
	 * Recursive escape value.
	 *
	 * @param  array $values
	 * @return string
	 */
	public static function esc_value( $values ) {
		return static::sanitize_recursive( $values, 'esc_attr' );
	}

	/**
	 * Returns class string by given an array of classes.
	 *
	 * @param  array $classes The array of class.
	 * @return string
	 */
	public static function esc_html_class( $classes ) {
		$classes = array_filter( array_unique( (array) $classes ) );

		if ( empty( $classes ) ) {
			return '';
		}

		return implode( ' ', array_map( 'sanitize_html_class', $classes ) );
	}

	/**
	 * Build an HTML attribute string from an array.
	 *
	 * @param  array $attributes The HTML attributes.
	 * @return string
	 */
	public static function build_html_attributes( $attributes ) {
		$html = [];

		foreach ( (array) $attributes as $key => $value ) {
			$element = static::build_attribute_element( $key, $value );

			if ( ! is_null( $element ) ) {
				$html[] = $element;
			}
		}

		return count( $html ) > 0 ? ' ' . implode( ' ', $html ) : '';
	}

	/**
	 * Build a single attribute element.
	 *
	 * @param string $key
	 * @param string $value
	 * @return string|null
	 */
	protected static function build_attribute_element( $key, $value ) {
		// For numeric keys we will assume that the value is a boolean attribute
		// where the presence of the attribute represents a true value and the
		// absence represents a false value.
		if ( is_numeric( $key ) ) {
			return $value;
		}

		// Treat boolean attributes as HTML properties.
		if ( is_bool( $value ) && 'value' !== $key ) {
			return $value ? $key : '';
		}

		if ( is_array( $value ) && 'class' === $key ) {
			return 'class="' . static::esc_html_class( $value ) . '"';
		}

		if ( ! is_null( $value ) ) {
			return $key . '="' . esc_attr( $value ) . '"';
		}

		return null;
	}

	/**
	 * Deprecated a CMB2 method.
	 *
	 * @param string $method The method name.
	 */
	public static function deprecated_cmb2_method( $method ) {
		// @codingStandardsIgnoreLine
		trigger_error( 'The [' . $method . '] method has been deprecated and no longer works.', E_USER_DEPRECATED );
	}
}
