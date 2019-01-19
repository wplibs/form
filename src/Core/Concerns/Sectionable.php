<?php

namespace WPLibs\Form\Core\Concerns;

trait Sectionable {
	/**
	 * The options manager instance.
	 *
	 * @var \WPLibs\Form\Core\Manager
	 */
	protected $manager;

	/**
	 * Unique identifier.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Title of the section to show in UI.
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * Description to show in the UI.
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Tab icon to show in the UI.
	 *
	 * @var string
	 */
	public $icon = '';

	/**
	 * Priority of the section which informs load order of sections.
	 *
	 * @var integer
	 */
	public $priority = 160;

	/**
	 * Capability required for the section.
	 *
	 * @var string
	 */
	public $capability = 'edit_theme_options';

	/**
	 * Theme feature support for the tab.
	 *
	 * @var string|array
	 */
	public $theme_supports = '';

	/**
	 *  Determines if the current section is showing by default.
	 *
	 * @var callable
	 */
	public $active_callback;

	/**
	 * The optional options.
	 *
	 * @var array
	 */
	public $options = [];

	/**
	 * Check whether section is active to current screen.
	 *
	 * @return bool
	 */
	public function active() {
		// Default to showing the section.
		$show = true;

		// Use the callback to determine showing the section, if it exists.
		if ( is_callable( $this->active_callback ) ) {
			$show = call_user_func( $this->active_callback, $this );
		}

		return $show;
	}

	/**
	 * Checks required user capabilities and whether the theme has the
	 * feature support required by the section.
	 *
	 * @return bool
	 */
	public function check_capabilities() {
		if ( $this->capability && ! call_user_func_array( 'current_user_can', (array) $this->capability ) ) {
			return false;
		}

		if ( $this->theme_supports && ! call_user_func_array( 'current_theme_supports', (array) $this->theme_supports ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns the section/panel ID.
	 *
	 * @return string
	 */
	public function uniqid() {
		return $this->manager->get_builder()->get_id() . '-' . $this->id;
	}

	/**
	 * Show the section/panel icon as HTML.
	 *
	 * @return string
	 */
	public function icon() {
		if ( ! $this->icon ) {
			return '';
		}

		if ( 0 === strpos( $this->icon, 'dashicons-' ) ) {
			$icon_html = '<span class="wplibs-menu-icon dashicons ' . esc_attr( $this->icon ) . '"></span>';
		} elseif ( 0 === strpos( $this->icon, 'suru-icon-' ) ) {
			$icon_html = '<span class="wplibs-menu-icon suru-icon ' . esc_attr( $this->icon ) . '"></span>';
		} elseif ( preg_match( '/^(http.*\.)(jpe?g|png|[tg]iff?|svg)$/', $this->icon ) ) {
			$icon_html = '<span class="wplibs-menu-icon" style="background-image:url(\'' . esc_attr( $this->icon ) . '\'"></span>';
		} else {
			$icon_html = '<span class="wplibs-menu-icon ' . esc_attr( $this->icon ) . '"></span>';
		}

		return $icon_html;
	}
}
