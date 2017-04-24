<?php

namespace Skeleton\Http;

use Skeleton\Skeleton;

class Response {
	/**
	 * The globally available instance of the skeleton container.
	 *
	 * @var Skeleton
	 */
	protected $skeleton;

	/**
	 * Constructor.
	 *
	 * @param Skeleton $skeleton The skeleton container instance.
	 */
	public function __construct( Skeleton $skeleton ) {
		$this->skeleton = $skeleton;
	}

	/**
	 * Send a download file to client.
	 *
	 * @param  string $data     Data to send to client download.
	 * @param  string $filename Download file name.
	 */
	public function download( $data, $filename = null ) {
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
