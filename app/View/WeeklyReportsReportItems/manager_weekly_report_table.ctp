<?php 
// File: app/View/WeeklyReportsReportItems/weekly_report_table.ctp

$page_options = array();

$this->Local->setSortableChargeCodes($item_charge_codes);
$this->Local->setSortableStates($item_states);
$this->Local->setSortableActivities($item_activities);


// content
$th = array(
	'ReportItem.item_date' => array('content' => __('Date'), 'options' => array('sort' => 'ReportItem.item_date', 'editable' => array('type' => 'date', 'required' => true, 'default' => $this->Wrap->niceDay($weekly_report['WeeklyReport']['report_date']) ) )),
	'ReportItem.item' => array('content' => __('Item'), 'options' => array('sort' => 'ReportItem.item', 'editable' => array('type' => 'text', 'required' => true) )),
	'WeeklyReportsReportItem.highlighted' => array('content' => __('Highlighted?'), 'options' => array('sort' => 'WeeklyReportsReportItem.highlighted', 'editable' => array('type' => 'checkbox', 'options' => array(0 => '', 1 => __('Yes') ), 'highlight_toggle' => 1) )),
	'WeeklyReportsReportItem.item_state' => array('content' => __('%s %s', __('Item'), __('State')), 'options' => array('sort' => 'WeeklyReportsReportItem.item_state', 'editable' => array('type' => 'select', 'options' => $this->Local->getSortableStates()) )),
	'ReportItem.charge_code_id' => array('content' => __('Charge Code'), 'options' => array('sort' => 'ReportItem.charge_code_id', 'editable' => array('type' => 'select', 'required' => true, 'options' => $this->Local->getSortableChargeCodes(false, true)) )),
	'ReportItem.activity_id' => array('content' => __('Activity'), 'options' => array('sort' => 'ReportItem.activity_id', 'editable' => array('type' => 'select', 'required' => true, 'options' => $this->Local->getSortableActivities(false, true)) )),
	'ReportItem.user_id' => array('content' => __('item user id'), 'options' => array('sort' => 'ReportItem.user_id' )),
	'WeeklyReportsReportItem.user_id' => array('content' => __('report item user id'), 'options' => array('sort' => 'WeeklyReportsReportItem.user_id' )),
	//	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();

foreach ($weeklyreports_reportitems as $i => $weeklyreports_reportitem)
{
	$actions = array();
	$edit_id = array(
		'WeeklyReportsReportItem.weekly_report_id' => $weeklyreports_reportitem['WeeklyReportsReportItem']['weekly_report_id'],
		'ReportItem' => $weeklyreports_reportitem['ReportItem']['id'],
		'WeeklyReportsReportItem' => $weeklyreports_reportitem['WeeklyReportsReportItem']['id'],
	);
	
	$td[$i] = array(
		array($this->Wrap->niceDay($weeklyreports_reportitem['ReportItem']['item_date']), array('value' =>$weeklyreports_reportitem['ReportItem']['item_date'])),
		$weeklyreports_reportitem['ReportItem']['item'],
		array(($weeklyreports_reportitem['WeeklyReportsReportItem']['highlighted']?__('Yes'):''), array('class' => 'nowrap', 'value' => ($weeklyreports_reportitem['WeeklyReportsReportItem']['highlighted']?1:0))),
		array($this->Local->getSortableStates($weeklyreports_reportitem['WeeklyReportsReportItem']['item_state']), array('class' => 'nowrap', 'value' => ($weeklyreports_reportitem['WeeklyReportsReportItem']['item_state']?$weeklyreports_reportitem['WeeklyReportsReportItem']['item_state']:''))),
		array($this->Local->getSortableChargeCodes($weeklyreports_reportitem['ReportItem']['charge_code_id']), array('class' => 'nowrap', 'value' => ($weeklyreports_reportitem['ReportItem']['charge_code_id']?$weeklyreports_reportitem['ReportItem']['charge_code_id']:''))),
		array($this->Local->getSortableActivities($weeklyreports_reportitem['ReportItem']['activity_id']), array('class' => 'nowrap', 'value' => ($weeklyreports_reportitem['ReportItem']['activity_id']?$weeklyreports_reportitem['ReportItem']['activity_id']:''))),
		$weeklyreports_reportitem['ReportItem']['user_id'],
		$weeklyreports_reportitem['WeeklyReportsReportItem']['user_id']. ' ',
		'multiselect' => $weeklyreports_reportitem['WeeklyReportsReportItem']['id'],
		'edit_id' => $edit_id,
		'highlight' => ($weeklyreports_reportitem['WeeklyReportsReportItem']['highlighted']?true:false),
	);
}

$use_multiselect = true;

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Report Items'),
	'page_options' => $page_options,
	'search_placeholder' => __('Report Items'),
	'th' => $th,
	'td' => $td,
	// grid/inline edit options
	'use_gridedit' => $use_multiselect,
	'use_gridadd' => $use_multiselect,
	'use_griddelete' => $use_multiselect,
	// multiselect options
//	'use_multiselect' => $use_multiselect,
	'use_multiselect' => false,
	'multiselect_options' => array(
		'charge_code' => __('Set all selected %s to one %s', __('Report Items'), __('Charge Code')),
		'multicharge_code' => __('Set each selected %s to a %s individually', __('Report Items'), __('Charge Code')),
		'activity' => __('Set all selected %s to one %s', __('Report Items'), __('Activity')),
		'multiactivity' => __('Set each selected %s to an %s individually', __('Report Items'), __('Activity')),
		'delete' => __('Delete selected %s', __('Report Items')),
	),
	'multiselect_referer' => array(
		'admin' => $this->params['admin'],
		'manager' => $this->params['manager'],
		'controller' => 'weekly_reports',
		'action' => 'view',
		(isset($this->params['pass'][0])?$this->params['pass'][0]:0),
	),
));