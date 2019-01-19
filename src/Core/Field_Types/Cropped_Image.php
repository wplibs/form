<?php

namespace WPLibs\Form\Core\Field_Types;

use Illuminate\Support\Arr;

class Cropped_Image extends Media {
	/**
	 * {@inheritdoc}
	 */
	public function sanitization( $value ) {
		return $value;
	}

	public function js_data( $field ) {
		$json = parent::js_data( $field );

		$json = array_merge( $json, $this->default_settings() );

		return $json;
	}

	public function default_settings() {
		return [
			'width' => 150,
			'height' => 150,
			'flex_width' => false,
			'flex_height' => false,
		];
	}
}
