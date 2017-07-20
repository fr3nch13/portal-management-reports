<?php

$dashboard_blocks = [];
$dashboard_blocks['mr_db_block_activity_'.$management_report_id] = ['action' => 'db_block_activity', $management_report_id];
$dashboard_blocks['mr_db_block_charge_code_'.$management_report_id] = ['action' => 'db_block_charge_code', $management_report_id];

foreach($chargeCodes as $chargeCode)
{
	$charge_code_id = $chargeCode['ChargeCode']['id'];
	$dashboard_blocks['mr_db_block_charge_code_c_'.$management_report_id.'_'.$charge_code_id] = ['action' => 'db_block_charge_code_consolidated', $management_report_id, $charge_code_id];
}

foreach($users as $user)
{
	$user_id = $user['User']['id'];
	$dashboard_blocks['mr_db_block_charge_code_'.$management_report_id.'_'.$user_id] = ['action' => 'db_block_charge_code', $management_report_id, $user_id];
}

$tabs = [];
$tabs['charge_codes'] = [
	'id' => 'charge_codes',
	'name' => __('Charge Code Matrix'), 
	'ajax_url' => ['action' => 'db_tab_charge_code', $management_report_id],
];
$tabs['activities'] = [
	'id' => 'activities',
	'name' => __('Activity Matrix'), 
	'ajax_url' => ['action' => 'db_tab_activity', $management_report_id],
];

$this->element('../ManagementReports/manager_view_options');

echo $this->element('Utilities.page_dashboard', [
	'page_title' => $management_report['ManagementReport']['title'],
	'page_subtitle' => $management_report['ManagementReport']['subtitle'],
	'page_options_title' => $this->get('page_options_title'),
	'page_options' => $this->get('page_options'),
	'page_options_title2' => $this->get('page_options_title2'),
	'page_options2' => $this->get('page_options2'),
	'dashboard_blocks' => $dashboard_blocks,
	'tabs' => $tabs,
]);