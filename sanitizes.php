<?php
/**
 * Sanitization Functions.
 *
 * This file demonstrates how to define sanitization callback functions for various data types.
 *
 * @package Suru/Libs
 */

/**
 * Sanitizes a simple text string.
 *
 * @param  mixed $value The string to sanitize.
 * @return string
 */
function wplibs_sanitize_text( $value ) {
	return sanitize_text_field( wp_unslash( $value ) );
}

/**
 * Sanitizes content that could contain HTML.
 *
 * @param  mixed $value The HTML string to sanitize.
 * @return string
 */
function wplibs_sanitize_html( $value ) {
	return wp_filter_post_kses( $value );
}

/**
 * Sanitizes content that could not contain HTML.
 *
 * @param  string $nohtml The no-HTML content to sanitize.
 * @return string
 */
function wplibs_sanitize_nohtml( $nohtml ) {
	return wp_filter_nohtml_kses( $nohtml );
}

/**
 * Sanitizes for checkbox or toggle.
 *
 * @param  mixed $value The checkbox value.
 * @return string
 */
function wplibs_sanitize_checkbox( $value ) {
	return in_array( $value, [ 'on', 'yes', 'true', true ] ) ? '1' : '0';
}

/**
 * Sanitizes a color value with support bold hex & rgba.
 *
 * @param  string $color The value to sanitize.
 * @return string
 */
function wplibs_sanitize_color( $color ) {
	$color = trim( $color );

	if ( empty( $color ) || is_string( $color ) ) {
		return '';
	}

	if ( 'transparent' === $color ) {
		return $color;
	}

	// Given a color name, return the hex.
	static $color_names;

	if ( ! $color_names ) {
		$color_names = json_decode(
			file_get_contents( __DIR__ . '/src/Resources/color-names.json' ), true
		);
	}

	if ( is_array( $color_names ) && array_key_exists( $color, $color_names ) ) {
		return $color_names[ $color ];
	}

	if ( false !== strpos( $color, '#' ) ) {
		return sanitize_hex_color( $color );
	}

	if ( false !== strpos( $color, 'rgba(' ) ) {
		return wplibs_sanitize_rgba_color( $color );
	}

	return '';
}

/**
 * Sanitizes an RGBA color value.
 *
 * @param  string $color The RGBA color value to sanitize.
 * @return string
 */
function wplibs_sanitize_rgba_color( $color ) {
	// Trim unneeded whitespace.
	$color = trim( str_replace( ' ', '', $color ) );

	/* @noinspection PhpUndefinedVariableInspection */
	sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );

	if ( ( $red >= 0 && $red <= 255 )
		&& ( $green >= 0 && $green <= 255 )
		&& ( $blue >= 0 && $blue <= 255 )
		&& ( $alpha >= 0 && $alpha <= 1 ) ) {
		return "rgba({$red},{$green},{$blue},{$alpha})";
	}

	return '';
}

/**
 * Sanitizes emails address.
 *
 * @param  string $email The email to sanitize.
 * @return string
 */
function wplibs_sanitize_emails( $email ) {
	if ( ! is_array( $email ) ) {
		$email = array_map( 'trim', explode( ',', $email ) );
	}

	return implode( ', ', array_filter( $email, 'is_email' ) );
}

/**
 * Sanitizes comma-separated list of IDs.
 *
 * @param  string $list The value to sanitize.
 * @return string
 */
function wplibs_sanitize_ids( $list ) {
	return implode( ', ', wp_parse_id_list( $list ) );
}

/**
 * Sanitization callback for 'url' type text inputs.
 *
 * @param  string $url URL to sanitize.
 * @return string
 */
function wplibs_sanitize_url( $url ) {
	return esc_url_raw( $url );
}

/**
 * Sanitization callback for 'css' type textarea inputs.
 *
 * @param  string $css CSS to sanitize.
 * @return string
 */
function wplibs_sanitize_css( $css ) {
	return trim( wp_strip_all_tags( $css ) );
}

/**
 * Sanitization callback for 'dropdown-pages' type controls.
 *
 * @param  int $page_id Page ID.
 * @return int|null
 */
function wplibs_sanitize_dropdown_pages( $page_id ) {
	$page_id = absint( $page_id );

	return 'publish' === get_post_status( $page_id ) ? $page_id : null;
}
