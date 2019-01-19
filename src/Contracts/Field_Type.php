<?php

namespace WPLibs\Form\Contracts;

interface Field_Type {
	/**
	 * Display the field type contents.
	 *
	 * @param  \WPLibs\Form\Field        $field
	 * @param  mixed                     $value
	 * @param  \WPLibs\Form\Field_Render $builder
	 * @return void
	 */
	public function display( $field, $value, $builder );

	/**
	 * Sanitization the value.
	 *
	 * @param  mixed $value
	 * @return mixed
	 */
	public function sanitization( $value );
}
