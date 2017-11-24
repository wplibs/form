<?php
namespace Skeleton\Fields;

class Map_Field extends CMB2_Field {
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
		$field->add_js_dependencies( array( 'google-maps-api', 'google-maps' ) );

		echo '<input type="text" class="large-text skeleton-map-search" id="' . $field->args( 'id' ) . '" />';

		echo '<div class="skeleton-map"></div>';

		$field_type_object->_desc( true, true );

		echo $field_type_object->input( array(
			'type'       => 'hidden',
			'name'       => $field->args('_name') . '[latitude]',
			'value'      => isset( $escaped_value['latitude'] ) ? $escaped_value['latitude'] : '',
			'class'      => 'skeleton-map-latitude',
			'desc'       => '',
		) );

		echo $field_type_object->input( array(
			'type'       => 'hidden',
			'name'       => $field->args('_name') . '[longitude]',
			'value'      => isset( $escaped_value['longitude'] ) ? $escaped_value['longitude'] : '',
			'class'      => 'skeleton-map-longitude',
			'desc'       => '',
		) );
	}
}
