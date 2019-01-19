<?php

namespace WPLibs\Form\Core;

interface Has_Panel {
	/**
	 * Add a options panel.
	 *
	 * @param Panel|string $id   The Panel object, or Panel ID.
	 * @param array        $args {
	 *  Optional. Array of properties for the new Panel object. Default empty array.
	 *
	 *  @type string       $title           Title of the panel to show in UI.
	 *  @type string       $description     Description to show in the UI.
	 *  @type string       $icon            Icon of the panel.
	 *  @type int          $priority        Priority of the panel, defining the display order of panels and sections.
	 *                                            Default 160.
	 *  @type string       $capability      Capability required for the panel. Default `edit_theme_options`
	 *  @type string|array $theme_supports  Theme features required to support the panel.
	 *  @type callable     $active_callback Active callback.
	 * }
	 * @return Panel The instance of the panel that was added.
	 */
	public function add_panel( $id, $args = [] );

	/**
	 * Retrieve a options panel.
	 *
	 * @param  string $id Panel ID to get.
	 * @return Panel|null Requested panel instance, if set.
	 */
	public function get_panel( $id );

	/**
	 * Remove a options panel.
	 *
	 * @param string $id Panel ID to remove.
	 */
	public function remove_panel( $id );

	/**
	 * Get the registered panels.
	 *
	 * @return Panel[]
	 */
	public function panels();
}
