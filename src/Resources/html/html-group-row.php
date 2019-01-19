<?php

use WPLibs\Form\Helper\Utils;

/* @var $group \WPLibs\Form\Field_Group */
$group->prepare_new_row();

$group->peform_param_callback( 'before_group_row' ); ?>

<div role="row" class="cmb-repeatable-grouping <?php echo $group->options( 'closed' ) ? ' closed' : ''; ?>" data-iterator="<?php echo esc_attr( $group->index ); ?>">
	<?php
	// Loop and render repeatable group fields.
	foreach ( $group->fields as $field ) {
		$field->set_option( 'description', '' );

		// Build the row_attributes.
		$row_attributes = array_merge( [
			'role'           => 'cell',
			'class'          => 'wplibs-form__grouprow ' . $field->row_classes(),
			'data-hash'      => $field->hash_id(),
			'data-fieldtype' => $field->get_type(),
		], (array) $field->get_option( 'row_attributes', [] ) );

		// @codingStandardsIgnoreLine
		echo '<div' . Utils::build_html_attributes( $row_attributes ) . '>', $field->render(), '</div>';
	}
	?>
</div>

<?php $group->peform_param_callback( 'after_group_row' ); ?>
