<?php

namespace WPLibs\Form\Contracts;

interface Fields_Stack {
	/**
	 * Add a field into the stack.
	 *
	 * @param  array $args     The field config.
	 * @param  int   $position Position of field.
	 * @return $this
	 */
	public function add_field( array $args, $position = 0 );

	/**
	 * Add a group field into the stack.
	 *
	 * @param  array $args
	 * @param  int   $position
	 * @return \WPLibs\Form\Group_Builder
	 */
	public function add_group( array $args, $position = 0 );
}
