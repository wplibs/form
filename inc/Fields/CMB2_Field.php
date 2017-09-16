<?php
namespace Skeleton\Fields;

use Skeleton\Support\Multidimensional;

abstract class CMB2_Field {
	/**
	 * Field type.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Adding this field to the blacklist of repeatable field-types.
	 *
	 * @var boolean
	 */
	public $repeatable = true;

	/**
	 * The passed in `CMB2_Field` object.
	 *
	 * @var \CMB2_Field
	 */
	protected $field;

	/**
	 * The `CMB2_Types` object.
	 *
	 * @var \CMB2_Types
	 */
	protected $field_type_object;

	/**
	 * This method will run after hook register field.
	 */
	public function hooks() {}

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
		/**
		 * Save this `field` so another method can be use late.
		 *
		 * @var \CMB2_Field
		 */
		$this->field = $field;

		/**
		 * Save this `field_type_object` similar `field`.
		 *
		 * @var \CMB2_Types
		 */
		$this->field_type_object = $field_type_object;
	}

	/**
	 * Filter the value before it is saved.
	 *
	 * @param bool|mixed     $override_value Sanitization/Validation override value to return.
	 * @param mixed          $value      The value to be saved to this field.
	 * @param int            $object_id  The ID of the object where the value will be saved.
	 * @param array          $field_args The current field's arguments.
	 * @param \CMB2_Sanitize $sanitizer  The `CMB2_Sanitize` object.
	 */
	public function sanitization( $override_value, $value, $object_id, $field_args, $sanitizer ) {}

	/**
	 * Filter field types that are non-repeatable.
	 *
	 * @param  array $fields Array of fields designated as non-repeatable.
	 * @return array
	 */
	public function disable_repeatable( $fields ) {
		$fields[ $this->type ] = 1;

		return $fields;
	}

	/**
	 * A tiny helper to clone field.
	 *
	 * @param  string $clone_suffix Clone field suffix name.
	 * @param  array  $args         Optional, custom clone field arguments.
	 * @return CMB2_Field
	 */
	protected function clone_field( $clone_suffix, $args = array() ) {
		$base_id = $this->field->group ? $this->field->args( '_id' ) : $this->field_type_object->_id();

		return $this->field->get_field_clone( wp_parse_args( $args, array(
			'id'      => $this->clone_id( $base_id, $clone_suffix ),
			'default' => $this->field->args( 'default_' . $clone_suffix ),
		) ) );
	}

	/**
	 * Return a clone ID by given a suffix.
	 *
	 * @param  string $base_id      Base ID.
	 * @param  string $clone_suffix Clone suffix.
	 * @return string
	 */
	protected function clone_id( $base_id, $clone_suffix ) {
		$id_data = Multidimensional::split( $base_id );

		if ( empty( $id_data['keys'] ) ) {
			return $base_id . '_' . $clone_suffix;
		}

		$editkey = array_pop( $id_data['keys'] );
		$id_data['keys'][] = $editkey . '_' . $clone_suffix;

		return Multidimensional::build( $id_data );
	}
}
