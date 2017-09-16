<?php
/**
 * Skeleton bootstrap file.
 *
 * @package Skeleton
 */

// Load local composer autoload.
if ( file_exists( trailingslashit( __DIR__ ) . 'vendor/autoload.php' ) ) {
	require_once trailingslashit( __DIR__ ) . 'vendor/autoload.php';
}

// Try locate the CMB2.
if ( file_exists( trailingslashit( __DIR__ ) . 'vendor/webdevstudios/cmb2/init.php' ) ) {
	require_once trailingslashit( __DIR__ ) . 'vendor/webdevstudios/cmb2/init.php';
} elseif ( file_exists( __DIR__ . '/../../webdevstudios/cmb2/init.php' ) ) {
	require_once __DIR__ . '/../../webdevstudios/cmb2/init.php';
} elseif ( ! defined( 'CMB2_LOADED' ) ) {
	throw new RuntimeException( 'Unable to locate the CMB2' );
}
