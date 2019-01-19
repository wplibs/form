<?php

namespace WPLibs\Form\Core;

use WPLibs\Form\Helper\Utils;
use WPLibs\Form\Contracts\Fields_Stack;

class Section implements Fields_Stack {
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
	 * Panel in which to show the section, making it a sub-section.
	 *
	 * @var string
	 */
	public $panel = '';

	/**
	 * Store fields for this section.
	 *
	 * @var \WPLibs\Form\Field[]
	 */
	public $fields = [];

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

		// Users cannot customize the fields array.
		$this->fields = [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function add_field( array $args, $position = 0 ) {
		// Alway belong to this section.
		$args['section'] = $this->id;

		$this->manager->get_builder()->add_field( $args, $position );

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function add_group( array $args, $position = 0 ) {
		$args['section'] = $this->id;

		return $this->manager->get_builder()->add_group( $args, $position );
	}

	/**
	 * Returns the nav-link of the section.
	 *
	 * @return string
	 */
	public function navlink() {
		$attributes = [
			'role'          => 'tab',
			'aria-controls' => $this->uniqid(),
			'data-section'  => $this->id,
			'data-open'     => ! empty( $this->options['active'] ) ? 'true' : 'false',
		];

		return sprintf( '<li%1$s><a href="#" aria-expanded="false">%3$s <span>%2$s</span></a></li>',
			Utils::build_html_attributes( $attributes ),
			esc_html( $this->title ?: $this->id ),
			$this->icon()
		);
	}

	/**
	 * Output the section content.
	 *
	 * @return void
	 */
	public function output() {
		if ( ! $form = $this->manager->get_form() ) {
			_doing_it_wrong( __FUNCTION__, 'Cannot output the section until the form is created', null );
			return;
		}

		$attributes = [
			'role'        => 'tabpanel',
			'id'          => $this->uniqid(),
			'aria-hidden' => ! empty( $this->options['active'] ) ? 'false' : 'true',
		];

		echo '<section' . Utils::build_html_attributes( $attributes ) . '>'; // WPCS: XSS OK.

		foreach ( $this->fields as $field ) {
			$form->show( $field );
		}

		echo '</section>';
	}

	/**
	 * Gather the parameters passed to client JavaScript via JSON.
	 *
	 * @return array
	 */
	public function json() {
		$array = wp_array_slice_assoc( get_object_vars( $this ),
			[ 'id', 'description', 'priority', 'panel', 'options' ]
		);

		$array['title']    = html_entity_decode( $this->title, ENT_QUOTES, get_bloginfo( 'charset' ) );
		$array['active']   = $this->active();
		$array['instance'] = $this->instance_number;

		return $array;
	}
}
