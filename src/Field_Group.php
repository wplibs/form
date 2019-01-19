<?php

namespace WPLibs\Form;

use WPLibs\Form\Helper\Dependency;

class Field_Group extends Field {
	/**
	 * Store the fields instance.
	 *
	 * @var \WPLibs\Form\Contracts\Field[]
	 */
	public $fields = [];

	/**
	 * Setup group for a new row.
	 *
	 * @return void
	 */
	public function prepare_new_row() {
		$this->field = [];

		$this->setup_fields();

		$values = $this->get_value();

		Dependency::check( $this->fields,
			isset( $values[ $this->index ] ) ? (array) $values[ $this->index ] : []
		);
	}

	/**
	 * Setup field in the group.
	 *
	 * @return void
	 */
	protected function setup_fields() {
		foreach ( $this->prop( 'fields' ) as $args ) {
			$field = $this->builder
				->get_field( $args, $this )
				->set_option( 'context', $this->args( 'context' ) )
				->initialize();

			if ( ! $field->check_capabilities() ) {
				continue;
			}

			if ( 'hidden' === $args['type'] ) {
				$this->builder->add_hidden_field( $args, $this );
				continue;
			}

			$this->fields[ $field->get_id() ] = $field;
		}
	}
}
