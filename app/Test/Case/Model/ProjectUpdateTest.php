<?php
App::uses('ProjectUpdate', 'Model');

/**
 * ProjectUpdate Test Case
 */
class ProjectUpdateTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.project_update',
		'app.project',
		'app.user',
		'app.management_report_default',
		'app.user_setting',
		'app.login_history',
		'app.report_item',
		'app.charge_code',
		'app.report_item_favorite',
		'app.activity',
		'app.management_report_item',
		'app.management_report',
		'app.management_reports_report_item',
		'app.tag',
		'app.tagged',
		'app.review_reason',
		'app.daily_reports_report_item',
		'app.daily_report',
		'app.weekly_reports_report_item',
		'app.weekly_report',
		'app.project_status',
		'app.project_status_user'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ProjectUpdate = ClassRegistry::init('ProjectUpdate');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ProjectUpdate);

		parent::tearDown();
	}

}
