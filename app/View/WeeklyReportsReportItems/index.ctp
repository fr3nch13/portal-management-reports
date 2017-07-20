<?php 
// File: app/View/WeeklyReportsReportItems/index.ctp

$this->Local->setSortableChargeCodes($item_charge_codes);
$this->Local->setSortableStates($item_states);
$this->Local->setSortableActivities($item_activities);

$page_title = __('All %s %s', __('Weekly Report'), __('Items'));

if($item_state !== false)
{
	$page_title = __('%s %s - State: %s', __('Weekly Report'), __('Items'), $this->Local->getSortableStates($item_state));
	
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
	'WeeklyReport.name' => array('content' => __('Weekly Report'), 'options' => array('sort' => 'WeeklyReport.name')),
	'ReportItem.item' => array('content' => __('Item'), 'options' => array('sort' => 'ReportItem.item', 'editable' => array('type' => 'text', 'required' => true) )),
	'WeeklyReportsReportItem.highlighted' => array('content' => __('Highlighted?'), 'options' => array('sort' => 'WeeklyReportsReportItem.highlighted', 'editable' => array('type' => 'checkbox', 'options' => array(0 => '', 1 => __('Yes') ), 'highlight_toggle' => 1) )),
	'WeeklyReportsReportItem.item_state' => array('content' => __('%s %s', __('Item'), __('State')), 'options' => array('sort' => 'WeeklyReportsReportItem.item_state', 'editable' => array('type' => 'select', 'options' => $this->Local->getSortableStates()) )),
	'ReportItem.charge_code_id' => array('content' => __('Charge Code'), 'options' => array('sort' => 'ReportItem.charge_code_id', 'editable' => array('type' => 'select', 'required' => true, 'options' => $this->Local->getSortableChargeCodes(false, true)) )),
	'ReportItem.activity_id' => array('content' => __('Activity'), 'options' => array('sort' => 'ReportItem.activity_id', 'editable' => array('type' => 'select', 'required' => true, 'options' => $this->Local->getSortableActivities(false, true)) )),
//	'multiselect' => true,
);

$td = array();
foreach ($weeklyreports_reportitems as $i => $weeklyreports_reportitem)
{
	$edit_id = false;
	if($owner)
	{
		$edit_id = array(
			'WeeklyReportsReportItem.weekly_report_id' => $weeklyreports_reportitem['WeeklyReportsReportItem']['weekly_report_id'],
			'ReportItem' => $weeklyreports_reportitem['ReportItem']['id'],
			'WeeklyReportsReportItem' => $weeklyreports_reportitem['WeeklyReportsReportItem']['id'],
		);
	}
	
	$td[$i] = array(
		array($this->Wrap->niceDay($weeklyreports_reportitem['ReportItem']['item_date']), array('value' =>$weeklyreports_reportitem['ReportItem']['item_date'])),
		$this->Html->link($weeklyreports_reportitem['WeeklyReport']['name'], array('controller' => 'weekly_reports', 'action' => 'view', $weeklyreports_reportitem['WeeklyReport']['id'])),
		$weeklyreports_reportitem['ReportItem']['item'],
		array(($weeklyreports_reportitem['WeeklyReportsReportItem']['highlighted']?__('Yes'):''), array('class' => 'nowrap', 'value' => ($weeklyreports_reportitem['WeeklyReportsReportItem']['highlighted']?1:0))),
		array($this->Local->getSortableStates($weeklyreports_reportitem['WeeklyReportsReportItem']['item_state']), array('class' => 'nowrap', 'value' => ($weeklyreports_reportitem['WeeklyReportsReportItem']['item_state']?$weeklyreports_reportitem['WeeklyReportsReportItem']['item_state']:''))),
		array($this->Local->getSortableChargeCodes($weeklyreports_reportitem['ReportItem']['charge_code_id']), array('class' => 'nowrap', 'value' => ($weeklyreports_reportitem['ReportItem']['charge_code_id']?$weeklyreports_reportitem['ReportItem']['charge_code_id']:''))),
		array($this->Local->getSortableActivities($weeklyreports_reportitem['ReportItem']['activity_id']), array('class' => 'nowrap', 'value' => ($weeklyreports_reportitem['ReportItem']['activity_id']?$weeklyreports_reportitem['ReportItem']['activity_id']:''))),
//		'multiselect' => $weeklyreports_reportitem['WeeklyReportsReportItem']['id'],
		'edit_id' => $edit_id,
		'highlight' => ($weeklyreports_reportitem['WeeklyReportsReportItem']['highlighted']?true:false),
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