<?php

namespace WPLibs\Form\Helper;

use WPLibs\Form\Contracts\Field;

class Rules_Parser {
	/**
	 * The field instance.
	 *
	 * @var \WPLibs\Form\Contracts\Field
	 */
	protected $field;

	/**
	 * The rule parser.
	 *
	 * @var \WPLibs\Form\Helper\Validation_Parser
	 */
	protected $parser;

	/**
	 * Constructor.
	 *
	 * @param \WPLibs\Form\Contracts\Field $field
	 */
	public function __construct( Field $field ) {
		$this->field  = $field;
		$this->parser = new Validation_Parser;
	}

	/**
	 * Parse a rule for an input into an array of attributes.
	 *
	 * @param  string|array $rules
	 * @return array
	 */
	public function parse( $rules ) {
		$attributes = [];

		$rules = $this->parser->parse( $rules );

		foreach ( $rules as list( $rule, $parameters ) ) {
			$rule = strtolower( $rule );

			if ( method_exists( $this, $rule ) ) {
				$attributes += $this->$rule( $parameters );
			}
		}

		return $attributes;
	}

	/**
	 * Check that a checkbox is accepted. Needs yes, on, 1, or true as value.
	 *
	 * @return array
	 */
	protected function accepted() {
		return [
			'required' => 'required',
			'title'    => $this->get_title( 'accepted' ),
		];
	}

	/**
	 * Check that the field is required.
	 *
	 * @return array
	 */
	protected function required() {
		return [ 'required' => 'required' ];
	}

	/**
	 * Check that the input only contains alpha.
	 *
	 * @see \Valitron\Validator::validateAlpha()
	 *
	 * @return array
	 */
	protected function alpha() {
		return [
			'pattern' => '[a-zA-Z]+',
			'title'   => $this->get_title( 'alpha' ),
		];
	}

	/**
	 * Check if the input contains only alpha and num.
	 *
	 * @see \Valitron\Validator::validateAlphaNum()
	 *
	 * @return array
	 */
	protected function alphanum() {
		return [
			'pattern' => '[a-zA-Z0-9]+',
			'title'   => $this->get_title( 'alpha_num' ),
		];
	}

	/**
	 * Check if the input contains only alpha, num and dash.
	 *
	 * @see \Valitron\Validator::validateSlug()
	 *
	 * @return array
	 */
	protected function slug() {
		return [
			'pattern' => '[a-zA-Z0-9_\-]+',
			'title'   => $this->get_title( 'slug' ),
		];
	}

	/**
	 * Check if the field is an integer value.
	 *
	 * @see \Valitron\Validator::validateInteger()
	 *
	 * @return array
	 */
	protected function integer() {
		if ( $this->is_numeric() ) {
			return [ 'step' => 1 ];
		}

		return [
			'pattern' => '\d+',
			'title'   => $this->get_title( 'integer' ),
		];
	}

	/**
	 * Check that a field is numeric.
	 *
	 * @return array
	 */
	protected function numeric() {
		if ( $this->is_numeric() ) {
			return [ 'step' => 'any' ];
		}

		return [
			'pattern' => '[-+]?[0-9]*[.,]?[0-9]+',
			'title'   => $this->get_title( 'numeric' ),
		];
	}

	/**
	 * Check that a value is either 0 or 1, so it can be parsed as bool.
	 *
	 * @return array
	 */
	protected function boolean() {
		return [
			'pattern' => '0|1',
			'title'   => $this->get_title( 'boolean' ),
		];
	}

	/**
	 * For numbers, set the minimum value.
	 *
	 * For strings, set the minimum number of characters.
	 *
	 * @param  string $params
	 * @return array
	 */
	protected function min( $params ) {
		$min = $params[0];

		if ( $this->is_numeric() ) {
			return [ 'min' => $min ];
		}

		return [
			'minlength' => $min,
		];
	}

