<?php

namespace WPLibs\Form\Helper;

use WPLibs\Rules\Rule;
use WPLibs\Rules\Variable;

class Dependency {
	/* Constants */
	const LOGICAL_OR  = 'or';
	const LOGICAL_AND = 'and';

	/**
	 * The data store.
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * The supported logicals.
	 *
	 * @var array
	 */
	protected static $logicals = [
		'or'  => \Ruler\Operator\LogicalOr::class,
		'and' => \Ruler\Operator\LogicalAnd::class,
	];

	/**
	 * The operators that supported.
	 *
	 * @var array
	 */
	protected static $operators = [
		// Compare operators.
		'equal',
		'not_equal',
		'less_than',
		'less_than_or_equal',
		'greater_than',
		'greater_than_or_equal',
		'between',
		'not_between',
		'contains',
		'not_contains',
		'begins_with',
		'not_begins_with',
		'ends_with',
		'not_ends_with',
		'in',
		'not_in',

		// Check operators.
		'is_empty',
		'is_not_empty',
	];

	/**
	 * Check the fields dependencies.
	 *
	 * @param \WPLibs\Form\Contracts\Field[] $fields
	 * @param array                          $data
	 * @return void
	 */
	public static function check( array $fields, array $data ) {
		$dependency = new static( $data );

		foreach ( $fields as $field ) {
			if ( ! $field->get_option( 'deps' ) ) {
				continue;
			}

			$satisfied = $dependency->apply(
				$deps = $field->get_option( 'deps' )
			);

			$field->set_option( 'row_attributes',
				$field->get_option( 'row_attributes' ) + [
					'data-deps'      => esc_attr( json_encode( $deps, JSON_HEX_APOS ) ),
					'data-satisfied' => $satisfied ? 'true' : 'false',
				]
			);

			$field->set_option( 'active', $satisfied );
		}
	}

	/**
	 * Constructor.
	 *
	 * @param array $data
	 */
	public function __construct( array $data ) {
		$this->data = $data;
	}

	/**
	 * Check rules is satisfied the conditions.
	 *
	 * @param  array $rules
	 * @return bool
	 */
	public function apply( $rules ) {
		$satisfied = true;

		if ( empty( $rules ) || ! is_array( $rules ) ) {
			return $satisfied;
		}

		// If no any valid rules, consider as satisfied.
		if ( ! is_null( $rule = $this->get_rule( $rules ) ) ) {
			return $rule->apply( $this->data );
		}

		return $satisfied;
	}

	/**
	 * Gets the rule.
	 *
	 * @param  array $rules
	 * @return \WPLibs\Rules\Rule|null
	 */
	public function get_rule( array $rules ) {
		$logical = static::LOGICAL_AND;

		if ( static::is_group_rules( $rules ) ) {
			list( $logical, $rules ) = $this->parse_group_logical( $rules );
		}

		return $this->create_rule( $rules, $logical );
	}

	/**
	 * Create new rule based on an array of rules.
	 *
	 * @param  array  $rules
	 * @param  string $logical
	 * @return \WPLibs\Rules\Rule|null
	 */
	public function create_rule( array $rules, $logical = 'and' ) {
		if ( empty( $rules ) ) {
			return null;
		}

		if ( static::is_proposition( $rules ) ) {
			$rules = [ $rules ];
		}

		if ( ! $props = $this->build_propositions( $rules ) ) {
			return null;
		}

		// Correct the "or" logical in case we have only one prop.
		if ( static::LOGICAL_OR === $logical && count( $props ) === 1 ) {
			$logical = static::LOGICAL_AND;
		}

		$logical = static::$logicals[ $logical ];

		return new Rule( new $logical( $props ) );
	}

	/**
	 * Build propositions based given array rules.
	 *
	 * @param  array $rules
	 * @return array
	 */
	protected function build_propositions( array $rules ) {
		$props = [];

		foreach ( $rules as $rule ) {
			if ( ! is_array( $rule ) || ! static::is_proposition( $rule ) ) {
				continue;
			}

			try {
				$props[] = $this->create_proposition( ... $rule );
			} catch ( \Exception $e ) {
				continue;
			}
		}

		return array_filter( $props );
	}

