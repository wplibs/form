<?php
/**
 * Skeleton bootstrap file.
 *
 * @package Skeleton
 */

use Skeleton\Skeleton;
use Composer\Autoload\ClassLoader;

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
		$loader = new ClassLoader;

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

/**
 * Handles checking for and loading the newest version of Skeleton.
 */
class Skeleton_Bootstrap_110 {
	/**
	 * Current priority, decrement with each release.
	 *
	 * @var int
	 */
	const PRIORITY = 9890;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->try_locate_cmb2();

		if ( ! defined( 'AWETHEMES_SKELETON_LOADED' ) ) {
			define( 'AWETHEMES_SKELETON_LOADED', static::PRIORITY );
		}

		add_action( 'plugins_loaded', array( $this, 'start_skeleton' ), static::PRIORITY );
	}

	/**
	 * Start the Skeleton.
	 *
	 * @access private
	 */
	public function start_skeleton() {
		if ( class_exists( 'Skeleton\Skeleton', false ) ) {
			return;
		}

		// Include helper functions.
		require_once trailingslashit( __DIR__ ) . 'inc/Support/helpers.php';
		require_once trailingslashit( __DIR__ ) . 'inc/Skeleton.php';

		skeleton_psr4_autoloader( 'Skeleton\\', trailingslashit( __DIR__ ) . 'inc/' );

		if ( ! defined( 'SKELETON_VERSION' ) ) {
			define( 'SKELETON_VERSION', Skeleton::VERSION );
		}

		add_action( 'cmb2_init', array( new Skeleton, 'run' ) );
	}

	/**
	 * Try locale the CMB2.
	 *
	 * @return void
	 */
	protected function try_locate_cmb2() {
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
			trigger_error( 'The Skeleton unable to locate the CMB2' );
		}
	}
}
