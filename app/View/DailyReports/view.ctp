<?php 
// File: app/View/DailyReports/view.ctp
$page_options = array(
	$this->Html->link(__('Edit %s', __('Report')), array('action' => 'edit', $daily_report['DailyReport']['id'])),
);


$finalize_text = __('Review %s', __('Report'));
$page_options[] = $this->Html->link($finalize_text, array('action' => 'finalize', $daily_report['DailyReport']['id']));

$details = array(
	array('name' => __('Date/Time'), 'value' => $this->Wrap->niceTime($daily_report['DailyReport']['report_date'])),
	array('name' => __('Finalized'), 'value' => $this->Wrap->niceTime($daily_report['DailyReport']['finalized_date'])),
	array('name' => __('Created'), 'value' => $this->Wrap->niceTime($daily_report['DailyReport']['created'])),
);

$stats = array();
$tabs = array();

$stats[] = array(
	'id' => 'item_state_all',
	'name' => __('All %s', __('Items')), 
	'ajax_count_url' => array('controller' => 'daily_reports_report_items', 'action' => 'daily_report', $daily_report['DailyReport']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);

$tabs[] = array(
	'key' => 'ManageItems',
	'title' => __('Manage %s', __('Items')),
	'url' => array('controller' => 'daily_reports_report_items', 'action' => 'daily_report', $daily_report['DailyReport']['id']),
);

$tabs[] = array(
	'key' => 'AddItems',
	'title' => __('Add %s', __('Items')),
	'url' => array('controller' => 'daily_reports_report_items', 'action' => 'add', $daily_report['DailyReport']['id']),
);

$tabs[] = array(
	'key' => 'AllItems',
	'title' => __('All %s', __('Items')),
	'url' => array('controller' => 'daily_reports_report_items', 'action' => 'daily_report_table', $daily_report['DailyReport']['id']),
);


foreach($item_states as $item_state_id => $item_state_name)
{
	$stats[] = array(
		'id' => 'item_state_'. $item_state_id,
		'name' => __('%s %s', $item_state_name, __('Items')), 
		'ajax_count_url' => array('controller' => 'daily_reports_report_items', 'action' => 'daily_report_table', $daily_report['DailyReport']['id'], $item_state_id),
		'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
	);

	$tabs[] = array(
		'key' => 'ItemState'. $item_state_id,
		'title' => __('%s %s', $item_state_name, __('Items')),
		'url' => array('controller' => 'daily_reports_report_items', 'action' => 'daily_report_table', $daily_report['DailyReport']['id'], $item_state_id),
	);

}

$tabs[] = array(
	'key' => 'notes',
	'title' => __('Notes'),
	'content' => $this->Wrap->descView($daily_report['DailyReport']['notes']),
);

$stats[] = array(
	'id' => 'tagsReport',
	'name' => __('Tags'), 
	'ajax_count_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'daily_report', $daily_report['DailyReport']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);	
$tabs[] = array(
	'key' => 'tags',
	'title' => __('Tags'),
	'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'daily_report', $daily_report['DailyReport']['id']),
);


echo $this->element('Utilities.page_view', array(
	'page_title' => __('%s : %s', __('Daily Report'), $daily_report['DailyReport']['name']),
	'page_options' => $page_options,
	'details_title' => __('Details'),
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));