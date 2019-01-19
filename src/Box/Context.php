<?php

namespace WPLibs\Form\Box;

class Context {
	/**
	 * The current object ID.
	 *
	 * @var int
	 */
	public $object_id;

	/**
	 * The current object type.
	 *
	 * @var string
	 */
	public $object_type;

	/**
	 * Create the context from global.
	 *
	 * @return static
	 */
	public static function guest() {
		$type = static::guest_object_type();

		$object_id = static::guest_object_id( 0, $type );

		return new static( $object_id, $type );
	}

	/**
	 * Constructor.
	 *
	 * @param int    $object_id
	 * @param string $object_type
	 */
	public function __construct( $object_id, $object_type = 'post' ) {
		$this->object_id   = $object_id;
		$this->object_type = $object_type;
	}

	/**
	 * Gets the current object ID.
	 *
	 * @return int
	 */
	public function get_object_id() {
		return $this->object_id;
	}

	/**
	 * Sets the current object ID.
	 *
	 * @param  int $object_id
	 * @return $this
	 */
	public function set_object_id( $object_id ) {
		$this->object_id = $object_id;

		return $this;
	}

	/**
	 * Gets the current object type.
	 *
	 * @return string
	 */
	public function get_object_type() {
		return $this->object_type;
	}

	/**
	 * Sets the current object type.
	 *
	 * @param  string $object_type
	 * @return $this
	 */
	public function set_object_type( $object_type ) {
		if ( ! in_array( $object_type, $supported = [ 'user', 'comment', 'term', 'post' ] ) ) {
			// @codingStandardsIgnoreLine
			_doing_it_wrong( __FUNCTION__, 'The object type must be one of: ' . implode( ', ', $supported ), null );
		}

		$this->object_type = $object_type;

		return $this;
	}

	/**
	 * Guest the object type for the current page, based on the $pagenow global.
	 *
	 * @return string
	 */
	public static function guest_object_type() {
		global $pagenow;

		if ( in_array( $pagenow, [ 'user-edit.php', 'profile.php', 'user-new.php' ], true ) ) {
			return 'user';
		}

		if ( in_array( $pagenow, [ 'edit-comments.php', 'comment.php' ], true ) ) {
			return 'comment';
		}

		if ( in_array( $pagenow, [ 'edit-tags.php', 'term.php' ], true ) ) {
			return 'term';
		}

		// @codingStandardsIgnoreLine
		if ( defined( 'DOING_AJAX' ) && isset( $_POST['action'] ) && 'add-tag' === sanitize_text_field( wp_unslash( $_POST['action'] ) ) ) {
			return 'term';
		}

		return 'post';
	}

	/**
	 * Guest the object ID from global space.
	 *
	 * @param  int    $object_id   Object ID.
	 * @param  string $object_type Object type.
	 * @return int
	 */
	public static function guest_object_id( $object_id = 0, $object_type = null ) {
		global $pagenow;

		if ( is_null( $object_type ) ) {
			$object_type = static::guest_object_type();
		}

		switch ( $object_type ) {
			case 'user':
				$object_id = isset( $_REQUEST['user_id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['user_id'] ) ) : $object_id;

				if ( ! $object_id && 'user-new.php' !== $pagenow && isset( $GLOBALS['user_ID'] ) ) {
					$object_id = $GLOBALS['user_ID'];
				}
				break;

			case 'comment':
				$object_id = isset( $_REQUEST['c'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['c'] ) ) : $object_id;

				if ( ! $object_id && isset( $GLOBALS['comments']->comment_ID ) ) {
					$object_id = $GLOBALS['comments']->comment_ID;
				}
				break;

			case 'term':
				$object_id = isset( $_REQUEST['tag_ID'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tag_ID'] ) ) : $object_id;
				break;

			default:
				$object_id = isset( $GLOBALS['post']->ID ) ? $GLOBALS['post']->ID : $object_id;

				if ( ! $object_id && isset( $_REQUEST['post'] ) ) {
					$object_id = sanitize_text_field( wp_unslash( $_REQUEST['post'] ) );
				}
				break;
		}

		return absint( $object_id );
	}
}
