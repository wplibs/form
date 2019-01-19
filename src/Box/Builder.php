<?php

namespace WPLibs\Form\Box;

use Illuminate\Support\Arr;
use WPLibs\Form\Storages\Metadata_Storage;

class Builder extends \WPLibs\Form\Builder {
	/**
	 * The box instance.
	 *
	 * @var Box
	 */
	protected $box;

	/**
	 * Store the context instance.
	 *
	 * @var Context
	 */
	protected $context;

	/**
	 * Constructor.
	 *
	 * @param Box $box
	 */
	public function __construct( Box $box ) {
		$this->box = $box;

		parent::__construct( $box->id, [] );
	}

	/**
	 * Sets the context screen.
	 *
	 * @param Context $context
	 */
	public function set_context( Context $context ) {
		$this->context = $context;
	}

	/**
	 * {@inheritdoc}
	 */
	public function mb_object_type() {
		// We need the context screen to do this.
		if ( ! $this->context ) {
			return '';
		}

		if ( null !== $this->mb_object_type ) {
			return $this->mb_object_type;
		}

		$type = '';
		$box_types = $this->box_types();

		if ( 1 === count( $box_types ) ) {
			$type = Arr::first( $box_types );
		} elseif ( in_array( $this->context->get_object_type(), $box_types ) ) {
			$type = $this->context->get_object_type();
		}

		// Keep the values.
		if ( ! in_array( $type, [ 'user', 'comment', 'term' ] ) ) {
			$type = 'post';
		}

		return $this->mb_object_type = $type;
	}

	/**
	 * {@inheritdoc}
	 */
	public function show_form( $object_id = 0, $object_type = '' ) {
		if ( ! $this->context ) {
			_doing_it_wrong( __FUNCTION__, 'The screen context must be set before showing the form.', null );
			return;
		}

		$form = $this
			->set_storage( $this->get_new_storage( $object_id, $object_type ) )
			// ->set_request( wplibs_request() )
			// ->set_session_store( wplibs_session()->get_store() )
			->set_option( 'resolve_errors', '_errors_' . $this->get_id() )
			->set_option( 'classes', 'wplibs-box wplibs-box--' . $this->get_option( 'layout' ) )
			->get_form();

		// Maps the fields into their sections - if any.
		$this->box->prepare( $form );

		$this->render_form_open( $object_id, $object_type );

		$this->box->nav( 'wplibs-box__nav--' . $this->get_option( 'layout' ) );
		$this->box->main();

		$this->render_form_close( $object_id, $object_type );
	}

	/**
	 * {@inheritdoc}
	 */
	public function save_fields( $object_id = 0, $object_type = '', $data_to_save = [] ) {
		if ( ! $this->context ) {
			_doing_it_wrong( __FUNCTION__, 'The context screen must be set before saving the form.', null );
			return;
		}

		$form = $this
			->set_storage( $this->get_new_storage( $object_id, $object_type ) )
			->get_form()
			->submit( $data_to_save );

		if ( ! $form->is_valid() ) {
			// suru_libs_session()->flash( '_errors_' . $this->get_id(), $form->get_errors() );
		}

		// Save into the storage.
		$form->save();
	}

	/**
	 * Returns new storage.
	 *
	 * @param  int         $id
	 * @param  string|null $object_type
	 * @return \WPLibs\Form\Storages\Metadata_Storage
	 */
	public function get_new_storage( $id, $object_type = null ) {
		$id = $id ?: $this->context->get_object_id();

		return new Metadata_Storage( $id, $object_type ?: $this->mb_object_type() );
	}
}
