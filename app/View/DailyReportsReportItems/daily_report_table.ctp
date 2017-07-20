<?php 
// File: app/View/DailyReportsReportItems/daily_report_table.ctp

$owner = false;
if($daily_report['DailyReport']['user_id'] == AuthComponent::user('id'))
{
	$owner = true;
}

$page_options = array();

if($owner)
{
//	$page_options[] = $this->Html->link(__('Add %s', __('Items')), array('action' => 'add', $daily_report['DailyReport']['id']));
}

$this->Local->setSortableChargeCodes($item_charge_codes);
$this->Local->setSortableStates($item_states);
$this->Local->setSortableActivities($item_activities);


// content
$th = array(
	'ReportItem.item' => array('content' => __('Item'), 'options' => array('sort' => 'ReportItem.item', 'editable' => array('type' => 'text', 'required' => true) )),
	'DailyReportsReportItem.item_state' => array('content' => __('%s %s', __('Item'), __('State')), 'options' => array('sort' => 'DailyReportsReportItem.item_state', 'editable' => array('type' => 'select', 'required' => true, 'options' => $this->Local->getSortableStates()) )),
	'ReportItem.charge_code_id' => array('content' => __('Charge Code'), 'options' => array('sort' => 'ReportItem.charge_code_id', 'editable' => array('type' => 'select', 'required' => true, 'options' => $this->Local->getSortableChargeCodes(false, true)) )),
	'ReportItem.activity_id' => array('content' => __('Activity'), 'options' => array('sort' => 'ReportItem.activity_id', 'editable' => array('type' => 'select', 'required' => true, 'options' => $this->Local->getSortableActivities(false, true)) )),
	'ReportItem.item_date' => array('content' => __('Date'), 'options' => array('sort' => 'ReportItem.item_date', 'editable' => array('type' => 'date', 'required' => true ) )),
	'multiselect' => true,
);

$td = array();
foreach ($dailyreports_reportitems as $i => $dailyreports_reportitem)
{
	$edit_id = false;
	if($owner)
	{
		$edit_id = array(
			'DailyReportsReportItem.daily_report_id' => $dailyreports_reportitem['DailyReportsReportItem']['daily_report_id'],
			'ReportItem' => $dailyreports_reportitem['ReportItem']['id'],
			'DailyReportsReportItem' => $dailyreports_reportitem['DailyReportsReportItem']['id'],
		);
	}
	
	$td[$i] = array(
		$dailyreports_reportitem['ReportItem']['item'],
		array($this->Local->getSortableStates($dailyreports_reportitem['DailyReportsReportItem']['item_state']), array('value' => $dailyreports_reportitem['DailyReportsReportItem']['item_state'])),
		array($this->Local->getSortableChargeCodes($dailyreports_reportitem['ReportItem']['charge_code_id']), array('value' => $dailyreports_reportitem['ReportItem']['charge_code_id'])),
		array($this->Local->getSortableActivities($dailyreports_reportitem['ReportItem']['activity_id']), array('value' => $dailyreports_reportitem['ReportItem']['activity_id'])),
		array($this->Wrap->niceDay($dailyreports_reportitem['ReportItem']['item_date']), array('value' => $dailyreports_reportitem['ReportItem']['item_date'])),
		'multiselect' => $dailyreports_reportitem['DailyReportsReportItem']['id'],
		'edit_id' => $edit_id,
	);
}

$use_multiselect = false;
if($daily_report['DailyReport']['user_id'] == AuthComponent::user('id'))
{
	$use_multiselect = true;
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Report Items'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	// grid/inline edit options
	'use_gridedit' => $use_multiselect,
	'use_gridadd' => $use_multiselect,
	'use_griddelete' => $use_multiselect,
	// add the ability to import a favorite to this report
	'after_inner_table' => $this->element('favorites_import', array(
		'model' => 'DailyReportsReportItem',
		'ids' => array(
			'daily_report_id' => $daily_report['DailyReport']['id'],
			'user_id' => $daily_report['DailyReport']['user_id'],
		),
	)),
	// multiselect options
	'use_multiselect' => $use_multiselect,
	'multiselect_options' => array(
		'charge_code' => __('Set all selected %s to one %s', __('Report Items'), __('Charge Code')),
		'multicharge_code' => __('Set each selected %s to a %s individually', __('Report Items'), __('Charge Code')),
		'activity' => __('Set all selected %s to one %s', __('Report Items'), __('Activity')),
		'multiactivity' => __('Set each selected %s to an %s individually', __('Report Items'), __('Activity')),
		'item_date' => __('Set all selected %s to one %s', __('Report Items'), __('Date')),
		'delete' => __('Delete selected %s', __('Report Items')),
	),
	'multiselect_referer' => array(
		'admin' => $this->params['admin'],
		'controller' => 'daily_reports',
		'action' => 'view',
		$this->params['pass'][0],
	),
));