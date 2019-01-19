<?php

namespace WPLibs\Form\Core\Utils;

use Illuminate\Support\Arr;

class Webfonts {
	/* Contants */
	const SYSTEM = 'system';
	const GOOGLE = 'googlefonts';
	const GOOGLE_FONTS_ENDPOINT = 'https://www.googleapis.com/webfonts/v1/webfonts';

	/**
	 * List of websafe fonts.
	 *
	 * @link http://www.w3schools.com/cssref/css_websafe_fonts.asp
	 *
	 * @var array
	 */
	protected static $websafe_fonts = [
		// Serif Fonts.
		'Georgia'             => 'Georgia, serif',
		'Palatino Linotype'   => '"Palatino Linotype", "Book Antiqua", Palatino, serif',
		'Times New Roman'     => '"Times New Roman", Times, serif',
		// Sans-Serif Fonts.
		'Arial'               => 'Arial, Helvetica, sans-serif',
		'Arial Black'         => '"Arial Black", Gadget, sans-serif',
		'Comic Sans MS'       => '"Comic Sans MS", cursive, sans-serif',
		'Impact'              => 'Impact, Charcoal, sans-serif',
		'Lucida Sans Unicode' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
		'Tahoma'              => 'Tahoma, Geneva, sans-serif',
		'Trebuchet MS'        => '"Trebuchet MS", Helvetica, sans-serif',
		'Verdana'             => 'Verdana, Geneva, sans-serif',
		// Monospace Fonts.
		'Courier New'         => '"Courier New", Courier, monospace',
		'Lucida Console'      => '"Lucida Console", Monaco, monospace',
	];

	/**
	 * Get all fonts.
	 *
	 * @return array
	 */
	public static function get_fonts() {
		return [
			static::SYSTEM => (array) static::get_system_fonts(),
			static::GOOGLE => (array) static::get_google_fonts(),
		];
	}

	/**
	 * Gets list system fonts.
	 *
	 * @return array
	 */
	public static function get_system_fonts() {
		$fonts = [];

		foreach ( static::$websafe_fonts as $label => $family ) {
			$fonts[] = [
				'family'   => $family,
				'label'    => $label,
				'variants' => [ '400', '400italic', '700', '700italic' ],
			];
		}

		return apply_filters( 'suru_libs_system_fonts', $fonts );
	}

	/**
	 * Gets the Google Fonts.
	 *
	 * @see https://developers.google.com/fonts/docs/developer_api
	 *
	 * @return array
	 */
	public static function get_google_fonts() {
		$google_fonts = get_transient( '_suru_libs_google_fonts' );

		if ( ! is_array( $google_fonts ) || empty( $google_fonts ) ) {
			// Fallback to load fonts from local file.
			$google_fonts = json_decode(
				file_get_contents( dirname( dirname( __DIR__ ) ) . '/Resources/webfonts.json' ), true
			);

			static::set_transients( $google_fonts );
		}

		return apply_filters( 'suru_libs_system_fonts', $google_fonts );
	}

	/**
	 * Get list of fonts from Google Fonts.
	 *
	 * @param  string $api_key The Google Fonts API key.
	 * @return array|false
	 */
	public static function fetch_google_fonts( $api_key = null ) {
		$raw_fonts = static::request_google_fonts( $api_key );

		// Invalid API key or something else.
		if ( ! is_array( $raw_fonts ) || ! isset( $raw_fonts['items'] ) ) {
			return [];
		}

		$fonts = [];

		foreach ( $raw_fonts['items'] as $item ) {
			if ( ! isset( $item['kind'] ) || 'webfonts#webfont' !== $item['kind'] ) {
				continue;
			}

			$fonts[] = Arr::only( $item, [ 'family', 'category', 'variants', 'subset' ] );
		}

		static::flush_transients();
		static::set_transients( $fonts );

		return $fonts;
	}

	/**
	 * Send http request to get fonts from Google Fonts service.
	 *
	 * @see https://developers.google.com/fonts/docs/developer_api
	 *
	 * @param  string $api_key The Google Fonts API key.
	 * @return array|false
	 */
	public static function request_google_fonts( $api_key = null ) {
		$api_key = apply_filters( 'suru_libs_google_fonts_api_keys', $api_key );

		if ( empty( $api_key ) ) {
			return false;
		}

		$response = wp_remote_get( static::GOOGLE_FONTS_ENDPOINT . '?sort=alpha' . ( $api_key ? "&key={$api_key}" : '' ),
			[ 'sslverify' => false ]
		);

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}

	/**
	 * Sets the google fonts in the transients.
	 *
	 * @param array $fonts
	 */
	public static function set_transients( array $fonts ) {
		set_transient( '_suru_libs_google_fonts', $fonts, 29 * DAY_IN_SECONDS );
	}

	/**
	 * FLush the Google Fonts from the transients.
	 */
	public static function flush_transients() {
		delete_transient( '_suru_libs_google_fonts' );
	}
}
