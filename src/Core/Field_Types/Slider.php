<?php

namespace WPLibs\Form\Core\Field_Types;

class Slider extends Field_Type {
	/**
	 * {@inheritdoc}
	 */
	public function display( $field, $value, $builder ) {
		wp_enqueue_style( 'jquery-ui-slider-pips' );
		$field->add_js_dependencies( 'jquery-ui-slider-pips' );

		$field_args = $field->_data( 'args' );

		if ( isset( $field_args['pips'] ) && false === $field_args['pips'] ) {
			$pips_args = false;
		} else {
			/**
			 * Setting slider-pip pips.
			 *
			 * @see http://simeydotme.github.io/jQuery-ui-Slider-Pips/#options-pips
			 *
			 * @var array(
			 *      @type string|false $last    Determines the style of the first pip on the slider.
			 *                                  Value can be: "label", "pip" or false.
			 *      @type string|false $first   Determines the style of the final pip on the slider.
			 *                                  Value can be: "label", "pip" or false.
			 *      @type string|false $rest    Determines the style of all other pips on the slider.
			 *                                  Value can be: "label", "pip" or false.
			 *      @type number       $step    The step parameter will only generate every nth pip.
			 *      @type string       $prefix  Adds a string value before the pip label.
			 *      @type string       $suffix  Adds a string value after the pip label.
			 *      @type array|false  $labels  Will override the values of the pips with an array of given values.
			 *                                  eg: array( 'Monday', 'Tuesday', 'Wednesday', ...)
			 *                                  or array( 'first' => 'Monday', 'last' => 'Sunday' )
			 * )
			 */
			$pips_args = json_encode( wp_parse_args( $field->args( 'pips' ), [
				'last'   => 'label',
				'first'  => 'label',
				'rest'   => 'pip',
				// 'step'   => 1, // Note: Not set default step at here.
				'prefix' => '',
				'suffix' => '',
				'labels' => false,
			]));
		}

		if ( isset( $field_args['float'] ) && false === $field_args['float'] ) {
			$float_args = false;
		} else {
			/**
			 * Setting slider-pip float.
			 *
			 * @see http://simeydotme.github.io/jQuery-ui-Slider-Pips/#options-float
			 *
			 * @var array(
			 *      @type string       $prefix  Adds a string value before the float label.
			 *      @type string       $suffix  Adds a string value after the float label.
			 *      @type array|false  $labels  Will override the values of the floats with an array of given values.
			 *                                  eg: array( 'Monday', 'Tuesday', 'Wednesday', ...)
			 *                                  or array( 'first' => 'Monday', 'last' => 'Sunday' )
			 * )
			 */
			$float_args = json_encode( wp_parse_args( $field->args( 'float' ), [
				'prefix' => '',
				'suffix' => '',
				'labels' => false,
			]));
		}
		?>

		<div class="cmb2-slider"><div class="cmb2-ui-slider"></div></div>
		<?php
		print $builder->input( [ // WPCS: XSS OK.
			'type'       => 'hidden',
			'class'      => 'cmb2-ui-slider-input',
			'desc'       => '',
			'data-min'   => $field->args( 'min' ),
			'data-max'   => $field->args( 'max' ),
			'data-step'  => $field->args( 'step' ),
			'data-value' => is_numeric( $value ) ? $value : 0,
			'data-pips'  => esc_attr( $pips_args ),
			'data-float' => esc_attr( $float_args ),
		] );
		?>
		<span class="hidden"><span class="cmb2-ui-slider-preview"></span></span>
		<?php
	}
}
