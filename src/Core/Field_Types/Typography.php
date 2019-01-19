<?php /* @noinspection PhpMethodParametersCountMismatchInspection */

namespace WPLibs\Form\Core\Field_Types;

use WPLibs\Form\Utils\Webfonts;

class Typography extends Field_Type {
	/**
	 * Default field values.
	 *
	 * @var array
	 */
	protected $defaults = [
		'family'         => '',
		'backup'         => '',
		'font-size'      => '14',
		'font-weight'    => '400',
		'line-height'    => '',
		'word-spacing'   => '',
		'letter-spacing' => '',
	];

	/**
	 * {@inheritdoc}
	 */
	public function display( $field, $value, $builder ) {
		$value = $this->parse_value( $value );

		$html_builder = $builder->get_html_builder();

		$system_fonts = wp_list_pluck( Webfonts::get_system_fonts(), 'label', 'name' );

		$defaults = [
			'font-family'     => true,
			'font-size'       => true,
			'font-weight'     => true,
			'font-style'      => true,
			'font-backup'     => false,
			'subsets'         => true,
			'custom_fonts'    => true,
			'text-align'      => true,
			'text-transform'  => false,
			'font-variant'    => false,
			'text-decoration' => false,
			'preview'         => true,
			'line-height'     => true,
			'word-spacing'    => false,
			'letter-spacing'  => false,
			'google_fonts'    => true,
		];
		?>

		<div class="wplibs-control-typography">
			<div class="wplibs-control-typography__row">
				<div class="wplibs-control-typography__family">
					<label for="<?php echo esc_attr( $builder->_input_id( '_family' ) ); ?>"><?php echo esc_html__( 'Font Family', 'wplibs-form' ); ?></label>

					<?php
					print $html_builder->select( $builder->_name( '[family]' ), [], $value['family'], [ // @WPCS: XSS OK.
						'id'           => $builder->_input_id( '_family' ),
						'data-element' => 'family',
					] );
					?>
				</div>

				<div class="wplibs-control-typography__backup">
					<label for="<?php echo esc_attr( $builder->_input_id( '_backup' ) ); ?>"><?php echo esc_html__( 'Backup Font Family', 'wplibs-form' ); ?></label>

					<?php
					print $html_builder->select( $builder->_name( '[backup]' ), $system_fonts, $value['backup'], [ // @WPCS: XSS OK.
						'id'           => $builder->_input_id( '_backup' ),
						'data-element' => 'backup',
					] );
					?>
				</div>

				<div class="wplibs-control-typography__weight">
					<label for="<?php echo esc_attr( $builder->_input_id( '_font_weight' ) ); ?>"><?php echo esc_html__( 'Font Weight', 'wplibs-form' ); ?></label>

					<?php
					print $html_builder->select( $builder->_name( '[font-weight]' ), $this->get_font_weights(), $value['font-weight'], [ // @WPCS: XSS OK.
						'id'           => $builder->_input_id( '_font_weight' ),
						'data-element' => 'weight',
					] );
					?>
				</div>
			</div>

			<div class="wplibs-control-typography__row">
				<div class="wplibs-control-typography__size">
					<label for="<?php echo esc_attr( $builder->_input_id( '_font_size' ) ); ?>"><?php echo esc_html__( 'Font Size', 'wplibs-form' ); ?></label>

					<?php
					print $html_builder->number( $builder->_name( '[font-size]' ), $value['font-size'], [ // @WPCS: XSS OK.
						'id'           => $builder->_input_id( '_font_size' ),
						'step'         => '1',
						'data-element' => 'size',
					] );
					?>
				</div>

				<div class="wplibs-control-typography__line-height">
					<label for="<?php echo esc_attr( $builder->_input_id( '_line_height' ) ); ?>"><?php echo esc_html__( 'Line Height', 'wplibs-form' ); ?></label>

					<?php
					print $html_builder->number( $builder->_name( '[line-height]' ), $value['line-height'], [ // @WPCS: XSS OK.
						'id'           => $builder->_input_id( '_line_height' ),
						'step'         => '0.1',
						'data-element' => 'line_height',
					] );
					?>
				</div>

				<div class="wplibs-control-typography__word-spacing">
					<label for="<?php echo esc_attr( $builder->_input_id( '_word_spacing' ) ); ?>"><?php echo esc_html__( 'Word Spacing', 'wplibs-form' ); ?></label>

					<?php
					print $html_builder->number( $builder->_name( '[word-spacing]' ), $value['word-spacing'], [ // @WPCS: XSS OK.
						'id'           => $builder->_input_id( '_word_spacing' ),
						'step'         => '1',
						'data-element' => 'word_spacing',
					] );
					?>
				</div>

				<div class="wplibs-control-typography__letter-spacing">
					<label for="<?php echo esc_attr( $builder->_input_id( '_letter_spacing' ) ); ?>"><?php echo esc_html__( 'Letter Spacing', 'wplibs-form' ); ?></label>

					<?php
					print $html_builder->number( $builder->_name( '[letter-spacing]' ), $value['letter-spacing'], [ // @WPCS: XSS OK.
						'id'           => $builder->_input_id( '_letter_spacing' ),
						'step'         => '1',
						'data-element' => 'letter_spacing',
					] );
					?>
				</div>
			</div>
		</div>
		<?php
	}

	public function get_font_weights() {
		return [
			'200' => 'Lighter',
			'400' => 'Normal',
			'700' => 'Bold',
			'900' => 'Bolder',
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function sanitization( $value ) {
		return $value;
	}
}
