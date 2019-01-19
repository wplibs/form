<?php

namespace WPLibs\Form\Core\Field_Types;

use WPLibs\Form\Helper\Utils;

class Dimensions extends Field_Type {
	/**
	 * Default field values.
	 *
	 * @var mixed
	 */
	protected $defaults = [
		'top'    => '',
		'right'  => '',
		'bottom' => '',
		'left'   => '',
		'linked' => 1,
	];

	/**
	 * {@inheritdoc}
	 */
	public function display( $field, $value, $builder ) {
		$value = $this->parse_value( $value );

		$dimensions = [
			'top'    => esc_html__( 'Top', 'wplibs-form' ),
			'right'  => esc_html__( 'Right', 'wplibs-form' ),
			'bottom' => esc_html__( 'Bottom', 'wplibs-form' ),
			'left'   => esc_html__( 'Left', 'wplibs-form' ),
		];

		?>
		<ul class="wplibs-control-dimension">
			<?php foreach ( $dimensions as $key => $_title ) : ?>
				<li>
					<?php
					echo $builder->input( [ // @WPCS: XSS OK.
						'type'         => 'number',
						'name'         => $builder->_name( '[' . $key . ']' ),
						'id'           => $builder->_id( '_' . $key ),
						'value'        => isset( $value[ $key ] ) ? $value[ $key ] : '',
						'desc'         => null,
						'data-element' => esc_attr( $key ),
					] );
					?>

					<label for="<?php echo $builder->_id( '_' . $key ); // @WPCS: XSS OK. ?>" class="suru-control-dimension-label"><?php echo esc_html( $_title ); ?></label>
				</li>
			<?php endforeach; ?>

			<button type="button" class="button wplibs-tooltip-target <?php echo $value['linked'] ? 'linked' : 'unlinked'; ?>" data-toggle="link" data-tooltip="<?php echo esc_attr__( 'Link values together', 'wplibs-form' ); ?>">
				<span class="wplibs-control-dimension__linked">
					<i class="dashicons dashicons-admin-links" aria-hidden="true"></i>
					<span class="screen-reader-text"><?php echo esc_html__( 'Link values together', 'wplibs-form' ); ?></span>
				</span>

				<span class="wplibs-control-dimension__unlinked">
					<i class="dashicons dashicons-editor-unlink" aria-hidden="true"></i>
					<span class="screen-reader-text"><?php echo esc_html__( 'Unlinked values', 'wplibs-form' ); ?></span>
				</span>
			</button>

			<?php
			echo $builder->input( [ // @WPCS: XSS OK.
				'type'         => 'hidden',
				'name'         => $builder->_name( '[linked]' ),
				'id'           => $builder->_id( '_linked' ),
				'value'        => $value['linked'],
				'desc'         => null,
				'data-element' => 'linked',
			] );
			?>
		</ul>
		<?php
	}

	/**
	 * {@inheritdoc}
	 */
	public function sanitization( $value ) {
		if ( ! is_array( $value ) || ! isset( $value['linked'] ) ) {
			return null;
		}

		$sanitized = [];

		foreach ( [ 'top', 'right', 'bottom', 'left' ] as $key ) {
			if ( isset( $value[ $key ] ) && Utils::is_not_blank( $value[ $key ] ) ) {
				$sanitized[ $key ] = filter_var( $value[ $key ], FILTER_SANITIZE_NUMBER_FLOAT );
			}
		}

		if ( empty( $sanitized ) ) {
			return null;
		}

		$sanitized['linked'] = wplibs_sanitize_checkbox( $value['linked'] );

		return $sanitized;
	}
}
