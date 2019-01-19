<?php

namespace WPLibs\Form\Core\Concerns;

use WPLibs\Form\Core\Panel;

trait With_Panels {
	/**
	 * Registered instances of Panel.
	 *
	 * @var \WPLibs\Form\Core\Panel[]
	 */
	protected $panels = [];

	/**
	 * {@inheritdoc}
	 */
	public function panels() {
		return $this->panels;
	}

	/**
	 * {@inheritdoc}
	 */
	public function add_panel( $id, $args = [] ) {
		$panel = $id instanceof Panel ? $id : new Panel( $this, $id, $args );

		$this->panels[ $panel->id ] = $panel;

		return $panel;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_panel( $id ) {
		return isset( $this->panels[ $id ] ) ? $this->panels[ $id ] : null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove_panel( $id ) {
		unset( $this->panels[ $id ] );
	}
}
