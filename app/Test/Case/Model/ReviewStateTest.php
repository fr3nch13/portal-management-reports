<?php
App::uses('ReviewState', 'Model');

/**
 * ReviewState Test Case
 *
 */
class ReviewStateTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.review_state',
		'app.equipment',
		'app.equipment_status',
		'app.user',
		'app.org_group',
		'app.user_setting',
		'app.upload',
		'app.exception_update',
		'app.exception_update_reason',
		'app.keyword',
		'app.uploads_keyword',
		'app.login_history',
		'app.discovery_method',
		'app.equipment_detail',
		'app.equipment_type',
		'app.equipment_equipment_types',
		'app.equipment_equipment_type'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ReviewState = ClassRegistry::init('ReviewState');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ReviewState);

		parent::tearDown();
	}

}
