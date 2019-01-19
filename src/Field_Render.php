<?php

namespace WPLibs\Form;

use WPLibs\Form\Helper\Html_Form;

class Field_Render extends \CMB2_Types {
	/**
	 * Store the html form builder.
	 *
	 * @var \WPLibs\Form\Helper\Html_Form
	 */
	protected $html_builder;

	/**
	 * Gets the html form builder helper.
	 *
	 * @return \WPLibs\Form\Helper\Html_Form
	 */
	public function get_html_builder() {
		if ( ! $this->html_builder ) {
			$this->html_builder = new Html_Form();
		}

		$this->html_builder->global_attributes( array_merge( (array) $this->field->args( 'attributes' ), [
			'data-hash'     => $this->field->hash_id(),
			'data-iterator' => $this->field->is_repeatable() ? $this->iterator : null,
		] ) );

		return $this->html_builder;
	}

	/**
	 * Generate the ID attribute.
	 *
	 * @param  string $suffix
	 * @return string
	 */
	public function _input_id( $suffix = '' ) {
		return $this->field->id() . $suffix . ( $this->field->is_repeatable() ? '_' . $this->iterator : '' );
	}
}
