<?php
namespace Skeleton;

final class Skeleton {
	/* Constants */
	const VERSION = '1.0.3';

	/**
	 * The current globally available container (if any).
	 *
	 * @var static
	 */
	protected static $instance;

	/**
	 * Field manager instance.
	 *
	 * @var Manager
	 */
	protected $fields;

	/**
	 * Wwebfonts manager instance.
	 *
	 * @var Webfonts
	 */
	protected $webfonts;

	/**
	 * Set the globally available instance of the container.
	 *
	 * @return static
	 */
	public static function get_instance() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * Instantiate the Skeleton.
	 */
	public function __construct() {
		$this->fields = new Fields\Manager;
		$this->webfonts = new Webfonts\Webfonts;

		static::$instance = $this;
	}

	/**
	 * Returns webfonts instance.
	 *
	 * @return Webfonts
	 */
	public function get_webfonts() {
		return $this->webfonts;
	}

	/**
	 * Returns CMB2 fields manager.
	 *
	 * @return Manager
	 */
	public function get_fields() {
		return $this->fields;
	}

	/**
	 * Returns directory url.
	 *
	 * @return string
	 */
	public function get_dir_url() {
		return plugin_dir_url( __DIR__ );
	}

	/**
	 * Returns directory path.
	 *
	 * @return string
	 */
	public function get_dir_path() {
		return plugin_dir_path( __DIR__ );
	}

	/**
	 * Run Skeleton after `cmb2_init` fired.
	 *
	 * @return void
	 */
	public function run() {
		// Doing CMB2 hooks.
		new CMB2\CMB2_Hooks;
		new CMB2\Backup_Ajax;

		do_action( 'skeleton/init', $this );

		// Register custom fields.
		$this->fields->register_fields();

		// Register and enqueue admin scripts.
		add_action( 'admin_enqueue_scripts', array( $this, '_admin_register_scripts' ), 20 );
	}

	/**
	 * Enqueue admin scripts.
	 */
	public function _admin_register_scripts() {
		$skeleton_url = $this->get_dir_url();

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG == true ) ? '' : '.min';
		$version = static::VERSION;

		// Register vendor, plugins styles & scripts.
		wp_register_script( 'ace-editor', '//cdnjs.cloudflare.com/ajax/libs/ace/1.2.6/ace.js', array(), '1.2.6', true );
		wp_register_script( 'ace-ext-language_tools', '//cdnjs.cloudflare.com/ajax/libs/ace/1.2.6/ext-language_tools.js', array( 'ace-editor' ), '1.2.6', true );

		wp_register_style( 'jquery-ui-slider-pips', $skeleton_url . 'css/jquery-ui-slider-pips.css', array(), '1.11.4' );
		wp_register_script( 'jquery-ui-slider-pips', $skeleton_url . 'js/plugins/jquery-ui-slider-pips.min.js', array( 'jquery-ui-slider' ), '1.11.4', true );
		wp_register_script( 'wp-color-picker-alpha', $skeleton_url . 'js/plugins/wp-color-picker-alpha.min.js', array( 'wp-color-picker' ), '1.2.2', true );

		wp_register_style( 'skeleton', $skeleton_url . 'css/skeleton' . $suffix . '.css', array(), $version );
		wp_register_script( 'skeleton', $skeleton_url . 'js/skeleton' . $suffix . '.js', array( 'wp-util', 'jquery-effects-highlight' ), $version, true );

		// Enqueue Skeleton.
		wp_enqueue_style( 'skeleton' );
		wp_enqueue_script( 'skeleton' );

		wp_localize_script( 'skeleton', 'Skeleton', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'strings' => array(
				'warning' => esc_html__( 'Are you sure you want to do this?', 'skeleton' ),
			),
		) );

		do_action( 'skeleton/register_admin_scripts' );
	}
}
