<?php

namespace WPLibs\Form\Box;

class Loader extends \CMB2_hookup {
	/**
	 * Determines scripts is enqueued.
	 *
	 * @var bool
	 */
	protected static $enqueued = false;

	/**
	 * Fire the hooks.
	 *
	 * @return void
	 */
	public function fire() {
		$context = Context::guest();

		$this->cmb->set_context( $context );
		$this->object_type = $this->cmb->mb_object_type();

		if ( $this->cmb->prop( 'hookup' ) ) {
			$this->universal_hooks();
		}

		$this->once( 'admin_footer', [ __CLASS__, 'enqueue_scripts' ], 20 );
		$this->once( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_styles' ], 20 );
	}

	/**
	 * Enqueue the form styles.
	 */
	public static function enqueue_styles() {
		if ( ! static::$enqueued && wp_style_is( 'cmb2-styles', 'enqueued' ) ) {
			wp_enqueue_style( 'wplibs-form' );
			wp_enqueue_script( 'wplibs-form' );

			static::$enqueued = true;
		}
	}

	/**
	 * Enqueue the form scripts.
	 */
	public static function enqueue_scripts() {
		if ( ! static::$enqueued && wp_script_is( 'cmb2-scripts', 'enqueued' ) ) {
			wp_enqueue_script( 'wplibs-form' );
			static::$enqueued = true;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function options_page_hooks() {
		// @codingStandardsIgnoreLine
		_deprecated_function( get_class( $this ) . '::' . __FUNCTION__, '1.0.0' );
	}
}
