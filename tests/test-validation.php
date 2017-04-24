<?php

use Skeleton\Support\Validator;

class ValidationTest extends WP_UnitTestCase {

	function test_working_ok() {
		$data = array(
			'data-required'    => 'string',
			'data-equals'      => 'string',
			'data-different'   => 'another-string',
			'data-email'       => 'example@domain.com',
			'data-accepted'    => 'on',
			'data-array'       => array(),
			'data-numeric'     => '1994',
			'data-integer'     => '1993',
			'data-length'      => '1234567890',
			'data-between'     => '25',
		);

		$v = new Validator( $data, array(
			'data-required'    => 'required',
			'data-equals'      => 'equals:data-required',  // It mean same 'data-required' === 'string'
			'data-different'   => 'different:data-required',
			'data-accepted'    => 'accepted',
			'data-array'       => 'array',
			'data-integer'     => 'integer',
			'data-length'      => 'length:10',
			'data-between'     => 'lengthBetween:1,10',
		));

		$this->assertTrue($v->passes());
	}

	function test_string_rules() {

	}

	function test_array_rules() {

	}

	function test_mixed_rules() {

	}
}
