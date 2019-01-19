<?php

use WPLibs\Form\Field_Render;
use WPLibs\Form\Helper\Utils;

// Build the row_attributes.
$row_attributes = array_merge( [
	'class'          => 'wplibs-form__row cmb-row ' . $this->row_classes(),
	'data-hash'      => $this->hash_id(),
	'data-fieldtype' => $this->get_type(),
], (array) $this->get_option( 'row_attributes', [] ) );

// Errors.
$_errors = $this->form ? $this->form->get_error( $this->get_id() ) : null;

// Start printing.
$this->peform_param_callback( 'before_row' );

echo '<div' . Utils::build_html_attributes( $row_attributes ) . ">\n"; // @WPCS: XSS OK.

if ( ! $this->args( 'show_names' ) ) {
	echo "\n\t<div class=\"wplibs-form__control cmb-td\">\n";

	$this->peform_param_callback( 'label_cb' );
} else {
	if ( $this->get_param_callback_result( 'label_cb' ) ) {
		// @codingStandardsIgnoreLine
		echo '<div class="wplibs-form__name cmb-th">', $this->peform_param_callback( 'label_cb' ), '</div>';
	}

	echo "\n\t<div class=\"wplibs-form__control cmb-td\">\n";
}

$this->render();

if ( $_errors ) {
	echo '<p class="wplibs-form__invalid">' . $this->form->get_error( $this->get_id() ) . '</p>'; // @WPCS: XSS OK.
}

echo "\n\t</div>\n</div>";

$this->peform_param_callback( 'after_row' );
