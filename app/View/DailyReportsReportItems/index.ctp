<?php 
// File: app/View/DailyReportsReportItems/index.ctp

$this->Local->setSortableChargeCodes($item_charge_codes);
$this->Local->setSortableStates($item_states);
$this->Local->setSortableActivities($item_activities);

$page_title = __('All %s %s', __('Daily Report'), __('Items'));

if($item_state !== false)
{
	$page_title = __('%s %s - State: %s', __('Daily Report'), __('Items'), $this->Local->getSortableStates($item_state));
	
}

$owner = true;

$page_options = array();

foreach($item_states as $v => $name)
{
	$page_options[] = $this->Html->link(__('Filter: %s', $name), array('action' => 'index', $v));
}


// content
$th = array(
	'ReportItem.item_date' => array('content' => __('Date'), 'options' => array('sort' => 'ReportItem.item_date', 'editable' => array('type' => 'date', 'required' => true ) )),
	'DailyReport.name' => array('content' => __('Daily Report'), 'options' => array('sort' => 'DailyReport.name')),
	'ReportItem.item' => array('content' => __('Item'), 'options' => array('sort' => 'ReportItem.item', 'editable' => array('type' => 'text', 'required' => true) )),
	'DailyReportsReportItem.item_state' => array('content' => __('%s %s', __('Item'), __('State')), 'options' => array('sort' => 'DailyReportsReportItem.item_state', 'editable' => array('type' => 'select', 'options' => $this->Local->getSortableStates()) )),
	'ReportItem.charge_code_id' => array('content' => __('Charge Code'), 'options' => array('sort' => 'ReportItem.charge_code_id', 'editable' => array('type' => 'select', 'required' => true, 'options' => $this->Local->getSortableChargeCodes(false, true)) )),
	'ReportItem.activity_id' => array('content' => __('Activity'), 'options' => array('sort' => 'ReportItem.activity_id', 'editable' => array('type' => 'select', 'required' => true, 'options' => $this->Local->getSortableActivities(false, true)) )),
//	'multiselect' => true,
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
	
	$highlight = false;
	if(
		!$dailyreports_reportitem['ReportItem']['charge_code_id'] 
		or !$dailyreports_reportitem['ReportItem']['activity_id'] 
		or !$dailyreports_reportitem['DailyReportsReportItem']['item_state']
	)
	{
		$highlight = true;
	}
	
	$td[$i] = array(
		array($this->Wrap->niceDay($dailyreports_reportitem['ReportItem']['item_date']), array('value' => $dailyreports_reportitem['ReportItem']['item_date'])),
		$this->Html->link($dailyreports_reportitem['DailyReport']['name'], array('controller' => 'daily_reports', 'action' => 'view', $dailyreports_reportitem['DailyReport']['id'])),
		$dailyreports_reportitem['ReportItem']['item'],
		array($this->Local->getSortableStates($dailyreports_reportitem['DailyReportsReportItem']['item_state']), array('value' => $dailyreports_reportitem['DailyReportsReportItem']['item_state'])),
		array($this->Local->getSortableChargeCodes($dailyreports_reportitem['ReportItem']['charge_code_id']), array('value' => $dailyreports_reportitem['ReportItem']['charge_code_id'])),
		array($this->Local->getSortableActivities($dailyreports_reportitem['ReportItem']['activity_id']), array('value' => $dailyreports_reportitem['ReportItem']['activity_id'])),
//		'multiselect' => $dailyreports_reportitem['DailyReportsReportItem']['id'],
		'edit_id' => $edit_id,
		'highlight' => $highlight,
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => $page_title,
	'page_options' => $page_options,
	'search_placeholder' => __('Report Items'),
	'th' => $th,
	'td' => $td,
	// grid/inline edit options
	'use_gridedit' => $owner,
));