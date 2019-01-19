<?php

namespace WPLibs\Form\Storages;

use WPLibs\Form\Contracts\Storage;

class Custom_CSS_Storage implements Storage {
	/**
	 * Name of the current stylesheet.
	 *
	 * @var string
	 */
	protected $stylesheet;

	/**
	 * Constructor.
	 *
	 * @param string $stylesheet
	 */
	public function __construct( $stylesheet = null ) {
		$this->stylesheet = $stylesheet ?: get_stylesheet();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get( $key, $default = null ) {
		$post = wp_get_custom_css_post( $this->stylesheet );

		if ( $post ) {
			return $post->post_content;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function set( $key, $value ) {
		$r = wp_update_custom_css_post( $value, [
			'stylesheet' => $this->stylesheet,
		] );

		if ( is_wp_error( $r ) ) {
			return false;
		}

		// Cache post ID in theme mod for performance to avoid additional DB query.
		if ( get_stylesheet() === $this->stylesheet ) {
			set_theme_mod( 'custom_css_post_id', $r->ID );
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete( $key ) {
		return $this->set( $key, '' );
	}
}
