<?php

namespace WPLibs\Form\Core\Field_Types;

class Toggle extends Field_Type {
	/**
	 * {@inheritdoc}
	 */
	public function display( $field, $value, $builder ) {
		$description = $builder->_desc();

		$field->set_option( 'description', '' );

		?>
		<div class="wplibs-onoffswitch">
			<?php echo $builder->checkbox( [], wplibs_sanitize_checkbox( $field->escaped_value() ) ); // @codingStandardsIgnoreLine ?>
		</div>

		<label for="<?php echo esc_attr( $builder->_id() ); ?>"><?php print $description; // WPCS: XSS OK. ?></label>
		<?php
	}

	/**
	 * {@inheritdoc}
	 */
	public function sanitization( $value ) {
		return wplibs_sanitize_checkbox( $value );
	}
}
