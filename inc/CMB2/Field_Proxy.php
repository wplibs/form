<?php
namespace Skeleton\CMB2;

class Field_Proxy {
	/**
	 * CMB2 form instance.
	 *
	 * @var CMB2
	 */
	protected $form;

	/**
	 * The CMB2_Field being operated on.
	 *
	 * @var \CMB2_Field
	 */
	protected $field;

	/**
	 * Create a new proxy instance.
	 *
	 * @param CMB2        $form  CMB2 Form instance.
	 * @param \CMB2_Field $field CMB2 Field instance.
	 */
	public function __construct( CMB2 $form, \CMB2_Field $field ) {
		$this->form = $form;
		$this->field = $field;
	}

	/**
	 * Display the field.
	 *
	 * @return void
	 */
	public function display() {
		$this->form->render_field( $this->field->args() );
	}

	/**
	 * Render the field.
	 *
	 * @return void
	 */
	public function render() {
		skeleton_render_field( $this->field );
	}

	/**
	 * Display the field errors.
	 *
	 * @return void
	 */
	public function errors() {
		skeleton_display_field_errors( $this->field );
	}

	/**
	 * Returns field value.
	 *
	 * @return mixed
	 */
	public function get_value() {
		return $this->field->val_or_default(
			$this->field->value()
		);
	}

	/**
	 * Set the field value.
	 *
	 * @param mixed $value Field value.
	 */
	public function set_value( $value ) {
		$this->field->value = $value;

		return $this;
	}

	/**
	 * Get the field property.
	 *
	 * @param  string $property The property key.
	 * @param  mixed  $default  Default value will be return if key not exists.
	 * @return mixed
	 */
	public function get_prop( $property, $default = null ) {
		return $this->field->prop( $property, $default );
	}

	/**
	 * Set the field property.
	 *
	 * @param  string $property Field property.
	 * @param  mixed  $value    Value to set.
	 * @return $this
	 */
	public function set_prop( $property, $value ) {
		$this->field->set_prop( $property, $value );

		return $this;
	}

	/**
	 * Get the field attribute property.
	 *
	 * @param  string $key     The attribute key.
	 * @param  mixed  $default Default value will be return if key not exists.
	 * @return mixed
	 */
	public function get_attribute( $key, $default = null ) {
		$attributes = $this->field->prop( 'attributes' ) ?: [];

		return array_key_exists( $key, $attributes ) ? $attributes[ $key ] : $default;
	}

	/**
	 * Set the field attribute property.
	 *
	 * @param string $attribute Attribute key name.
	 * @param string $value     Attribute value.
	 */
	public function set_attribute( $attribute, $value = '' ) {
		$attribute  = is_array( $attribute ) ? $attribute : [ $attribute => $value ];
		$attributes = $this->prop( 'attributes' ) ?: [];

		$this->set_prop( 'attributes',
			array_merge( $attributes, $attribute )
		);

		return $this;
	}

	/**
	 * Show the field.
	 *
	 * @return $this
	 */
	public function hide() {
		$this->field->set_prop( 'show_on_cb', '__return_false' );

		return $this;
	}

	/**
	 * Hide the field.
	 *
	 * @return $this
	 */
	public function show() {
		$this->field->set_prop( 'show_on_cb', '__return_true' );

		return $this;
	}

	/**
	 * Set or toggle field visibility.
	 *
	 * @return $this
	 */
	public function toggle() {
		$visibility = $this->field->prop( 'show_on_cb' );

		if ( '__return_true' === $visibility ) {
			$this->hide();
		} else {
			$this->show();
		}

		return $this;
	}

	/**
	 * Get the field.
	 *
	 * @return \CMB2_Field
	 */
	public function get_field() {
		return $this->field;
	}

	/**
	 * Proxy accessing an property onto the field.
	 *
	 * @param  string $key Field property.
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->field->{$key};
	}

	/**
	 * Proxy set an property onto the field.
	 *
	 * @param  string $key   Field property.
	 * @param  mixed  $value Field value.
	 * @return void
	 */
	public function __set( $key, $value ) {
		$this->field->{$key} = $value;
	}

	/**
	 * Proxy a method call onto the field.
	 *
	 * @param  string $method     Method to call.
	 * @param  array  $parameters Method parameters.
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public function __call( $method, $parameters ) {
		if ( ! method_exists( $this->field, $method ) ) {
			throw new \BadMethodCallException( "Method [{$method}] does not exists" );
		}

		return call_user_func_array( [ $this->field, $method ], $parameters );
	}
}
