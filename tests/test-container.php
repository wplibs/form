<?php

use Skeleton\Container\Container;
use Skeleton\Container\Service_Hooks;

class Unit_Test_Service_Hooks extends Service_Hooks {
	public function register( $container ) {
		$container['param3'] = 'changed';
	}

	public function init( $container ) {
	}
}

class ContainerTest extends WP_UnitTestCase {
	function test_container() {
		$container = new Container(array(
			'param'  => 'value',
			'param2' => 2,
			'param3' => 'value3',
		));

		$container->trigger(new Unit_Test_Service_Hooks($container));

		$this->assertNull($container->get_registered('Unknow_Service_Hooks'));
		$this->assertTrue($container->get_registered('Unit_Test_Service_Hooks') instanceof Unit_Test_Service_Hooks);

		$this->assertEquals(2, $container['param2']);
		$this->assertEquals('value', $container['param']);
		$this->assertEquals('changed', $container['param3']);
	}
}
