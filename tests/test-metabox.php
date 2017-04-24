<?php

use Skeleton\Metabox;
use Skeleton\CMB2\Group;
use Skeleton\CMB2\Section;

class MetaboxTest extends WP_UnitTestCase {

	function test_definition_metabox() {
		$cmb1 = Metabox::make('metabox-1');
		$cmb2 = new Metabox('metabox-2');

		$this->assertTrue($cmb1 instanceof CMB2);
		$this->assertTrue($cmb2 instanceof CMB2);

		$this->assertEquals($cmb1, CMB2_Boxes::get('metabox-1'));
		$this->assertEquals($cmb2, CMB2_Boxes::get('metabox-2'));
	}

	function test_builder_metabox() {
		$cmb2 = Metabox::make('metabox-with-group')->set([
			'id' => 'overwite-title-will-be-defined',
			'title' => 'Metabox Name',
			'context' => 'normal',
			'priority' => 'high',
			'closed' => false,
			'cmb_styles' => true,
			'show_names' => true,
			'classes' => 'custom-class',
		]);

		$this->assertEquals($cmb2->prop('id'), 'metabox-with-group');
		$this->assertEquals($cmb2->prop('title'), 'Metabox Name');
		$this->assertEquals($cmb2->prop('priority'), 'high');
		$this->assertEquals($cmb2->prop('context'), 'normal');
		$this->assertEquals($cmb2->prop('closed'), false);
		$this->assertEquals($cmb2->prop('cmb_styles'), true);
		$this->assertEquals($cmb2->prop('show_names'), true);
		$this->assertEquals($cmb2->prop('classes'), 'custom-class');

		$cmb2->set_title('Custom Title')
			 ->set_context('advanced')
			 ->set_priority(19)
			 ->set_classes('aaaaa');

		$this->assertEquals($cmb2->prop('title'), 'Custom Title');
		$this->assertEquals($cmb2->prop('context'), 'advanced');
		$this->assertEquals($cmb2->prop('priority'), 19);
		$this->assertEquals($cmb2->prop('classes'), 'aaaaa');

		$cmb2->closed(false)->include_styles(false)->show_field_names(false);
		$this->assertEquals($cmb2->prop('closed'), false);
		$this->assertEquals($cmb2->prop('cmb_styles'), false);
		$this->assertEquals($cmb2->prop('show_names'), false);
	}

	function test_builder_group() {
		$cmb2 = new Metabox('test_group');

		$simple_group = $cmb2->add_group('first-group');
		$this->assertTrue($simple_group instanceof Group);
		$this->assertFieldExists($cmb2, 'first-group');

		$group_callack = $cmb2->add_group('second-group', function($group) {
			$group->set([
				'id' => 'will-not-set',
				'type' => 'will-not-set',

				'name' => 'Name of Group',
				'desc' => null,
			]);

			$group->add_field(array(
				'id' => 'test',
				'type' => 'text',
			));

			$group->add_field(array(
				'id' => 'test2',
				'type' => 'password',
			));
		});

		$fields = $cmb2->prop('fields');

		$this->assertFieldExists($cmb2, 'second-group');
		$this->assertEquals($fields['second-group']['id'], 'second-group');
		$this->assertEquals($fields['second-group']['type'], 'group');

		$this->assertArrayHasKey('name', $fields['second-group']);
		$this->assertArrayHasKey('desc', $fields['second-group']);
		$this->assertArrayHasKey('fields', $fields['second-group']);

		$this->assertArrayHasKey('test', $fields['second-group']['fields']);
		$this->assertArrayHasKey('test2', $fields['second-group']['fields']);
	}

	function test_builder_tab() {
		// TODO...
	}

	/**
	 * @expectedException \LogicException
	 */
	function test_metabox_exception() {
		Metabox::make('metabox-1');
		new Metabox('metabox-1');
	}

	protected function assertFieldExists(Metabox $cmb2, $field_id) {
		$fields = $cmb2->prop('fields');
		$this->assertTrue(isset($fields[$field_id]));
	}
}
