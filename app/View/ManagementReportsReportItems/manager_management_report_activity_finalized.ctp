<?php 
// File: app/View/ManagementReportsReportItems/manager_management_report_finalized.ctp

$this->Local->setSortableChargeCodes($item_charge_codes);
$this->Local->setSortableStates($item_states);
$this->Local->setSortableActivities($item_activities);


// content
$th = array(
	'ReportItem.item_date' => array('content' => __('Date'), 'options' => array('sort' => 'ReportItem.item_date', 'editable' => array('type' => 'date') )),
	'ReportItem.item' => array('content' => __('Item'), 'options' => array('sort' => 'ReportItem.item')),
	'ManagementReportsReportItem.highlighted' => array('content' => __('Highlighted?'), 'options' => array('sort' => 'ManagementReportsReportItem.highlighted')),
	'ManagementReportsReportItem.item_state' => array('content' => __('%s %s', __('Item'), __('State')), 'options' => array('sort' => 'ManagementReportsReportItem.item_state')),
	'User.ChargeCodeName' => array('content' => __('Resource'), 'options' => array('sort' => 'User.charge_code_id')),
	'ReportItem.charge_code_id' => array('content' => __('Charge Code'), 'options' => array('sort' => 'ReportItem.charge_code_id')),
	'ReportItem.activity_id' => array('content' => __('Activity'), 'options' => array('sort' => 'ReportItem.activity_id')),
//	'User.name' => array('content' => __('User'), 'options' => array('sort' => 'User.name')),
);

$td = array();

foreach ($managementreports_reportitems as $i => $managementreports_reportitem)
{
	$resource = false;
	if(isset($managementreports_reportitem['User']['ChargeCode']['name']))
		$resource = $managementreports_reportitem['User']['ChargeCode']['name'];
	
	$td[$i] = array(
		$this->Wrap->niceDay($managementreports_reportitem['ReportItem']['item_date']),
		array($managementreports_reportitem['ReportItem']['item'], array('class' => 'word-wrap')),
		($managementreports_reportitem['ManagementReportsReportItem']['highlighted']?__('Yes'):''),
		$this->Local->getSortableStates($managementreports_reportitem['ManagementReportsReportItem']['item_state']),
		$resource,
		$this->Local->getSortableChargeCodes($managementreports_reportitem['ReportItem']['charge_code_id']),
		$this->Local->getSortableActivities($managementreports_reportitem['ReportItem']['activity_id']),
//		$managementreports_reportitem['User']['name'],
	);
}

echo $this->element('Utilities.table', array(
	'th' => $th,
	'td' => $td,
	'table_stripped' => true,
)); 