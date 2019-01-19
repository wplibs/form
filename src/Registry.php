<?php

namespace WPLibs\Form;

use WPLibs\Form\Helper\Utils;
use WPLibs\Form\Contracts\Field_Type;

class Registry {
	/**
	 * The registered field types bindings.
	 *
	 * @var array
	 */
	protected static $bindings = [];

	/**
	 * The field types has been resolved.
	 *
	 * @var array
	 */
	protected static $resolved = [];

	/**
	 * Store the field type instances.
	 *
	 * @var \WPLibs\Form\Contracts\Field_Type[]
	 */
	protected static $instances = [];

	/**
	 * Core field types.
	 *
	 * @var array
	 */
	protected static $core_types = [
		'toggle'        => \WPLibs\Form\Core\Field_Types\Toggle::class,
		'button_choice' => \WPLibs\Form\Core\Field_Types\Button_Choice::class,
		'media'         => \WPLibs\Form\Core\Field_Types\Media::class,
		'image'         => \WPLibs\Form\Core\Field_Types\Image::class,
		'cropped_image' => \WPLibs\Form\Core\Field_Types\Cropped_Image::class,
		'link'          => \WPLibs\Form\Core\Field_Types\Link::class,
		'slider'        => \WPLibs\Form\Core\Field_Types\Slider::class,
		'dimensions'    => \WPLibs\Form\Core\Field_Types\Dimensions::class,
		'typography'    => \WPLibs\Form\Core\Field_Types\Typography::class,
	];

	/**
	 * Initialize the custom fields.
	 *
	 * @return void
	 */
	public static function initialize() {
		foreach ( static::$core_types as $name => $class ) {
			static::register( $name, new $class );
		}

		foreach ( static::$bindings as $name => $binding ) {
			if ( ! static::resolved( $name ) ) {
				static::fire( $name, $binding['callback'], $binding['sanitizer'] );

				static::$resolved[ $name ] = true;
			}
		}

		// Print the field type JS templates.
		if ( count( static::$instances ) > 0 ) {
			add_action( 'admin_footer', [ __CLASS__, 'print_js_templates' ], 20 );
		}
	}

	/**
	 * Register a custom field type.
	 *
	 * @param  string              $name
	 * @param  callable|Field_Type $callback
	 * @param  callable            $sanitizer
	 * @return void
	 */
	public static function register( $name, $callback = null, $sanitizer = null ) {
		unset( static::$resolved[ $name ] );

		if ( $callback instanceof Field_Type ) {
			$instance = $callback;

			list( $callback, $sanitizer ) = [
				[ $instance, 'display' ],
				[ $instance, 'sanitization' ],
			];

			static::$instances[ $name ] = $instance;
		}

		static::$bindings[ $name ] = compact( 'callback', 'sanitizer' );
	}

	/**
	 * Determine if the given field type has been registered.
	 *
	 * @param  string $name
	 * @return bool
	 */
	public static function has( $name ) {
		return isset( static::$bindings[ $name ] );
	}

	/**
	 * Determine if the given field type has been resolved.
	 *
	 * @param string $name
	 * @return bool
	 */
	public static function resolved( $name ) {
		return isset( static::$resolved[ $name ] );
	}

	/**
	 * Fire the hooks to add field type in to the CMB2.
	 *
	 * @param string   $name
	 * @param callable $callback
	 * @param callable $sanitizer
	 */
	public static function fire( $name, callable $callback, $sanitizer = null ) {
		add_action( "cmb2_render_{$name}", static::get_render_callback( $callback ), 10, 5 );

		if ( ! is_null( $sanitizer ) && is_callable( $sanitizer ) ) {
			add_filter( "cmb2_sanitize_{$name}", static::get_sanitize_callback( $sanitizer ), 10, 5 );
		}
	}

	/**
	 * Returns the field types instances.
	 *
	 * @return \WPLibs\Form\Contracts\Field_Type[]
	 */
	public static function get_field_types() {
		return static::$instances;
	}

	/**
	 * Print the field JS templates.
	 *
	 * @return void
	 */
	public static function print_js_templates() {
		foreach ( static::$instances as $type => $control ) {
			if ( ! method_exists( $control, 'template' ) ) {
				continue;
			}
			?>

			<script type="text/html" id="tmpl-wplibs-field-<?php echo esc_attr( $type ); ?>-content">
				<?php $control->template(); ?>
			</script>

			<?php // @codingStandardsIgnoreLine
		}
	}

	/**
	 * Get the render field callback.
	 *
	 * @param  callable $callback
	 * @return \Closure
	 */
	protected static function get_render_callback( $callback ) {
		/**
		 * Rendering the field.
		 *
		 * @param Field       $field         The passed in `CMB2_Field` object.
		 * @param mixed       $escaped_value The value of this field escaped.
		 * @param int         $object_id     The ID of the current object.
		 * @param string      $object_type   The type of object you are working with.
		 * @param \CMB2_Types $field_types   The `CMB2_Types` object.
		 */
		return function ( $field, $escaped_value, $object_id, $object_type, $field_types ) use ( $callback ) {
			if ( $field instanceof Contracts\Field ) {
				$callback( $field, $escaped_value, $field_types );
			}
		};
	}

	/**
	 * Get the sanitize callback.
	 *
	 * @param  callable $sanitize_cb The sanitize callback.
	 * @return \Closure
	 */
	protected static function get_sanitize_callback( $sanitize_cb ) {
		/**
		 * Filter the value before it is saved.a
		 *
		 * @param bool|mixed     $check      The check variable.
		 * @param mixed          $value      The value to be saved to this field.
		 * @param int            $object_id  The ID of the object where the value will be saved.
		 * @param array          $field_args The current field's arguments.
		 * @param \CMB2_Sanitize $sanitizer  The `CMB2_Sanitize` object.
		 *
		 * @return mixed
		 */
		return function( $check, $value, $object_id, $field_args, $sanitizer ) use ( $sanitize_cb ) {
			if ( $field_args['repeatable'] ) {
				return Utils::filter_blank(
					is_array( $value ) ? array_map( $sanitize_cb, $value ) : []
				);
			}

			return $sanitize_cb( $value );
		};
	}
}
