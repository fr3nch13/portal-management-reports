<?php 
// File: app/View/ManagementReportsReportItems/manager_management_report.ctp

$owner = false;
if($management_report['ManagementReport']['user_id'] == AuthComponent::user('id'))
{
	$owner = true;
}

$page_options = array();

if($owner)
{
//	$page_options[] = $this->Html->link(__('Add %s', __('Items')), array('action' => 'add', $management_report['ManagementReport']['id']));
}

$this->Local->setSortableChargeCodes($item_charge_codes);
$this->Local->setSortableStates($item_states);
$this->Local->setSortableActivities($item_activities);


// content
$th = array(
	'ReportItem.item_date' => array('content' => __('Date'), 'options' => array('sort' => 'ReportItem.item_date')),
	'ReportItem.item' => array('content' => __('Item'), 'options' => array('sort' => 'ReportItem.item')),
	'ManagementReportsReportItem.highlighted' => array('content' => __('Highlighted?'), 'options' => array('sort' => 'ManagementReportsReportItem.highlighted')),
	'ManagementReportsReportItem.item_state' => array('content' => __('%s %s', __('Item'), __('State')), 'options' => array('sort' => 'ManagementReportsReportItem.item_state')),
	'ReportItem.charge_code_id' => array('content' => __('Charge Code'), 'options' => array('sort' => 'ReportItem.charge_code_id')),
	'ReportItem.activity_id' => array('content' => __('Activity'), 'options' => array('sort' => 'ReportItem.activity_id')),
//	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($managementreports_reportitems as $i => $managementreports_reportitem)
{
	$actions = array();
	if($owner)
	{
		$actions[] = $this->Html->link(__('Edit'), array('action' => 'edit', $managementreports_reportitem['ManagementReportsReportItem']['id']));
	}
	
	$td[$i] = array(
		array($this->Wrap->niceDay($managementreports_reportitem['ReportItem']['item_date']), array('value' => $managementreports_reportitem['ReportItem']['item_date'])),
		$managementreports_reportitem['ReportItem']['item'],
		($managementreports_reportitem['ManagementReportsReportItem']['highlighted']?__('Yes'):''),
		array($this->Local->getSortableStates($managementreports_reportitem['ManagementReportsReportItem']['item_state']), array('value' => $managementreports_reportitem['ManagementReportsReportItem']['item_state'])),
		array($this->Local->getSortableChargeCodes($managementreports_reportitem['ReportItem']['charge_code_id']), array('value' => $managementreports_reportitem['ReportItem']['charge_code_id'])),
		array($this->Local->getSortableActivities($managementreports_reportitem['ReportItem']['activity_id']), array('value' => $managementreports_reportitem['ReportItem']['activity_id'])),
/*
		array(
			implode(' ', $actions),
			array('class' => 'actions'),
		),
*/
		'multiselect' => $managementreports_reportitem['ManagementReportsReportItem']['id'],
	);
}

$use_multiselect = false;
if($management_report['ManagementReport']['user_id'] == AuthComponent::user('id'))
{
	$use_multiselect = true;
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Report Items'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	// multiselect options
	'use_multiselect' => $use_multiselect,
	'multiselect_options' => array(
		'charge_code' => __('Set all selected %s to one %s', __('Report Items'), __('Charge Code')),
		'multicharge_code' => __('Set each selected %s to a %s individually', __('Report Items'), __('Charge Code')),
		'activity' => __('Set all selected %s to one %s', __('Report Items'), __('Activity')),
		'multiactivity' => __('Set each selected %s to an %s individually', __('Report Items'), __('Activity')),
		'delete' => __('Delete selected %s', __('Report Items')),
	),
	'multiselect_referer' => array(
		'admin' => $this->params['admin'],
		'controller' => 'management_reports',
		'action' => 'view',
		$this->params['pass'][0],
	),
));