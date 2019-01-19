<?php

namespace WPLibs\Form\Box;

use WPLibs\Form\Core\Manager;
use WPLibs\Form\Contracts\Fields_Stack;
use WPLibs\Form\Core\Concerns\Configable;
use WPLibs\Form\Core\Concerns\Displayable;
use Illuminate\Support\Arr;

class Box implements Manager, Fields_Stack {
	use Configable, Displayable;

	/**
	 * The core type identifier.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * The title of the box.
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * The object types to display the box.
	 *
	 * Post type slug, or 'user', 'term', 'comment'
	 *
	 * @var string|array
	 */
	public $object_types = 'post';

	/**
	 * The taxonomies in case object_types is 'term'.
	 *
	 * @var array
	 */
	public $taxonomies = [];

	/**
	 * Context.
	 *
	 * @var string
	 */
	public $context = 'normal';

	/**
	 * Priority.
	 *
	 * @var string
	 */
	public $priority = 'default';

	/**
	 * Box layout.
	 *
	 * @var string
	 */
	public $layout = 'vertical'; // [vertical, horizontal]

	/**
	 * Box properties.
	 *
	 * @var array
	 */
	public $properties = [];

	/**
	 * The form builder instance.
	 *
	 * @var Builder
	 */
	protected $builder;

	/**
	 * Store the all instances.
	 *
	 * @var static[]
	 */
	protected static $instances = [];

	/**
	 * Returns all box instances.
	 *
	 * @return static[]
	 */
	public static function get_instances() {
		return static::$instances;
	}

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 * @param string $id   An specific ID of the metabox.
	 * @param array  $args Metabox arguments.
	 */
	public function __construct( $id, $args = [] ) {
		$keys = Arr::except( array_keys( get_object_vars( $this ) ),
			[ 'id', 'builder', '_form' ]
		);

		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		$this->id = $id;

		// Users cannot customize the $sections array.
		$this->sections   = [];
		$this->containers = [];

		static::$instances[ $this->id ] = $this;
	}

	/**
	 * Register the box.
	 *
	 * @return void
	 */
	public function register() {
		$builder = $this->get_builder();

		// Sets the builder configures.
		foreach ( $this->get_builder_config() as $key => $value ) {
			$builder->set_option( $key, $value );
		}

		( new Loader( $builder ) )->fire();
	}

	/**
	 * {@inheritdoc}
	 */
	public function add_field( array $args, $position = 0 ) {
		$this->get_builder()->add_field( $args, $position );

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function add_group( array $args, $position = 0 ) {
		return $this->get_builder()->add_group( $args, $position );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_builder() {
		if ( ! $this->builder ) {
			$this->builder = new Builder( $this );
		}

		return $this->builder;
	}

	/**
	 * Returns the form builder config.
	 *
	 * @return array
	 */
	public function get_builder_config() {
		$vars = Arr::except( get_object_vars( $this ),
			[ 'sections', 'containers', 'properties', 'builder', '_form' ]
		);

		return array_merge( $vars, $this->properties );
	}

	/**
	 * Get box property.
	 *
	 * @param  string $key
	 *
	 * @return mixed
	 */
	public function get_property( $key ) {
		return isset( $this->properties[ $key ] ) ? $this->properties[ $key ] : null;
	}

	/**
	 * Set box property.
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	public function set_property( $key, $value ) {
		$this->properties[ $key ] = $value;
	}
}
