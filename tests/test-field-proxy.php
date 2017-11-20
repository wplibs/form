<?php

use Skeleton\Post_Type;
use Skeleton\CMB2\CMB2;
use Skeleton\CMB2\Field_Proxy;

class FieldProxyTest extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		$cmb2 = new CMB2( [ 'id' => 'aaaa' ] );
		$cmb2->add_field(
			[
				'id' => 'field_1',
				'type' => 'text',
				'name' => 'Name',
				'description' => 'Simple description',
				'attributes' => [
					'type' => 'number'
				],
			]
		);

		$this->cmb2 = $cmb2;
	}

	public function testProxy() {
		$field = $this->cmb2->get_field( 'field_1' ) ;
		$proxy = new Field_Proxy( $this->cmb2, $field );

		$this->assertInstanceOf( 'CMB2_Field', $proxy->get_field() );

		// Getter
		$this->assertSame($field->args, $proxy->args);
		$this->assertSame($field->index, $proxy->index);
		$this->assertSame($field->group, $proxy->group);

		// Call method.
		$this->assertSame($field->value(), $proxy->value());
		$this->assertSame($field->label(), $proxy->label());
		$this->assertSame($field->row_classes(), $proxy->row_classes());

		// Setter
		$proxy->args = [ 'a' => 'b' ];
		$this->assertSame($field->args, $proxy->args);
		$proxy->args = 1111;
		$this->assertSame($field->index, $proxy->index);
	}

	/**
	 * @expectedException BadMethodCallException
	 */
	public function testMethodNotFound() {
		$field = $this->cmb2->get_field( 'field_1' ) ;
		$proxy = new Field_Proxy( $this->cmb2, $field );

		$proxy->dont_call_me_please();
	}

	public function testvisibility() {
		$field = $this->cmb2->get_field( 'field_1' ) ;
		$proxy = new Field_Proxy( $this->cmb2, $field );

		$proxy->show();
		$this->assertEquals($field->prop( 'show_on_cb' ), '__return_true' );
		$proxy->hide();
		$this->assertEquals($field->prop( 'show_on_cb' ), '__return_false' );
		$proxy->show();
		$this->assertEquals($field->prop( 'show_on_cb' ), '__return_true' );

		$proxy->toggle();
		$this->assertEquals($field->prop( 'show_on_cb' ), '__return_false' );
		$proxy->toggle();
		$this->assertEquals($field->prop( 'show_on_cb' ), '__return_true' );
	}

	public function testGetAndSetAttribute() {
		$field = $this->cmb2->get_field( 'field_1' ) ;
		$proxy = new Field_Proxy( $this->cmb2, $field );

		$this->assertEquals( $field->args['attributes']['type'], $proxy->get_attribute('type') );

		$field->args['attributes'] = null;
		$this->assertNull( $proxy->args['attributes'] );
		$this->assertNull( $proxy->get_attribute('type') );

		$proxy->set_attribute( 'type', null );
		$this->assertNull( $proxy->get_attribute('type') );

		$proxy->set_attribute( 'type', 'email' );
		$this->assertEquals( 'email', $field->args['attributes']['type'] );

		$proxy->set_attribute( [ 'type' => 'email', 'regex' => 1 ] );
		$this->assertArrayHasKey('type', $field->args['attributes']);
		$this->assertArrayHasKey('regex', $field->args['attributes']);
	}

	public function testGetWithDefaultValue() {
		$field = $this->cmb2->get_field( 'field_1' ) ;
		$proxy = new Field_Proxy( $this->cmb2, $field );

		$this->assertNull( $proxy->get_attribute('nonexists') );
		$this->assertEquals( 'defaultvalue', $proxy->get_attribute('nonexists', 'defaultvalue') );
	}

	public function testSetAndGetProp() {
		$field = $this->cmb2->get_field( 'field_1' ) ;
		$proxy = new Field_Proxy( $this->cmb2, $field );

		$proxy->set_prop('aaaa', 'HAHAHA');
		$this->assertEquals('HAHAHA', $proxy->get_prop('aaaa'));
		$this->assertEquals($field->prop('aaaa'), $proxy->get_prop('aaaa'));
		$this->assertNull($proxy->get_prop('nonexists'));
	}

	public function testGetValue() {
		$field = $this->cmb2->get_field( 'field_1' ) ;
		$proxy = new Field_Proxy( $this->cmb2, $field );

		$this->assertEquals('', $proxy->get_value());
		$this->assertEquals($field->value(), $proxy->get_value());

		$proxy->set_prop('default', 'aaa');
		$this->assertEquals('aaa', $proxy->get_value());
		$this->assertEquals('aaa', $field->get_default());
	}

	public function testHasRenderMethods() {
		$field = $this->cmb2->get_field( 'field_1' ) ;
		$proxy = new Field_Proxy( $this->cmb2, $field );

		$this->assertTrue(method_exists($proxy, 'errors'));
		$this->assertTrue(method_exists($proxy, 'render'));
		$this->assertTrue(method_exists($proxy, 'display'));
	}

	public function testDisplay() {
		$field = $this->cmb2->get_field( 'field_1' ) ;
		$proxy = new Field_Proxy( $this->cmb2, $field );

		ob_start();
		$this->cmb2->render_field( $field->args() );
		$content1 = ob_get_clean();

		ob_start();
		$proxy->display();
		$content2 = ob_get_clean();

		$this->assertEquals($content1, $content2);
	}

	public function testRender() {
		$field = $this->cmb2->get_field( 'field_1' ) ;
		$proxy = new Field_Proxy( $this->cmb2, $field );

		ob_start();
		skeleton_render_field( $field );
		$content1 = ob_get_clean();

		ob_start();
		$proxy->render();
		$content2 = ob_get_clean();

		$this->assertEquals($content1, $content2);
	}

	public function testErrors() {
		$field = $this->cmb2->get_field( 'field_1' ) ;
		$proxy = new Field_Proxy( $this->cmb2, $field );

		$this->cmb2->add_validation_error( 'field_1', 'Error' );

		ob_start();
		skeleton_display_field_errors( $field );
		$content1 = ob_get_clean();

		ob_start();
		$proxy->errors();
		$content2 = ob_get_clean();

		$this->assertEquals($content1, $content2);
	}
}
