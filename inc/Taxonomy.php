<?php
namespace Skeleton;

/**
 * Taxonomy registration starter class.
 */
class Taxonomy {
	/**
	 * Singlur Taxonomy label.
	 *
	 * @var string
	 */
	protected $singular;

	/**
	 * Plural Taxonomy label.
	 *
	 * @var string
	 */
	protected $plural;

	/**
	 * The name of the taxonomy.
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * Name of the object type for the taxonomy object.
	 *
	 * @var array
	 */
	protected $object_types;

	/**
	 * Taxonomy registration arguments.
	 *
	 * @var array
	 */
	protected $taxonomy_args = array();

	/**
	 * Taxonomy registered object.
	 *
	 * @var object
	 */
	protected $taxonomy_object;

	/**
	 * Create a taxonomy by static method.
	 *
	 * @param string       $taxonomy     The name of the taxonomy.
	 * @param string|array $object_types Name of the object type for the taxonomy object.
	 * @param string       $singular     Singular taxonomy name.
	 * @param string       $plural       Plural taxonomy name.
	 */
	public static function make( $taxonomy, $object_types, $singular, $plural ) {
		return new static( $taxonomy, $object_types, $singular, $plural );
	}

	/**
	 * Create a taxonomy.
	 *
	 * @param string       $taxonomy     The name of the taxonomy.
	 * @param string|array $object_types Name of the object type for the taxonomy object.
	 * @param string       $singular     Singular taxonomy name.
	 * @param string       $plural       Plural taxonomy name.
	 */
	public function __construct( $taxonomy, $object_types, $singular, $plural ) {
		$this->taxonomy = $taxonomy;
		$this->object_types = (array) $object_types;

		$this->plural = $plural;
		$this->singular = $singular;
	}

	/**
	 * Return the WP Taxonomy instance.
	 *
	 * @return object|\WP_Taxonomy If WordPress 4.7+
	 */
	public function get_instance() {
		if ( is_null( $this->taxonomy_object ) ) {
			throw new \RuntimeException( "The '{$this->taxonomy}' taxonomy has never been registered before." );
		}

		return $this->taxonomy_object;
	}

	/**
	 * Set the taxonomy arguments.
	 *
	 * @param  array $taxonomy_args The taxonomy arguments.
	 * @return $this
	 */
	public function set( array $taxonomy_args = array() ) {
		$this->taxonomy_args = $this->parser_args( $taxonomy_args );

		return $this;
	}

	/**
	 * Actually registers our Taxonomy with the merged arguments.
	 *
	 * @access private
	 */
	public function register() {
		global $wp_taxonomies;

		// Register taxonomy.
		$result = register_taxonomy( $this->taxonomy, $this->object_types, $this->taxonomy_args );

		// If error, yell about it.
		if ( is_wp_error( $result ) ) {
			wp_die( $result->get_error_message() ); // WPCS: XSS OK.
		}

		$this->meta_boxes();

		// Success. Set args to what WP returns.
		$this->taxonomy_object = $wp_taxonomies[ $this->taxonomy ];
	}

