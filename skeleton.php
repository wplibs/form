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

if ( ! class_exists( 'Skeleton_Bootstrap_110', false ) ) {
	require_once trailingslashit( __DIR__ ) . 'bootstrap.php';

	new Skeleton_Bootstrap_110;
}
