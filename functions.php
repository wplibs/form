<?php
/**
 * Core functions.
 *
 * @package WPLibs\Form
 */

if ( defined( 'WPLIBS_FORM_VERSION' ) ) {
	return;
}

use WPLibs\Form\Box\Box;
use WPLibs\Form\Registry;
use WPLibs\Http\Json_Response;
use WPLibs\Form\Core\Utils\Webfonts;

/* Constants */
define( 'WPLIBS_FORM_VERSION', '1.0.0' );

/* Load core functions */
require_once __DIR__ . '/sanitizes.php';

/**
 * Create a new meta-box.
 *
 * @param string $id   An specific ID of the box.
 * @param array  $args Box arguments.
 * @return \WPLibs\Form\Box\Box
 */
function wplibs_new_box( $id, $args = [] ) {
	if ( did_action( 'wplibs_loaded_boxes' ) ) {
		_doing_it_wrong( __FUNCTION__, 'Add new box must be called in the `wplibs_register_boxes` action.', null );
	}

	return new Box( $id, $args );
}

/**
 * Register a custom field type.
 *
 * @param  string   $name
 * @param  callable $callback
 * @param  callable $sanitizer
 * @return void
 */
function wplibs_register_field( $name, $callback = null, $sanitizer = null ) {
	Registry::register( $name, $callback, $sanitizer );
}

/**
 * Register boxes into the WordPress.
 *
 * @return void
 */
function _wplibs_register_boxes() {
	if ( ! did_action( 'cmb2_init' ) ) {
		_doing_it_wrong( __FUNCTION__, 'Unable located the CMB2, double check your setup.', null );
		return;
	}

	/**
	 * Doing the register boxes at here.
	 *
	 * @see wplibs_new_box
	 */
	do_action( 'wplibs_register_boxes' );

	foreach ( Box::get_instances() as $instance ) {
		$instance->register();
	}

	do_action( 'wplibs_loaded_boxes' );
}
add_action( 'init', '_wplibs_register_boxes', 10000 );

/**
 * Register admin scripts.
 *
 * @access private
 */
function _wplibs_register_admin_scripts() {
	$suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$libs_url = untrailingslashit( plugin_dir_url( __FILE__ ) );

	wp_register_style( 'selectize', $libs_url . '/libs/selectize/selectize.css', [], '0.12.6' );
	wp_register_script( 'selectize', $libs_url . '/libs/selectize/selectize' . $suffix . '.js', [], '0.12.6', true );

	wp_register_style( 'jquery-ui-slider-pips', $libs_url . '/libs/jquery-ui-slider-pips/jquery-ui-slider-pips' . $suffix . '.css', [], '1.11.4' );
	wp_register_script( 'jquery-ui-slider-pips', $libs_url . '/libs/jquery-ui-slider-pips/jquery-ui-slider-pips' . $suffix . '.js', [ 'jquery-ui-slider' ], '1.11.4', true );

	$deps = [ 'backbone', 'wp-util', 'cmb2-scripts', 'selectize', 'jquery-ui-sortable', 'jquery-effects-highlight' ];
	wp_register_style( 'wplibs-form', $libs_url . '/css/form' . $suffix . '.css', [ 'selectize' ], '1.0.0-dev' );
	wp_register_script( 'wplibs-form', $libs_url . '/js/form' . $suffix . '.js', $deps, '1.0.0-dev', true );

	// Localize scripts.
	wp_localize_script( 'wplibs-form', '_wpLibsForm', wplibs_get_localize_js() );
}
add_action( 'admin_enqueue_scripts', '_wplibs_register_admin_scripts', 9 );

/**
 * Returns the localize JS.
 *
 * @return array
 */
function wplibs_get_localize_js() {
	return apply_filters( 'suru_libs_get_localize_js', [
		'debug' => defined( 'WP_DEBUG' ) && WP_DEBUG,
		'urls'  => [
			'ajax'              => admin_url( 'admin-ajax.php' ),
			// 'assets'            => suru_libs()->get_url(),
			// 'webfonts_fallback' => suru_libs()->get_url() . '/src/Resources/webfonts.json',
		],
		'i18n'  => [
			'ok'      => esc_html__( 'OK', 'suru-libs' ),
			'cancel'  => esc_html__( 'Cancel', 'suru-libs' ),
			'warning' => esc_html__( 'Are you sure you want to do this?', 'suru-libs' ),
			'error'   => esc_html__( 'Something went wrong. Please try again.', 'suru-libs' ),
		],
	]);
}

/**
 * Ajax response all webfonts.
 *
 * @return void
 */
function _wplibs_ajax_get_fonts() {
	( new Json_Response( Webfonts::get_fonts() ) )->send();
	exit;
}
add_action( 'wp_ajax_wplibs_get_fonts', '_suru_libs_ajax_get_fonts' );
