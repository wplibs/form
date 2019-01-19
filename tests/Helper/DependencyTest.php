<?php

namespace WPLibs\Form\Test\Helper;

use WPLibs\Rules\Variable;
use WPLibs\Form\Helper\Dependency;

class DependencyTest extends \WP_UnitTestCase {
	public function setUp() {
		parent::setUp();
	}

	public function testSimpleRule() {
		$deps = new Dependency( [
			'username' => 'Van Anh',
			'age'      => 24,
		] );

		$rule  = $deps->get_rule( [ 'age', '=', 100 ] );
		// $this->assertRuleThat( $rule, 0, 'age', 'equal', 100 );

		$rule2 = $deps->get_rule( [ 'age', 100 ] );
		// dump($rule);
	}

	protected function assertRuleThat( $rule, $index, $varname, $operator, $value ) {
		$operands = $rule->getOperands();

		$operand = $operands[ $index ];
		$this->assertInstanceOf( Variable::$operators[ $operator ], $operand );

		$vars = $operand->getOperands();
		if ( 0 === strpos( $operator, 'is_' ) ) {
			$this->assertCount( 1, $vars );
			$this->assertEquals( $varname, $vars[0]->getName() );
		} else {
			$this->assertEquals( $varname, $vars[0]->getName() );
			$this->assertEquals( $value, $vars[1]->getValue() );
		}
	}
}
