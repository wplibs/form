<?php

namespace WPLibs\Form\Test;

use WPLibs\Form\Storages\Array_Storage;
use WPLibs\Form\Storages\Single_Option_Storage;
use WPLibs\Form\Storages\Metadata_Storage;

class StorageTest extends \WP_UnitTestCase {
	public function testArrayStorage() {
		$this->assertStorageWork( new Array_Storage( $this->getSampleData() ) );
	}

	public function testOptionStorage() {
		add_option( 'test_option_storage', $this->getSampleData() );

		$option = new Single_Option_Storage( 'test_option_storage' );
		$option->read();

		$this->assertStorageWork( $option );
	}

	public function testMetadataStorage() {
		/*$post = $this->factory->post->create();

		foreach ( $this->getSampleData() as $key => $value ) {
			add_post_meta( $post, $key, $value );
		}

		$this->assertStorageWork( new Metadata_Storage( $post ) );*/
	}

	public function assertStorageWork($data) {
		$this->assertEquals( 'bar', $data->get( 'foo' ) );
		$this->assertEquals( 'Van Anh', $data->get( 'name' ) );
		$this->assertEquals( 'value', $data->get( 'nested.data' ) );
		$this->assertNull( $data->get( 'not_exists' ) );

		$data->add( 'pull', 'request' );
		$data->add( 'push.commit', 'origin' );

		$this->assertEquals( 'request', $data->get( 'pull' ) );
		$this->assertInternalType( 'array', $data->get( 'push' ) );
		$this->assertEquals( 'origin', $data->get( 'push.commit' ) );

		$data->update( 'pull', 'request1' );
		$data->update( 'push.commit', 'origin1' );

		$this->assertEquals( 'request1', $data->get( 'pull' ) );
		$this->assertEquals( 'origin1', $data->get( 'push.commit' ) );

		$data->delete( 'pull' );
		$data->delete( 'push.commit' );

		$this->assertNull( $data->get( 'pull' ) );
		$this->assertNull( $data->get( 'push.commit' ) );
		$this->assertSame( [], $data->get( 'push' ) );
	}

	public function getSampleData() {
		return [
			'foo'    => 'bar',
			'name'   => 'Van Anh',
			'nested' => [ 'data' => 'value' ],
		];
	}
}
