<?php

namespace WPLibs\Form\Core;

use WPLibs\Form\Contracts\Form;

interface Manager {
	/**
	 * Gets the form builder instance.
	 *
	 * @return \WPLibs\Form\Builder
	 */
	public function get_builder();

	/**
	 * Gets the form instance.
	 *
	 * @return \WPLibs\Form\Form|null
	 */
	public function get_form();

	/**
	 * Sets the form instance.
	 *
	 * @param \WPLibs\Form\Contracts\Form $form
	 */
	public function set_form( Form $form );

	/**
	 * Prepare panels, sections, and fields.
	 *
	 * For each, check if required related components exist,
	 * whether the user has the necessary capabilities,
	 * and sort by priority.
	 *
	 * @param \WPLibs\Form\Contracts\Form $form
	 * @return $this
	 */
	public function prepare( Form $form );

	/**
	 * Add a options section.
	 *
	 * @param Section|string $id                  The Section object, or Section ID.
	 * @param array          $args                {
	 *  Optional. Array of properties for the new Section object. Default empty array.
	 *  @type string         $title               Title of the section to show in UI.
	 *  @type string         $description         Description to show in the UI.
	 *  @type string         $panel               The panel this section belongs to (if any). Default empty.
	 *  @type string         $icon                Icon of the section.
	 *  @type int            $priority            Priority of the section, defining the display order of panels and sections.
	 *                                            Default 160.
	 *  @type string         $capability          Capability required for the section. Default 'edit_theme_options'
	 *  @type string|array   $theme_supports      Theme features required to support the section.
	 *  @type callable       $active_callback     Active callback.
	 * }
	 * @return Section          The instance of the section that was added.
	 */
	public function add_section( $id, $args = [] );

	/**
	 * Retrieve a options section.
	 *
	 * @param  string $id Section ID.
	 * @return Section|null The section, if set.
	 */
	public function get_section( $id );

	/**
	 * Remove a options section.
	 *
	 * @param string $id Section ID.
	 */
	public function remove_section( $id );

	/**
	 * Get the registered sections.
	 *
	 * @return Section[]
	 */
	public function sections();

	/**
	 * Get the registered containers.
	 *
	 * (Merge of sections and panels - if any)
	 *
	 * @return array|Section[]|Panel[]
	 */
	public function containers();
}