	/**
	 * Gets the passed in arguments combined with our defaults.
	 *
	 * @param  array $taxonomy_args Taxonomy arguments.
	 * @return array
	 */
	protected function parser_args( array $taxonomy_args ) {
		// Hierarchical check that will be used multiple times below.
		$hierarchical = true;
		if ( isset( $taxonomy_args['hierarchical'] ) ) {
			$hierarchical = (bool) $taxonomy_args['hierarchical'];
		}

		// Default labels.
		$labels = array(
			'name'                       => $this->plural,
			'singular_name'              => $this->singular,
			'search_items'               => sprintf( __( 'Search %s', 'skeleton' ), $this->plural ),
			'all_items'                  => sprintf( __( 'All %s', 'skeleton' ), $this->plural ),
			'edit_item'                  => sprintf( __( 'Edit %s', 'skeleton' ), $this->singular ),
			'view_item'                  => sprintf( __( 'View %s', 'skeleton' ), $this->singular ),
			'update_item'                => sprintf( __( 'Update %s', 'skeleton' ), $this->singular ),
			'add_new_item'               => sprintf( __( 'Add New %s', 'skeleton' ), $this->singular ),
			'new_item_name'              => sprintf( __( 'New %s Name', 'skeleton' ), $this->singular ),
			'not_found'                  => sprintf( __( 'No %s found.', 'skeleton' ), $this->plural ),
			'no_terms'                   => sprintf( __( 'No %s', 'skeleton' ), $this->plural ),

			// Hierarchical stuff.
			'parent_item'       => $hierarchical ? sprintf( __( 'Parent %s', 'skeleton' ), $this->singular ) : null,
			'parent_item_colon' => $hierarchical ? sprintf( __( 'Parent %s:', 'skeleton' ), $this->singular ) : null,

			// Non-hierarchical stuff.
			'popular_items'              => $hierarchical ? null : sprintf( __( 'Popular %s', 'skeleton' ), $this->plural ),
			'separate_items_with_commas' => $hierarchical ? null : sprintf( __( 'Separate %s with commas', 'skeleton' ), $this->plural ),
			'add_or_remove_items'        => $hierarchical ? null : sprintf( __( 'Add or remove %s', 'skeleton' ), $this->plural ),
			'choose_from_most_used'      => $hierarchical ? null : sprintf( __( 'Choose from the most used %s', 'skeleton' ), $this->plural ),
		);

		$defaults = array(
			'labels'            => array(),
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug' => $this->taxonomy,
				'hierarchical' => $hierarchical,
			),
		);

		$taxonomy_args = wp_parse_args( $taxonomy_args, $defaults );
		$taxonomy_args['labels'] = wp_parse_args( $taxonomy_args['labels'], $labels );

		return $taxonomy_args;
	}

	/**
	 * Add meta boxes to this post type.
	 *
	 * @see $this->add_meta_box()
	 * @see \Skeleton\Metabox
	 */
	public function meta_boxes() {}

	/**
	 * Make a new meta box for this taxonomy.
	 *
	 * Note: Use this method will create a metabox with ID `awethemes/taxonomy/{$cmb_id}`.
	 *
	 * @param  string   $cmb_id   Metabox ID.
	 * @param  callable $callback Metabox arguments.
	 * @return $this
	 */
	public function add_meta_box( $cmb_id, $callback = null ) {
		if ( 'default' === $cmb_id ) {
			$cmb_id = $this->taxonomy . '_default';
		} else {
			$cmb_id = $this->taxonomy . '_' . $cmb_id;
		}

		$metabox = new Metabox( $cmb_id, array(
			'object_types' => array( 'term' ),
			'taxonomies'   => array( $this->taxonomy ),
		));

		if ( is_callable( $callback ) ) {
			call_user_func_array( $callback, array( $metabox ) );
		}

		return $metabox;
	}

	/**
	 * Add a field to default metabox of this taxonomy.
	 *
	 * @param  array $field Metabox field config array.
	 * @return int|false
	 */
	public function add_field( array $field ) {
		return $this->get_default_metabox()->add_field( $field );
	}

	/**
	 * Add a group field to default metabox of this taxonomy.
	 *
	 * @param  array    $id       Group ID.
	 * @param  callable $callback Group builder callback.
	 * @return \Skeleton\CMB2\Group
	 */
	public function add_group( $id, $callback = null ) {
		return $this->get_default_metabox()->add_group( $id, $callback );
	}

	/**
	 * Get default metabox for this taxonomy.
	 *
	 * @return \Skeleton\Metabox
	 */
	protected function get_default_metabox() {
		$metabox = \CMB2_Boxes::get( $this->taxonomy . '_default' );

		if ( ! $metabox ) {
			$metabox = $this->add_meta_box( 'default', function ( $mb ) {
				$mb->set_priority( 0 );
				$mb->vertical_tabs( false );
			});
		}

		return $metabox;
	}
}
