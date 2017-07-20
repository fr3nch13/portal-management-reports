<?php 
// File: app/View/WeeklyReports/view.ctp
$page_options = array(
	$this->Html->link(__('Edit %s', __('Report')), array('action' => 'edit', $weekly_report['WeeklyReport']['id'])),
);

if($weekly_report['WeeklyReport']['filename'])
{
	$page_options[] = $this->Html->link(__('View %s', __('Excel File')), array('action' => 'view_excel', $weekly_report['WeeklyReport']['id']));
	$page_options[] = $this->Html->link(__('Download %s', __('Excel File')), array('action' => 'download', $weekly_report['WeeklyReport']['id']));
/****
//// Commented out because this will scan and add items from the excel spreadsheet 
//// without actually checking for existing ones, which will cause duplicates
//	$page_options[] = $this->Html->link(__('Rescan %s', __('Report')), array('action' => 'rescan', $weekly_report['WeeklyReport']['id']));
*/
}

$finalize_text = __('Review %s', __('Report'));
$page_options[] = $this->Html->link($finalize_text, array('action' => 'finalize', $weekly_report['WeeklyReport']['id']));

$details = array(
	array('name' => __('Report Date'), 'value' => $this->Wrap->niceDay($weekly_report['WeeklyReport']['report_date'])),
	array('name' => __('Start Date'), 'value' => $this->Wrap->niceDay($weekly_report['WeeklyReport']['report_date_start'])),
	array('name' => __('End Date'), 'value' => $this->Wrap->niceDay($weekly_report['WeeklyReport']['report_date_end'])),
	array('name' => __('Finalized'), 'value' => $this->Wrap->niceTime($weekly_report['WeeklyReport']['finalized_date'])),
	array('name' => __('Created'), 'value' => $this->Wrap->niceTime($weekly_report['WeeklyReport']['created'])),
);

$stats = array();
$tabs = array();

$stats[] = array(
	'id' => 'item_state_all',
	'name' => __('All %s', __('Items')), 
	'ajax_count_url' => array('controller' => 'weekly_reports_report_items', 'action' => 'weekly_report', $weekly_report['WeeklyReport']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);

$tabs[] = array(
	'key' => 'ManageItems',
	'title' => __('Manage %s', __('Items')),
	'url' => array('controller' => 'weekly_reports_report_items', 'action' => 'weekly_report', $weekly_report['WeeklyReport']['id']),
);

$tabs[] = array(
	'key' => 'AddItems',
	'title' => __('Add %s', __('Items')),
	'url' => array('controller' => 'weekly_reports_report_items', 'action' => 'add', $weekly_report['WeeklyReport']['id']),
);

$tabs[] = array(
	'key' => 'AllItems',
	'title' => __('All %s', __('Items')),
	'url' => array('controller' => 'weekly_reports_report_items', 'action' => 'weekly_report_table', $weekly_report['WeeklyReport']['id']),
);

$stats[] = array(
	'id' => 'item_state_highlighted',
	'name' => __('Highlighted %s', __('Items')), 
	'ajax_count_url' => array('controller' => 'weekly_reports_report_items', 'action' => 'weekly_report_highlighted', $weekly_report['WeeklyReport']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);

$tabs[] = array(
	'key' => 'HighlightedItems',
	'title' => __('Highlighted %s', __('Items')),
	'url' => array('controller' => 'weekly_reports_report_items', 'action' => 'weekly_report_highlighted', $weekly_report['WeeklyReport']['id']),
);


foreach($item_states as $item_state_id => $item_state_name)
{
	$stats[] = array(
		'id' => 'item_state_'. $item_state_id,
		'name' => __('%s %s', $item_state_name, __('Items')), 
		'ajax_count_url' => array('controller' => 'weekly_reports_report_items', 'action' => 'weekly_report_table', $weekly_report['WeeklyReport']['id'], $item_state_id),
		'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
	);

	$tabs[] = array(
		'key' => 'ItemState'. $item_state_id,
		'title' => __('%s %s', $item_state_name, __('Items')),
		'url' => array('controller' => 'weekly_reports_report_items', 'action' => 'weekly_report_table', $weekly_report['WeeklyReport']['id'], $item_state_id),
	);

}

$tabs[] = array(
	'key' => 'notes',
	'title' => __('Notes'),
	'content' => $this->Wrap->descView($weekly_report['WeeklyReport']['notes']),
);

$stats[] = array(
	'id' => 'tagsReport',
	'name' => __('Tags'), 
	'ajax_count_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'weekly_report', $weekly_report['WeeklyReport']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);	
$tabs[] = array(
	'key' => 'tags',
	'title' => __('Tags'),
	'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'weekly_report', $weekly_report['WeeklyReport']['id']),
);


echo $this->element('Utilities.page_view', array(
	'page_title' => __('%s : %s', __('Weekly Report'), $weekly_report['WeeklyReport']['name']),
	'page_options' => $page_options,
	'details_title' => __('Details'),
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));