<?php

namespace WPLibs\Form\Core\Concerns;

use Illuminate\Support\Arr;
use WPLibs\Form\Core\Section;
use WPLibs\Form\Core\Has_Panel;
use WPLibs\Form\Contracts\Form;

trait Configable {
	/**
	 * Registered instances of Section.
	 *
	 * @var \WPLibs\Form\Core\Section[]
	 */
	protected $sections = [];

	/**
	 * Sorted top-level instances of Panel and Section.
	 *
	 * @var array
	 */
	protected $containers = [];

	/**
	 * Store the form instance has been created from the builder.
	 *
	 * @var \WPLibs\Form\Contracts\Form
	 */
	protected $form;

	/**
	 * {@inheritdoc}
	 */
	public function sections() {
		return $this->sections;
	}

	/**
	 * {@inheritdoc}
	 */
	public function containers() {
		return $this->containers;
	}

	/**
	 * {@inheritdoc}
	 */
	public function add_section( $id, $args = [] ) {
		$section = $id instanceof Section ? $id : new Section( $this, $id, $args );

		$this->sections[ $section->id ] = $section;

		return $section;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_section( $id ) {
		return isset( $this->sections[ $id ] ) ? $this->sections[ $id ] : null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove_section( $id ) {
		unset( $this->sections[ $id ] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_form() {
		return $this->form;
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_form( Form $form ) {
		$this->form = $form;
	}

	/**
	 * {@inheritdoc}
	 */
	public function prepare( Form $form ) {
		$this->set_form( $form );

		$this->prepare_fields( $form );

		$this->prepare_sections();

		$containers = $this->sections;

		if ( $this instanceof Has_Panel ) {
			$this->prepare_panels();
			$containers = array_merge( $this->panels(), $containers );
		}

		// Sort panels and top-level sections together.
		$this->containers = wp_list_sort( $containers, [
			'priority'        => 'ASC',
			'instance_number' => 'ASC',
		], 'ASC', true );

		$this->apply_current_section( $form );

		return $this;
	}

	/**
	 * Set the current section.
	 *
	 * @param \WPLibs\Form\Contracts\Form $form
	 */
	protected function apply_current_section( Form $form ) {
		if ( count( $this->sections ) === 0 ) {
			return;
		}

		// Make sure we don't have any active section before.
		foreach ( $this->sections as $section ) {
			$section->options['active'] = false;
		}

		$current = $this->current_section_in_cookie( $form->get_id() );

		if ( ! $current && $form->get_config()->has_option( 'active_section' ) ) {
			$current = $form->get_config()->get_option( 'active_section' );
		}

		// Use first section as fallback.
		if ( ! $current || ! isset( $this->sections[ $current ] ) ) {
			$current = Arr::first( array_keys( $this->sections ) );
		}

		if ( $section = $this->get_section( $current ) ) {
			$section->options['active'] = true;
		}
	}

	/**
	 * Resolve current section from the cookie.
	 *
	 * @return string|null
	 */
	protected function current_section_in_cookie( $id ) {
		if ( empty( $_COOKIE['_wplibs_current_sections'] ) ) {
			return null;
		}

		$currents = json_decode(
			sanitize_text_field( wp_unslash( $_COOKIE['_wplibs_current_sections'] ) ), true
		);

		if ( array_key_exists( $id, $currents ) ) {
			return $currents[ $id ];
		}

		return null;
	}

	/**
	 * Maps fields into their section.
	 *
	 * @param \WPLibs\Form\Contracts\Form $form
	 */
	protected function prepare_fields( Form $form ) {
		foreach ( $form->all() as $field ) {
			/* @var $field \WPLibs\Form\Field */
			if ( ! $field->should_show() ) {
				return;
			}

			$_section = $field->get_option( 'section' );

			if ( ! $_section || ! isset( $this->sections[ $_section ] ) ) {
				continue;
			}

			$this->sections[ $_section ]->fields[] = $field;
		}
	}

	/**
	 * Prepare the sections.
	 *
	 * @return void
	 */
	protected function prepare_sections() {
		$this->sections = wp_list_sort( $this->sections, [
			'priority'        => 'ASC',
			'instance_number' => 'ASC',
		], 'ASC', true );

		$sections = [];

		foreach ( $this->sections as $section ) {
			if ( ! $section->check_capabilities() ) {
				continue;
			}

			// Top-level section.
			if ( ! $this instanceof Has_Panel || ! $section->panel ) {
				$sections[ $section->id ] = $section;
				continue;
			}

			// This section belongs to a panel.
			if ( isset( $this->panels [ $section->panel ] ) ) {
				$this->panels[ $section->panel ]->sections[ $section->id ] = $section;
			}
		}

		// Update the sections.
		$this->sections = $sections;
	}

	/**
	 * Prepare the panels.
	 *
	 * @return void
	 */
	protected function prepare_panels() {
		if ( ! $this instanceof Has_Panel ) {
			return;
		}

		// Prepare panels.
		$this->panels = wp_list_sort( $this->panels(), [
			'priority'        => 'ASC',
			'instance_number' => 'ASC',
		], 'ASC', true );

		$panels = [];

		foreach ( $this->panels() as $panel ) {
			if ( ! $panel->check_capabilities() ) {
				continue;
			}

			$panel->sections = wp_list_sort( $panel->sections, [
				'priority'        => 'ASC',
				'instance_number' => 'ASC',
			], 'ASC', true );

			$panels[ $panel->id ] = $panel;
		}

		// Set the panels.
		$this->panels = $panels;
	}
}
