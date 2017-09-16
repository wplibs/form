<?php
namespace Skeleton\Fields;

class Manager {
	/**
	 * A list of registered custom fields.
	 *
	 * @var array
	 */
	protected $fields = [];

	/**
	 * Init fields manager.
	 */
	public function __construct() {
		$this->fields = apply_filters( 'skeleton/cmb2/fields', array(
			'range'             => 'Skeleton\Fields\Range_Field',
			'toggle'            => 'Skeleton\Fields\Toggle_Field',
			'backups'           => 'Skeleton\Fields\Backups_Field',
			'js_code'           => 'Skeleton\Fields\JS_Code_Field',
			'css_code'          => 'Skeleton\Fields\CSS_Code_Field',
			'html_code'         => 'Skeleton\Fields\HTML_Code_Field',
			'image'             => 'Skeleton\Fields\Image_Field',
			'radio_image'       => 'Skeleton\Fields\Radio_Image_Field',
			'link_color'        => 'Skeleton\Fields\Link_Color_Field',
			'rgba_colorpicker'  => 'Skeleton\Fields\Rgba_Colorpicker_Field',
			'typography'        => 'Skeleton\Fields\Typography_Field',
		));
	}

	/**
	 * Register a custom field.
	 *
	 * @param  string  $name       Custom field name.
	 * @param  string  $class_name Custom field class name.
	 * @param  boolean $force      Force register custom field even already exists.
	 * @return bool
	 */
	public function register_field( $name, $class_name, $force = false ) {
		if ( ! $force && $this->has_field( $name ) ) {
			return false;
		}

		$this->fields[ $name ] = $class_name;
		return true;
	}

	/**
	 * Check if a custom field already in registered list.
	 *
	 * @param  string $name Field name.
	 * @return boolean
	 */
	public function has_field( $name ) {
		return isset( $this->fields[ $name ] );
	}

	/**
	 * Return a array custom registered fields.
	 *
	 * @return array
	 */
	public function get_fields() {
		return $this->fields;
	}

	/**
	 * Hooks custom fields to CMB2.
	 */
	public function register_fields() {
		foreach ( $this->fields as $type => $class ) {
			$field = new $class;
			if ( ! $field instanceof CMB2_Field ) {
				return;
			}

			// Set field type at runtime.
			$field->type = $type;

			// Hook current field to CMB2.
			add_action( 'cmb2_render_' . $type, array( $field, 'output' ), 10, 5 );
			add_action( 'cmb2_sanitize_' . $type, array( $field, 'sanitization' ), 10, 5 );

			// If custom field tell that is not repeatable, add this field to blacklist.
			if ( ! $field->repeatable ) {
				add_filter( 'cmb2_non_repeatable_fields', array( $field, 'disable_repeatable' ) );
			}

			// Run custom hooks after done.
			$field->hooks();
		}
	}
}
