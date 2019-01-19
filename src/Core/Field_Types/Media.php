<?php

namespace WPLibs\Form\Core\Field_Types;

use Illuminate\Support\Arr;

class Media extends Field_Type {
	/**
	 * Media control mime type.
	 *
	 * Possible values: video, audio, image.
	 *
	 * @var string
	 */
	public $mime_type = '';

	/**
	 * {@inheritdoc}
	 */
	public function display( $field, $value, $builder ) {
		wp_enqueue_media();

		echo $builder->hidden(); // @WPCS: XSS OK.
		$this->print_control_container();
	}

	/**
	 * {@inheritdoc}
	 */
	public function sanitization( $value ) {
		return $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function template() {
		$labels = $this->get_button_labels();

		?>

		<# if ( data.attachment && data.attachment.id ) { #>
			<div class="attachment-media-view attachment-media-view-{{ data.attachment.type }} {{ data.attachment.orientation }}">
				<div class="thumbnail thumbnail-{{ data.attachment.type }}">
					<# if ( 'image' === data.attachment.type && data.attachment.sizes && data.attachment.sizes.medium ) { #>
						<img class="attachment-thumb" src="{{ data.attachment.sizes.medium.url }}" draggable="false" alt=""/>
					<# } else if ( 'image' === data.attachment.type && data.attachment.sizes && data.attachment.sizes.full ) { #>
						<img class="attachment-thumb" src="{{ data.attachment.sizes.full.url }}" draggable="false" alt=""/>
					<# } else if ( 'audio' === data.attachment.type ) { #>
						<# if ( data.attachment.image && data.attachment.image.src && data.attachment.image.src !== data.attachment.icon ) { #>
						<img src="{{ data.attachment.image.src }}" class="thumbnail" draggable="false" alt=""/>
						<# } else { #>
						<img src="{{ data.attachment.icon }}" class="attachment-thumb type-icon" draggable="false" alt=""/>
						<# } #>

						<p class="attachment-meta attachment-meta-title">&#8220;{{ data.attachment.title }}&#8221;</p>
						<# if ( data.attachment.album || data.attachment.meta.album ) { #>
						<p class="attachment-meta"><em>{{ data.attachment.album || data.attachment.meta.album }}</em></p>
						<# } #>

						<# if ( data.attachment.artist || data.attachment.meta.artist ) { #>
						<p class="attachment-meta">{{ data.attachment.artist || data.attachment.meta.artist }}</p>
						<# } #>

						<audio style="visibility: hidden" controls class="wp-audio-shortcode" width="100%" preload="none">
							<source type="{{ data.attachment.mime }}" src="{{ data.attachment.url }}"/>
						</audio>
					<# } else if ( 'video' === data.attachment.type ) { #>
						<div class="wp-media-wrapper wp-video">
							<video controls="controls" class="wp-video-shortcode" preload="metadata"
								<# if ( data.attachment.image && data.attachment.image.src !== data.attachment.icon ) { #>poster="{{ data.attachment.image.src }}"<# } #>>
								<source type="{{ data.attachment.mime }}" src="{{ data.attachment.url }}"/>
							</video>
						</div>
					<# } else { #>
						<img class="attachment-thumb type-icon icon" src="{{ data.attachment.icon }}" draggable="false" alt="" />
						<p class="attachment-title">{{ data.attachment.title }}</p>
					<# } #>
				</div>

				<div class="actions">
					<?php if ( current_user_can( 'upload_files' ) ) : ?>
						<button type="button" class="button remove-button"><?php echo esc_html( $labels['remove'] ); ?></button>
						<button type="button" class="button upload-button control-focus"><?php echo esc_html( $labels['change'] ); ?></button>
					<?php endif; ?>
				</div>
			</div>
		<# } else { #>
			<div class="attachment-media-view">
				<p class="placeholder"><?php echo esc_html( $labels['placeholder'] ); ?></p>

				<div class="actions">
					<# if ( data.default ) { #>
					<button type="button" class="button default-button"><?php echo esc_html( $labels['default'] ); ?></button>
					<# } #>

					<?php if ( current_user_can( 'upload_files' ) ) : ?>
						<button type="button" class="button upload-button"><?php echo esc_html( $labels['select'] ); ?></button>
					<?php endif; ?>
				</div>
			</div>
		<# } #>
		<?php
	}

	/**
	 * Returns field data for the JS.
	 *
	 * @param \WPLibs\Form\Field $field
	 * @return array
	 */
	public function js_data( $field ) {
		$value = $field->get_value();

		// Preapre attachment for the preview.
		$attachment = [];

		// Fake an attachment model - needs all fields used by template.
		// Note that the default value must be a URL, NOT an attachment ID.
		if ( ! $value && $_default = $field->get_default() ) {
			$type = in_array( substr( $_default, -3 ), [ 'jpg', 'png', 'gif', 'bmp' ] ) ? 'image' : 'document';

			$attachment = [
				'id'    => 1,
				'url'   => $_default,
				'type'  => $type,
				'icon'  => wp_mime_type_icon( $type ),
				'title' => basename( $_default ),
			];

			if ( 'image' === $type ) {
				$attachment['sizes'] = [ 'full' => [ 'url' => $_default ] ];
			}
		} elseif ( $value ) {
			$attachment = wp_prepare_attachment_for_js( $value );
		}

		return [
			'mime_type'  => $this->mime_type,
			'attachment' => $attachment,
			'labels'     => Arr::only( $this->get_button_labels(), [ 'frame_title', 'frame_button' ] ),
		];
	}

	/**
	 * Get the button labels.
	 *
	 * Provides an array of the default button labels based on the mime type of the current control.
	 *
	 * @return array An associative array of default button labels.
	 */
	public function get_button_labels() {
		// Get just the mime type and strip the mime subtype if present.
		$mime_type = ! empty( $this->mime_type )
			? strtok( ltrim( $this->mime_type, '/' ), '/' )
			: 'default';

		switch ( $mime_type ) {
			case 'video':
				return [
					'select'       => esc_html__( 'Select video', 'wplibs-form' ),
					'change'       => esc_html__( 'Change video', 'wplibs-form' ),
					'default'      => esc_html__( 'Default', 'wplibs-form' ),
					'remove'       => esc_html__( 'Remove', 'wplibs-form' ),
					'placeholder'  => esc_html__( 'No video selected', 'wplibs-form' ),
					'frame_title'  => esc_html__( 'Select video', 'wplibs-form' ),
					'frame_button' => esc_html__( 'Choose video', 'wplibs-form' ),
				];
			case 'audio':
				return [
					'select'       => esc_html__( 'Select audio', 'wplibs-form' ),
					'change'       => esc_html__( 'Change audio', 'wplibs-form' ),
					'default'      => esc_html__( 'Default', 'wplibs-form' ),
					'remove'       => esc_html__( 'Remove', 'wplibs-form' ),
					'placeholder'  => esc_html__( 'No audio selected', 'wplibs-form' ),
					'frame_title'  => esc_html__( 'Select audio', 'wplibs-form' ),
					'frame_button' => esc_html__( 'Choose audio', 'wplibs-form' ),
				];
			case 'image':
				return [
					'select'       => esc_html__( 'Select image', 'wplibs-form' ),
					'change'       => esc_html__( 'Change image', 'wplibs-form' ),
					'default'      => esc_html__( 'Default', 'wplibs-form' ),
					'remove'       => esc_html__( 'Remove', 'wplibs-form' ),
					'placeholder'  => esc_html__( 'No image selected', 'wplibs-form' ),
					'frame_title'  => esc_html__( 'Select image', 'wplibs-form' ),
					'frame_button' => esc_html__( 'Choose image', 'wplibs-form' ),
				];
			default:
				return [
					'select'       => esc_html__( 'Select file', 'wplibs-form' ),
					'change'       => esc_html__( 'Change file', 'wplibs-form' ),
					'default'      => esc_html__( 'Default', 'wplibs-form' ),
					'remove'       => esc_html__( 'Remove', 'wplibs-form' ),
					'placeholder'  => esc_html__( 'No file selected', 'wplibs-form' ),
					'frame_title'  => esc_html__( 'Select file', 'wplibs-form' ),
					'frame_button' => esc_html__( 'Choose file', 'wplibs-form' ),
				];
		} // End switch().
	}
}
