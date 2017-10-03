<?php

use Skeleton\Skeleton;
use Skeleton\Support\WP_Data;

if ( ! function_exists( 'dd' ) ) {
	/**
	 * Dump the passed variables and end the script.
	 */
	function dd() {
		// @codingStandardsIgnoreLine
		array_map( function( $x ) { var_dump( $x ); }, func_get_args() );
		die;
	}
}

if ( ! function_exists( 'skeleton' ) ) {
	/**
	 * Get Skeleton instance.
	 *
	 * @return Skeleton
	 */
	function skeleton() {
		return Skeleton::get_instance();
	}
}

if ( ! function_exists( 'wp_data' ) ) {
	/**
	 * Get Wordpress specific data from the DB and return in a usable array.
	 *
	 * @param  string $type Data type.
	 * @param  mixed  $args Optional, data query args or something else.
	 * @return array
	 */
	function wp_data( $type, $args = array() ) {
		return WP_Data::get( $type, $args );
	}
}

if ( ! function_exists( 'skeleton_render_field' ) ) :
	/**
	 * Tiny helper render a field.
	 *
	 * @param  CMB2_Field $field CMB2 Field instance.
	 * @return void
	 */
	function skeleton_render_field( CMB2_Field $field ) {
		(new CMB2_Types( $field ))->render();
	}
endif;

if ( ! function_exists( 'skeleton_display_field_errors' ) ) :
	/**
	 * Tiny helper display field errors.
	 *
	 * @param  CMB2_Field $field CMB2 Field instance.
	 * @return void
	 */
	function skeleton_display_field_errors( CMB2_Field $field ) {
		$cmb2 = $field->get_cmb();

		// Don't show if invalid CMB2 instance.
		if ( ! $cmb2 || is_wp_error( $cmb2 ) ) {
			return;
		}

		$id = $field->id( true );
		$errors = $cmb2->get_errors();

		if ( isset( $errors[ $id ] ) ) {
			$error_message = is_string( $errors[ $id ] ) ? $errors[ $id ] : $errors[ $id ][0];
			printf( '<p class="cmb2-validate-error">%s</p>', $error_message ); // WPCS: XSS OK.
		}
	}
endif;

if ( ! function_exists( 'skeleton_psr4_autoloader' ) ) :
	/**
	 * Register PSR-4 autoload classess.
	 *
	 * @param  string|array $namespace A string of namespace or an array with
	 *                                 namespace and directory to autoload.
	 * @param  string       $base_dir  Autoload directory if $namespace is string.
	 * @return void
	 */
	function skeleton_psr4_autoloader( $namespace, $base_dir = null ) {
		$loader = new Composer\Autoload\ClassLoader;

		if ( is_string( $namespace ) && $base_dir ) {
			$loader->setPsr4( rtrim( $namespace, '\\' ) . '\\', $base_dir );
		} elseif ( is_array( $namespace ) ) {
			foreach ( $namespace as $prefix => $dir ) {
				$loader->setPsr4( rtrim( $prefix, '\\' ) . '\\', $dir );
			}
		}

		$loader->register( true );
	}
endif;
