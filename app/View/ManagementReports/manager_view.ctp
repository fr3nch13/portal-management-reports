<?php 

$details = array(
	array('name' => __('Owner'), 'value' => $management_report['User']['name']),
	array('name' => __('Report Date'), 'value' => $this->Wrap->niceDay($management_report['ManagementReport']['report_date'])),
	array('name' => __('Start Date'), 'value' => $this->Wrap->niceDay($management_report['ManagementReport']['report_date_start'])),
	array('name' => __('End Date'), 'value' => $this->Wrap->niceDay($management_report['ManagementReport']['report_date_end'])),
	array('name' => __('Finalized'), 'value' => $this->Wrap->niceTime($management_report['ManagementReport']['finalized_date'])),
	array('name' => __('Created'), 'value' => $this->Wrap->niceTime($management_report['ManagementReport']['created'])),
);

$stats = [];
$tabs = [];

$stats['items_mine'] = $tabs['items_mine'] = [
	'id' => 'items_mine',
	'name' => __('%s %s %s', __('All'), __('My'), __('Items')),
	'ajax_url' => ['controller' => 'management_reports_report_items', 'action' => 'manager_management_report_mine', $management_report['ManagementReport']['id']],
];
$stats['items_all'] = $tabs['items_all'] = [
	'id' => 'items_all',
	'name' => __('%s %s %s', __('All'), __('Staff'), __('Items')),
	'ajax_url' => ['controller' => 'management_reports_report_items', 'action' => 'management_report_table', $management_report['ManagementReport']['id']],
];
$stats['items_completed'] = $tabs['items_completed'] = [
	'id' => 'items_completed',
	'name' => __('%s %s %s', __('Completed'), __('Staff'), __('Items')),
	'ajax_url' => ['controller' => 'management_reports_report_items', 'action' => 'management_report_table', $management_report['ManagementReport']['id'], 1],
];
$stats['items_highlighted'] = $tabs['items_highlighted'] = [
	'id' => 'items_highlighted',
	'name' => __('%s %s %s', __('Highlighted'), __('Staff'), __('Items')),
	'ajax_url' => ['controller' => 'management_reports_report_items', 'action' => 'management_report_highlighted', $management_report['ManagementReport']['id']],
];
$tabs['add_items'] = [
	'id' => 'add_items',
	'name' => __('Add %s %s', __('Management'), __('Items')),
	'ajax_url' => ['controller' => 'management_report_items', 'action' => 'add', $management_report['ManagementReport']['id']],
];
$stats['section_all'] = $tabs['section_all'] = [
	'id' => 'section_all',
	'name' => __('%s %s %s', __('All'), __('Section'), __('Items')),
	'ajax_url' => ['controller' => 'management_report_items', 'action' => 'management_report', $management_report['ManagementReport']['id']],
];
if(isset($item_sections['staff'])) unset($item_sections['staff']);
if(isset($item_sections['completed'])) unset($item_sections['completed']);
foreach($item_sections as $item_section_key => $item_section_name)
{

	$stats['section_'.$item_section_key] = $tabs['section_'.$item_section_key] = [
		'id' => 'section_'.$item_section_key,
		'name' => __('%s %s', $item_section_name, __('Items')),
		'ajax_url' => ['controller' => 'management_report_items', 'action' => 'management_report', $management_report['ManagementReport']['id'], $item_section_key],
	];
}
$tabs['notes'] = [
	'id' => 'notes',
	'name' => __('Notes'),
	'content' => $this->Wrap->descView($management_report['ManagementReport']['notes']),
];
$stats['tags'] = $tabs['tags'] = [
	'id' => 'tags',
	'name' => __('Tags'),
	'ajax_url' => ['plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'management_report', $management_report['ManagementReport']['id'], 'manager' => false],
];

$this->element('../ManagementReports/manager_view_options');

echo $this->element('Utilities.page_view', array(
	'page_title' => $management_report['ManagementReport']['title'],
	'page_subtitle' => $management_report['ManagementReport']['subtitle'],
	'page_options_title' => $this->get('page_options_title'),
	'page_options' => $this->get('page_options'),
	'page_options_title2' => $this->get('page_options_title2'),
	'page_options2' => $this->get('page_options2'),
	'details_title' => __('Details'),
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));