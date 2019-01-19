<?php

namespace WPLibs\Form\Core;

class Panel {
	use Concerns\Sectionable;

	/**
	 * Incremented with each new class instantiation, then stored in $instance_number.
	 *
	 * Used when sorting two instances whose priorities are equal.
	 *
	 * @var int
	 */
	protected static $instance_count = 0;

	/**
	 * Order in which this instance was created in relation to other instances.
	 *
	 * @var int
	 */
	public $instance_number;

	/**
	 * Store the sections for this panel.
	 *
	 * @var Section[]
	 */
	public $sections = [];

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 * @param Manager $manager The options manager instance.
	 * @param string  $id      An specific ID of the section.
	 * @param array   $args    Section arguments.
	 */
	public function __construct( Manager $manager, $id, $args = [] ) {
		$keys = array_keys( get_object_vars( $this ) );

		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		$this->id      = $id;
		$this->manager = $manager;

		++static::$instance_count;
		$this->instance_number = static::$instance_count;

		// Users cannot customize the $sections array.
		$this->sections = [];
	}

	/**
	 * Add a section into this panel.
	 *
	 * @param string $id   The Section object, or Section ID.
	 * @param array  $args The section properties.
	 *
	 * @return Section
	 */
	public function add_section( $id, $args = [] ) {
		$section = $this->manager->add_section( $id, $args );

		$section->panel = $this->id;

		return $section;
	}

	/**
	 * Returns the nav-link.
	 *
	 * @return void
	 */
	public function navlink() {
		foreach ( $this->sections as $section ) {
			$section->navlink();
		}
	}

	/**
	 * Output the panel content.
	 *
	 * @return void
	 */
	public function output() {
		foreach ( $this->sections as $section ) {
			$section->output();
		}
	}

	/**
	 * Gather the parameters passed to client JavaScript via JSON.
	 *
	 * @return array
	 */
	public function json() {
		$array = wp_array_slice_assoc( get_object_vars( $this ),
			[ 'id', 'description', 'priority', 'options' ]
		);

		$array['title']    = html_entity_decode( $this->title, ENT_QUOTES, get_bloginfo( 'charset' ) );
		$array['active']   = $this->active();
		$array['instance'] = $this->instance_number;

		return $array;
	}
}
