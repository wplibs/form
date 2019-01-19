<?php

namespace WPLibs\Form\Core\Field_Types;

class Button_Choice extends Field_Type {
	/**
	 * {@inheritdoc}
	 */
	public function display( $field, $value, $builder ) {
		?>
		<div class="button-group">
			<?php foreach ( (array) $field->options() as $key => $label ) : ?>
				<?php
				echo $builder->input( [ // @WPCS: XSS OK.
					'type'    => 'radio',
					'id'      => $builder->_id( '_' . $key ),
					'value'   => $key,
					// 'checked' => $key === $value ? 'checked' : null,
					'desc'    => '',
					'style' => 'display: none',
				] );
				?>

				<label class="button suru-tooltip-target" for="<?php echo esc_attr( $builder->_input_id( '_' . $key ) ); ?>" title="<?php echo esc_attr( $label ); ?>">
					<span><?php echo esc_html( $label ); ?></span>
				</label>
			<?php endforeach ?>
		</div>
		<?php
	}
}
