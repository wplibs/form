<?php

use Skeleton\Support\Multidimensional;

class MultidimensionalTest extends WP_UnitTestCase {
	function test_working() {
		$data = array(
			'foo'   => 'bar',
			'data'  => 'value',
			'empty' => '',
			'name' => array(
				'first' => 'Ha',
				'last'  => 'Giang',
				'false' => false,
				'full'  => array( array( 'a' => array( 'aa' => 'Nguyen Van Anh', 'b' => 'b' ) ) )
			),
		);

		// Get testing
		$this->assertEquals(Multidimensional::find($data, 'empty'), '');
		$this->assertEquals(Multidimensional::find($data, 'foo'), 'bar');

		$this->assertNull(Multidimensional::find($data, 'no-exist'));
		$this->assertTrue(Multidimensional::find($data, 'no-exist-default-true', true));
		$this->assertNull(Multidimensional::find($data, 'empty[no-exist]'));

		$this->assertFalse(Multidimensional::find($data, 'name[false]'));
		$this->assertEquals(Multidimensional::find($data, 'name[first]'), 'Ha');
		$this->assertEquals(Multidimensional::find($data, 'name[full][0][a][aa]'), 'Nguyen Van Anh');

		// Update testing
		Multidimensional::replace($data, 'empty', 'no-empty-anymore');
		$this->assertEquals(Multidimensional::find($data, 'empty'), 'no-empty-anymore');

		Multidimensional::replace($data, 'no-exists', 'now-exists');
		$this->assertEquals(Multidimensional::find($data, 'no-exists'), 'now-exists');

		Multidimensional::replace($data, 'no-exists-2[depth]', 'now-exists');
		$this->assertEquals(Multidimensional::find($data, 'no-exists-2[depth]'), 'now-exists');

		// Delete...
		Multidimensional::delete($data, 'data');
		$this->assertNull(Multidimensional::find($data, 'data'));

		Multidimensional::delete($data, 'name[false]');
		$this->assertNull(Multidimensional::find($data, 'name[false]'));

		Multidimensional::delete($data, 'name[full][0][a][aa]');
		$this->assertNull(Multidimensional::find($data, 'name[full][0][a][aa]'));
		$this->assertEquals(Multidimensional::find($data, 'name[full][0][a][b]'), 'b');

		Multidimensional::delete($data, 'name[full][0]');
		$this->assertEmpty(Multidimensional::find($data, 'name[full]'));
	}
}
