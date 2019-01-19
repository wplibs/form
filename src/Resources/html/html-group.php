<?php

use WPLibs\Form\Helper\Utils;

$group->index = 0;
$group_values = (array) $group->value();

$row_attributes = array_merge( [
	'class'          => 'wplibs-form__row cmb-row cmb-repeat-group-wrap ' . $group->row_classes(),
	'data-hash'      => $group->hash_id(),
	'data-fieldtype' => $group->get_type(),
], (array) $group->get_option( 'row_attributes', [] ) );

$group->peform_param_callback( 'before_group' );

?>

<div <?php echo Utils::build_html_attributes( $row_attributes ); // @WPCS: XSS OK. ?>>
	<?php
	if ( ! $group->args( 'show_names' ) ) {
		echo '<div class="wplibs-form__control cmb-td">';

		$group->peform_param_callback( 'label_cb' );
	} else {
		if ( $group->get_param_callback_result( 'label_cb' ) ) {
			// @codingStandardsIgnoreLine
			echo '<div class="wplibs-form__name cmb-th">', $group->peform_param_callback( 'label_cb' ), '</div>';
		}

		echo '<div class="wplibs-form__control cmb-td">';
	}
	?>

	<div role="table" data-groupid="<?php echo esc_attr( $group->id() ); ?>" id="<?php echo esc_attr( $group->id() ); ?>_repeat" <?php echo $this->group_wrap_attributes( $group ); // @WPCS: XSS OK. ?>>
		<div role="rowheader" class="">
			<?php
			foreach ( array_values( $group->args( 'fields' ) ) as $key => $args ) {
				if ( 'hidden' === $args['type'] ) {
					continue;
				}

				echo '<div role="cell" class="column">' . esc_html( $args['name'] ) . '</div>';
			}
			?>
		</div>

		<?php
		if ( ! empty( $group_values ) ) {
			foreach ( $group_values as $value ) {
				$this->render_group_row( $group );
				$group->index++;
			}
		} else {
			$this->render_group_row( $group );
		}
		?>

		<?php if ( $group->args( 'repeatable' ) ) : ?>
			<div role="row" class="cmb-remove-field-row">
				<div role="cell" class="cmb-add-row"><button type="button" data-selector="<?php echo esc_attr( $group->id() ); ?>_repeat" data-grouptitle="<?php echo esc_attr( $group->options( 'group_title' ) ); ?>" class="cmb-add-group-row button-secondary"><?php echo $group->options( 'add_button' ); // @WPCS: XSS OK. ?></button></div>
			</div>
		<?php endif; ?>
	</div>

</div></div><!-- /.wplibs-form__row -->

<?php $group->peform_param_callback( 'after_group' ); ?>
