<?php

namespace WPLibs\Form;

use WPLibs\Form\Contracts\Fields_Stack;

class Group_Builder implements Fields_Stack {
	/**
	 * The builder instance.
	 *
	 * @var \WPLibs\Form\Builder
	 */
	protected $builder;

	/**
	 * Group ID.
	 *
	 * @var string
	 */
	public $group_id;

	/**
	 * Constructor of class.
	 *
	 * @param Builder $builder  The builder instance.
	 * @param string  $group_id Group ID.
	 */
	public function __construct( Builder $builder, $group_id ) {
		$this->builder  = $builder;
		$this->group_id = $group_id;
	}

	/**
	 * {@inheritdoc}
	 */
	public function add_field( array $field, $position = 0 ) {
		return $this->builder->add_group_field( $this->group_id, $field, $position );
	}

	/**
	 * {@inheritdoc}
	 */
	public function add_group( array $args, $position = 0 ) {
		throw new \LogicException( 'Cannot add a group field from a group field' );
	}
}
