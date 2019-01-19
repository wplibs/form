<?php

namespace WPLibs\Form\Core\Concerns;

trait Displayable {
	/**
	 * Output the navigation.
	 *
	 * @param string $nav_class
	 */
	public function nav( $nav_class = '' ) {
		echo '<ul role="tablist" class="wplibs-box__nav ' . esc_attr( $nav_class ) . '">';

		foreach ( $this->sections() as $section ) {
			echo $section->navlink(); // @WPCS: XSS OK.
		}

		echo '</ul><!-- /.wplibs-box__nav -->';
	}

	/**
	 * Output the main sections and fields.
	 *
	 * @param string $main_class
	 */
	public function main( $main_class = '' ) {
		if ( count( $containers = $this->containers() ) > 0 ) {
			echo '<main class="wplibs-box__container ' . esc_attr( $main_class ) . '">';
			$this->show_sections( $containers );
			echo '</main>';
		} else {
			$this->show_fields( $this->get_form()->all() );
		}
	}

	/**
	 * Show the fields.
	 *
	 * @param \WPLibs\Form\Field[] $fields
	 */
	public function show_fields( $fields ) {
		foreach ( $fields as $field ) {
			$field->display();
		}
	}

	/**
	 * Show the sections.
	 *
	 * @param \WPLibs\Form\Core\Section[] $sections
	 */
	public function show_sections( $sections ) {
		foreach ( $sections as $section ) {
			$section->output();
		}
	}
}
