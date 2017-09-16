<?php
namespace Skeleton\Support;

class Utils {
	/**
	 * Return a clone ID by given a suffix.
	 *
	 * @param  string $base_id
	 * @param  string $clone_suffix
	 * @return string
	 */
	public static function clone_id( $base_id, $clone_suffix ) {
		$id_data = Multidimensional::split( $base_id );

		if ( empty( $id_data['keys'] ) ) {
			return $base_id . '_' . $clone_suffix;
		}

		$editkey = array_pop( $id_data['keys'] );
		$id_data['keys'][] = $editkey . '_' . $clone_suffix;

		return Multidimensional::build( $id_data );
	}

	/**
	 * Determine if a given string matches a given pattern.
	 *
	 * @param  string $pattern
	 * @param  string $value
	 * @return bool
	 */
	public static function str_is( $pattern, $value ) {
		if ( $pattern == $value ) {
			return true;
		}

		$pattern = preg_quote( $pattern, '#' );

		// Asterisks are translated into zero-or-more regular expression wildcards
		// to make it convenient to check if the strings starts with the given
		// pattern such as "library/*", making any string check convenient.
		$pattern = str_replace( '\*', '.*', $pattern );

		return (bool) preg_match( '#^' . $pattern . '\z#u', $value );
	}

	/**
	 * Send a download file to client.
	 *
	 * @param  string $data     Data to send to client download.
	 * @param  string $filename Download file name.
	 */
	public static function send_download( $data, $filename = null ) {
		if ( empty( $filename ) ) {
			$filename = sprintf( '%s.txt', uniqid() );
		}

		header( 'Content-Type: application/octet-stream; charset=' . get_option( 'blog_charset' ), true );
		header( 'Content-disposition: attachment; filename=' . $filename );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		// Print data endwith newline.
		print $data . "\n"; // WPCS: XSS OK.
		exit;
	}
}