	/**
	 * For numbers, set the max value.
	 *
	 * For strings, set the max number of characters.
	 *
	 * @param  array $param
	 * @return array
	 */
	protected function max( $param ) {
		$max = $param[0];

		if ( $this->is_numeric() ) {
			return [ 'max' => $max ];
		}

		return [ 'maxlength' => $max ];
	}

	/**
	 * For number/range inputs, check if the number is between the values.
	 *
	 * For strings, check the length of the string.
	 *
	 * @param  array $params
	 * @return array
	 */
	protected function between( $params ) {
		list ( $min, $max ) = $params;

		if ( $this->is_numeric() ) {
			return [
				'min' => $min,
				'max' => $max,
			];
		}

		return [
			'minlength' => $min,
			'maxlength' => $max,
		];
	}

	/**
	 * For numbers: Check an exact value.
	 *
	 * For strings: Check the length of the string.
	 *
	 * @param  array $params
	 * @return array
	 */
	protected function length( $params ) {
		$size = $params[0];

		if ( $this->is_numeric() ) {
			return [
				'min'   => $size,
				'max'   => $size,
				'title' => $this->get_title( 'size.numeric', compact( 'size' ) ),
			];
		}

		return [
			'pattern' => '.{' . $size . '}',
			'title'   => $this->get_title( 'size.string', compact( 'size' ) ),
		];
	}

	/**
	 * Check if the value is one of the give 'in' rule values
	 * by creating a matching pattern.
	 *
	 * @param  array $params
	 * @return array
	 */
	protected function in( $params ) {
		return [
			'pattern' => implode( '|', is_array( $params[0] ) ? $params[0] : [] ),
			'title'   => $this->get_title( 'in' ),
		];
	}

	/**
	 * Check if the value is not one of the 'not_in' rule values
	 * by creating a pattern value.
	 *
	 * @param  array $params
	 * @return array
	 */
	protected function notin( $params ) {
		return [
			'pattern' => '(?:(?!^' . implode( '$|^', $params[0] ) . '$).)*',
			'title'   => $this->get_title( 'not_in' ),
		];
	}

	/**
	 * Set the 'min' attribute on a date/datetime/datetime-local field,
	 * based on the 'before' validation.
	 *
	 * @param  array $params
	 * @return array
	 */
	protected function dateafter( $params ) {
		if ( $date = $this->get_date_attribute( $params[0] ) ) {
			return [ 'min' => $date ];
		}

		return [];
	}

	/**
	 * Set the 'min' attribute on a date/datetime/datetime-local field,
	 * based on the 'before' validation.
	 *
	 * @param  array $params
	 * @return array
	 */
	protected function datebefore( $params ) {
		if ( $date = $this->get_date_attribute( $params[0] ) ) {
			return [ 'max' => $date ];
		}

		return [];
	}

	/**
	 * Get the title, used for validating a rule.
	 *
	 * @param  string $rule
	 * @param  array  $params
	 * @return string
	 */
	protected function get_title( $rule, $params = [] ) {
		return $this->field->get_option( 'title' );
	}

	/**
	 * Check if the field is one of certain types.
	 *
	 * @param  string|array $types
	 * @return bool
	 */
	protected function is_type( $types ) {
		return in_array( $this->field->get_attribute( 'type' ), (array) $types );
	}

	/**
	 * Check if the field is numeric.
	 *
	 * @return bool
	 */
	protected function is_numeric() {
		return $this->is_type( [ 'number', 'range' ] );
	}

	/**
	 * Format a date to the correct format, based on the current field.
	 *
	 * @param  string $string
	 * @return bool|string
	 */
	protected function get_date_attribute( $string ) {
		$format = 'Y-m-d';

		if ( $this->is_type( [ 'datetime', 'datetime-local' ] ) ) {
			$format .= '\TH:i:s';
		}

		return date( $format, strtotime( $string ) );
	}
}
