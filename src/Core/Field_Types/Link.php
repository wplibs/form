<?php

namespace WPLibs\Form\Core\Field_Types;

class Link extends Field_Type {
	/**
	 * Default field values.
	 *
	 * @var array
	 */
	protected $defaults = [
		'url'         => '',
		'nofollow'    => 0,
		'is_external' => 0,
	];

	/**
	 * {@inheritdoc}
	 */
	public function display( $field, $value, $builder ) {
		$value = $this->parse_value( $value );

		wp_enqueue_style( 'editor-buttons' );
		$field->add_js_dependencies( 'wplink' );

		// Prints the link dialog on the footer.
		add_action( 'admin_footer', function () {
			require_once ABSPATH . 'wp-includes/class-wp-editor.php';
			\_WP_Editors::wp_link_dialog();
		} );
		?>

		<div class="wplibs-control-link">
			<div class="wplibs-control-link__input wplibs-input-group">
				<?php
				echo $builder->input( [ // @WPCS: XSS OK.
					'type'         => 'url',
					'name'         => $builder->_name( '[url]' ),
					'id'           => $builder->_id( '_url' ), // @WPCS: XSS OK.
					'value'        => esc_url( $value['url'] ),
					'desc'         => null,
					'class'        => 'wplibs-input-group__control',
					'data-element' => 'url',
				] );
				?>

				<div class="wplibs-input-group__append">
					<button type="button" class="button js-open-link" data-target="<?php echo $builder->_id( '_textarea' ); // @WPCS: XSS OK. ?>">
						<span class="screen-reader-text"><?php echo esc_html__( 'Search', 'wplibs-form' ); ?></span>
						<i class="dashicons dashicons-search"></i>
					</button>
				</div>

				<div class="wplibs-input-group__append">
					<button type="button" class="button js-toggle-link-options">
						<span class="screen-reader-text"><?php echo esc_html__( 'Link Options', 'wplibs-form' ); ?></span>
						<i class="dashicons dashicons-admin-generic"></i>
					</button>
				</div>
			</div>

			<div class="wplibs-control-link__options" style="display: none;">
				<p>
					<?php
					echo $builder->checkbox( [ // @WPCS: XSS OK.
						'name'         => $builder->_name( '[is_external]' ),
						'id'           => $builder->_id( '_is_external' ),
						'desc'         => null,
						'data-element' => 'is_external',
					], (bool) $value['is_external'] );
					?>

					<label for="<?php echo $builder->_id( '_is_external' ); // @WPCS: XSS OK. ?>"><?php echo esc_html__( 'Open link in a new tab', 'wplibs-form' ); ?></label>
				</p>

				<p style="margin-bottom: 0;">
					<?php
					echo $builder->checkbox( [ // @WPCS: XSS OK.
						'name'         => $builder->_name( '[nofollow]' ),
						'id'           => $builder->_id( '_nofollow' ), // @WPCS: XSS OK.
						'desc'         => null,
						'data-element' => 'nofollow',
					], (bool) $value['nofollow'] );
					?>

					<label for="<?php echo $builder->_id( '_nofollow' ); // @WPCS: XSS OK. ?>"><?php echo esc_html__( 'Add nofollow', 'wplibs-form' ); ?></label>
				</p>
			</div>

			<textarea id="<?php echo $builder->_id( '_textarea' ); // @WPCS: XSS OK. ?>" style="display: none;"></textarea>
		</div>
		<?php
	}

	/**
	 * {@inheritdoc}
	 */
	public function sanitization( $value ) {
		if ( ! is_array( $value ) || empty( $value['url'] ) ) {
			return null;
		}

		$sanitized = [];

		$sanitized['url']         = wplibs_sanitize_url( $value['url'] );
		$sanitized['nofollow']    = wplibs_sanitize_checkbox( $value['nofollow'] );
		$sanitized['is_external'] = wplibs_sanitize_checkbox( $value['is_external'] );

		if ( empty( $sanitized['url'] ) ) {
			return null;
		}

		return $sanitized;
	}
}
