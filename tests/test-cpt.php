<?php

use Skeleton\Post_Type;

class CustomPostTypeTest extends WP_UnitTestCase {

	function test() {
		$sample_post_type = new Post_Type('sample_post_type', 'Sample', 'Samples');
		$sample_post_type->set();
	}
}
