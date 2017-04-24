<?php
namespace Skeleton\CMB2\Fields;

class Icon_Field extends Field_Abstract {
	/**
	 * Render custom field type callback.
	 *
	 * @param \CMB2_Field $field              The passed in `CMB2_Field` object.
	 * @param mixed       $escaped_value      The value of this field escaped.
	 * @param int|string  $object_id          The ID of the current object.
	 * @param string      $object_type        The type of object you are working with.
	 * @param \CMB2_Types $field_type_object  The `CMB2_Types` object.
	 */
	public function output( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		$defaults = array(
			'id'    => '',
			'name'  => '',
			'value' => array(
				'type' => '',
				'icon' => '',
			),
			'select' => sprintf( '<a class="ipf-select">%s</a>', esc_html__( 'Select Icon', 'icon-picker-field' ) ),
			'remove' => sprintf( '<a class="ipf-remove button hidden">%s</a>', esc_html__( 'Remove', 'icon-picker-field' ) ),
		);

		$args          = array();
		$args          = wp_parse_args( $args, $defaults );
		$args['value'] = wp_parse_args( $args['value'], $defaults['value'] );

		$field  = sprintf( '<div id="%s" class="ipf">', $args['id'] );
		$field .= $args['select'];
		$field .= $args['remove'];

		foreach ( $args['value'] as $key => $value ) {
			$field .= sprintf(
				'<input type="hidden" id="%s" name="%s" class="%s" value="%s" />',
				esc_attr( "{$args['id']}-{$key}" ),
				esc_attr( "{$args['name']}[{$key}]" ),
				esc_attr( "ipf-{$key}" ),
				esc_attr( $value )
			);
		}

		// This won't be saved. It's here for the preview.
		$field .= sprintf(
			'<input type="hidden" class="url" value="%s" />',
			esc_attr( icon_picker_get_icon_url( $args['value']['type'], $args['value']['icon'] ) )
		);
		$field .= '</div>';

		echo $field; // xss ok
	}
}

/**
 * Get Icon URL
 *
 * @since  0.2.0
 *
 * @param  string  $type  Icon type.
 * @param  mixed   $id    Icon ID.
 * @param  string  $size  Optional. Icon size, defaults to 'thumbnail'.
 *
 * @return string
 */
function icon_picker_get_icon_url( $type, $id, $size = 'thumbnail' ) {
	$url = '';

	if ( ! in_array( $type, array( 'image', 'svg' ) ) ) {
		return $url;
	}

	if ( empty( $id ) ) {
		return $url;
	}

	return wp_get_attachment_image_url( $id, $size, false );
}
