<?php
/**
 * Skeleton - The last WordPress framework with everything you'll ever need.
 *
 * @author    awethemes <http://awethemes.com>
 * @link      https://github.com/awethemes/skeleton
 * @package   Skeleton
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'AWETHEMES_SKELETON_LOADED' ) ) {
	// First, init the bootstrap.
	require_once trailingslashit( __DIR__ ) . 'bootstrap.php';

	require_once trailingslashit( __DIR__ ) . 'inc/Support/helpers.php';

	define( 'SKELETON_VERSION', Skeleton\Skeleton::VERSION );

	$skeleton = new Skeleton\Skeleton;

	/**
	 * Finally, we run Skeleton after `cmb2_init` fired.
	 *
	 * @hook skeleton/init
	 * @hook skeleton/after_init
	 */
	add_action( 'cmb2_init', array( $skeleton, 'run' ) );

	// Declare Skeleton is loaded.
	define( 'AWETHEMES_SKELETON_LOADED', true );
}