	/**
	 * Create a rule proposition.
	 *
	 * @param  string $parent_name
	 * @param  string $operator
	 * @param  mixed  $check_value
	 * @return mixed
	 */
	protected function create_proposition( $parent_name, $operator = null, $check_value = null ) {
		list( $operator, $check_value ) = $this->normalize_proposition( $operator, $check_value );

		// Allow check value can be a reference value of a variable in the context.
		// This can be done with: ['some-check', '!=', '@another-check'].
		if ( is_string( $check_value ) && 0 === strpos( $check_value, '@' ) ) {
			$check_value = $this->new_variable( substr( $check_value, 1 ) );
		}

		$vars = $this->new_variable( $parent_name );

		// The "is_" operator can not have check value.
		if ( 0 === strpos( $operator, 'is_' ) ) {
			return $vars->{$operator}();
		}

		return $vars->{$operator}( $check_value );
	}

	/**
	 * Normalize the proposition.
	 *
	 * @param  string $operator
	 * @param  mixed  $check_value
	 * @return array
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function normalize_proposition( $operator = null, $check_value = null ) {
		// Here we will make some assumptions about the operator. If only $operator are
		// passed to the method, we will assume that the operator is an equals sign
		// and keep going. Otherwise, we'll require the operator to be passed in.
		if ( is_null( $check_value ) && 0 !== strpos( $operator, 'is_' ) ) {
			list( $check_value, $operator ) = [ $operator, '=' ];
		}

		// Some operator like "=", "<=", etc. need to normalize to valid name.
		$operator = $this->normalize_operator( $operator );

		if ( ! in_array( $operator, static::$operators ) ) {
			throw new \InvalidArgumentException( "The [{$operator}] operator is not supported." );
		}

		// Wrap the value as array in case "in" or "not_in".
		if ( is_string( $check_value ) && in_array( $operator, [ 'in', 'not_in' ] ) ) {
			$check_value = false !== strpos( $check_value, ',' )
				? str_getcsv( $check_value )
				: [ $check_value ];
		}

		return [ $operator, $check_value ];
	}

	/**
	 * Convert some match operators to correct name.
	 *
	 * @param  string $operator
	 * @return string
	 */
	protected function normalize_operator( $operator ) {
		switch ( $operator ) {
			case '=':
			case '==':
				return 'equal';
			case '!=':
			case '!==':
				return 'not_equal';
			case '<':
			case 'lt':
				return 'less_than';
			case '<=':
			case 'lte':
				return 'less_than_or_equal';
			case '>':
			case 'gt':
				return 'greater_than';
			case '>=':
			case 'gte':
				return 'greater_than_or_equal';
			case 'is_truthy':
				return 'is_not_empty';
			default:
				return $operator;
		}
	}

	/**
	 * Create new variable.
	 *
	 * @param string $name
	 * @return \WPLibs\Rules\Variable
	 */
	protected function new_variable( $name ) {
		return new Variable( $name );
	}

	/**
	 * Return the logical of given rules group.
	 *
	 * @param  array $array
	 * @return array
	 */
	protected function parse_group_logical( array $array ) {
		/* @noinspection LoopWhichDoesNotLoopInspection */
		foreach ( $array as $logical => $rules ) {
			return [ strtolower( $logical ), $rules ];
		}
	}

	/**
	 * Determines if given a single rule array.
	 *
	 * @param  array $array
	 * @return bool
	 */
	public static function is_proposition( array $array ) {
		return count( $array ) >= 2 && is_string( $array[0] );
	}

	/**
	 * Determines if given a group rules array.
	 *
	 * @param  array $array
	 * @return bool
	 */
	public static function is_group_rules( array $array ) {
		if ( count( $array ) !== 1 ) {
			return false;
		}

		$key = strtolower( array_keys( $array )[0] );

		return array_key_exists( $key, static::$logicals );
	}
}
