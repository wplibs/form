<?php

use Skeleton\Support\Encrypter;

class EncrypterTest extends WP_UnitTestCase {
	function test_working_ok() {
		// With a number
		$this->assertEncrypter( 1, 'eNrLtDK0BlwwAvMBEA' );

		// With a string
		$this->assertEncrypter( 'a', 'eNortjK0UkpUsgZcMAmoAfk' );

		// With an array
		$this->assertEncrypter(
			array( 'a', 'adasd', 'name' => 'sadasdas' ),
			'eNpLtDK2qs60MrAutjK0UkpUss60MgSyTYHslMTiFCUg28RKKS8xNxXEtLBSKgaLXCcWK1nXAlwwBswRyw'
		);
	}

	protected function assertEncrypter($data, $payload) {
		$encrypt = Encrypter::encrypt( $data );
		$this->assertEquals( $encrypt, $payload );

		$decrypt = Encrypter::decrypt( $payload );
		$this->assertEquals( $decrypt, $data );
	}
}
