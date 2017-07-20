<?php 

$page_options = array(
	$this->Html->link(__('View %s', __('Management Report')), array('action' => 'view', $management_report['ManagementReport']['id'])),
	$this->Html->link(__('View %s Dashboard', __('Management Report')), array('action' => 'view_dashboard', $management_report['ManagementReport']['id'])),
	$this->Html->link(__('View %s %s by %s', __('User'), __('Report Items'), __('Charge Code')), array('action' => 'view_charge_code', $management_report['ManagementReport']['id'])),
	$this->Html->link(__('View %s %s by %s', __('User'), __('Report Items'), __('Activity')), array('action' => 'view_activity', $management_report['ManagementReport']['id'])),
);

$page_options2 = array(
	$this->Html->link(__('Edit %s', __('Report')), array('action' => 'edit', $management_report['ManagementReport']['id'])),
);

$finalize_text = __('Review %s', __('Report'));
$page_options2[] = $this->Html->link($finalize_text, array('action' => 'finalize', $management_report['ManagementReport']['id']));

$stats = array();
$tabs = array();

foreach($item_charge_codes as $item_charge_code)
{
	$stats[] = array(
		'id' => 'item_section_'. $item_charge_code['value'],
		'name' => __('%s %s', $item_charge_code['name'], __('Items')), 
		'ajax_count_url' => array('controller' => 'management_reports_report_items', 'action' => 'management_report_charge_code', $management_report['ManagementReport']['id'], $item_charge_code['value']),
		'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
	);
	
	$tabs[] = array(
		'key' => 'Items_'. $item_charge_code['value'],
		'title' => __('%s %s', $item_charge_code['name'], __('Items')),
		'url' => array('controller' => 'management_reports_report_items', 'action' => 'management_report_charge_code', $management_report['ManagementReport']['id'], $item_charge_code['value']),
	);
}

$this->element('../ManagementReports/manager_view_options');

echo $this->element('Utilities.page_view', array(
	'page_title' => $management_report['ManagementReport']['title'],
	'page_subtitle' => $management_report['ManagementReport']['subtitle'],
	'page_options_title' => $this->get('page_options_title'),
	'page_options' => $this->get('page_options'),
	'page_options_title2' => $this->get('page_options_title2'),
	'page_options2' => $this->get('page_options2'),
	'details_title' => __('Details'),
	'details' => false,
	'stats_title' => __('%s Stats', __('Charge Codes')),
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));