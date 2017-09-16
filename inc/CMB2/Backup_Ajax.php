<?php
namespace Skeleton\CMB2;

class Backup_Ajax {
	/**
	 * Ajax backup and restore CMB2 data.
	 */
	public function __construct() {
		add_action( 'wp_ajax_skeleton_export_backup', array( $this, 'ajax_export_backup' ) );
		add_action( 'wp_ajax_skeleton_restore_backup', array( $this, 'ajax_restore_backup' ) );
		add_action( 'wp_ajax_skeleton_reset_cmb2', array( $this, 'ajax_reset_cmb2' ) );
	}

	/**
	 * Handler AJAX export CMB2 backups data.
	 */
	public function ajax_export_backup() {
		$backups = new Backup( $this->get_cmb2_from_request() );
		$filename = $backups->get_backup_id() . '-' . date_i18n( 'mdY' ) . '.txt';

		header( 'Content-Type: application/octet-stream; charset=' . get_option( 'blog_charset' ), true );
		header( 'Content-disposition: attachment; filename=' . $filename );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		// Print data endwith newline.
		print $backups->backup() . "\n"; // WPCS: XSS OK.
		exit;
	}

	/**
	 * Handler AJAX restore CMB2 backups data.
	 */
	public function ajax_restore_backup() {
		$backups = new Backup( $this->get_cmb2_from_request() );

		ob_clean();
		$restored = $backups->restore( $this->request( 'backup_code' ) );

		if ( is_wp_error( $restored ) ) {
			wp_send_json_error( $restored );
		}

		wp_send_json_success( array(
			'message' => 'Successfully restored settings',
		));
	}

	/**
	 * Handler AJAX export CMB2 backups data.
	 */
	public function ajax_reset_cmb2() {
		$cmb2 = $this->get_cmb2_from_request();

		// Remove all validate of fields.
		if ( $cmb2 instanceof CMB2 ) {
			$fields = $cmb2->prop( 'fields' );

			foreach ( $fields as &$args ) {
				$args['validate'] = null;
				$args['validate_cb'] = null;
			}

			$cmb2->set_prop( 'fields', $fields );
		}

		// Save the CMB2 with empty array.
		$cmb2->save_fields( 0, '', array() );

		// Remove transient errors.
		if ( $cmb2 instanceof CMB2 ) {
			delete_transient( $cmb2->transient_id( '_errors' ) );
		}

		wp_send_json_success(array(
			'message' => 'Successfully reset settings',
		));
	}

	/**
	 * Get CMB2 instance from request.
	 *
	 * @return \CMB2
	 */
	protected function get_cmb2_from_request() {
		$cmb2 = cmb2_get_metabox(
			$this->request( 'id' ),
			$this->request( 'obj_id' ),
			$this->request( 'obj_type' )
		);

		if ( ! $cmb2 instanceof \CMB2 ) {
			wp_send_json_error( new \WP_Error( 'error', esc_html__( 'Invalid CMB2 ID.', 'skeleton' ) ) );
		}

		return $cmb2;
	}

	/**
	 * Get request value by key name with default value support.
	 *
	 * @param  string $key     Request key name from $_POST, $_GET, $_REQUEST.
	 * @param  mixed  $default A default value will be return if key name is not exists.
	 * @return mixed
	 */
	protected function request( $key, $default = null ) {
		return isset( $_REQUEST[ $key ] ) ? $_REQUEST[ $key ] : $default;
	}
}
