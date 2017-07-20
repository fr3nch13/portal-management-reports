<?php 
// File: app/View/ManagementReportsReportItems/index.ctp

$owner = true;

$page_options = array();

$this->Local->setSortableChargeCodes($item_charge_codes);
$this->Local->setSortableStates($item_states);
$this->Local->setSortableActivities($item_activities);
$this->Local->setSortableReviewReasons($review_reasons);


// content
$th = array(
	'ReportItem.item_date' => array('content' => __('Date'), 'options' => array('sort' => 'ReportItem.item_date', 'editable' => array('type' => 'date', 'required' => true ) )),
	'ManagementReport.report_date' => array('content' => __('Management Report'), 'options' => array('sort' => 'ManagementReport.report_date')),
	'ReportItem.item' => array('content' => __('Item'), 'options' => array('sort' => 'ReportItem.item', 'editable' => array('type' => 'text', 'required' => true) )),
	'ManagementReportsReportItem.highlighted' => array('content' => __('Highlighted?'), 'options' => array('sort' => 'ManagementReportsReportItem.highlighted', 'editable' => array('type' => 'checkbox', 'options' => array(0 => '', 1 => __('Yes') ), 'highlight_toggle' => 1) )),
	'ManagementReportsReportItem.item_state' => array('content' => __('%s %s', __('Item'), __('State')), 'options' => array('sort' => 'ManagementReportsReportItem.item_state', 'editable' => array('type' => 'select', 'options' => $this->Local->getSortableStates()) )),
	'ReportItem.charge_code_id' => array('content' => __('Charge Code'), 'options' => array('sort' => 'ReportItem.charge_code_id', 'editable' => array('type' => 'select', 'required' => true, 'options' => $this->Local->getSortableChargeCodes(false, true)) )),
	'ReportItem.activity_id' => array('content' => __('Activity'), 'options' => array('sort' => 'ReportItem.activity_id', 'editable' => array('type' => 'select', 'required' => true, 'options' => $this->Local->getSortableActivities(false, true)) )),
	'ReportItem.review_reason_id' => array('content' => __('Review Reason'), 'options' => array('sort' => 'ReportItem.review_reason_id' )),
	'ReportItem.review_details' => array('content' => __('Review Details'), 'options' => array('sort' => 'ReportItem.review_details' )),
	'ReportItem.reviewed' => array('content' => __('Reviewed'), 'options' => array('sort' => 'ReportItem.review', 'editable' => array('type' => 'checkbox', 'options' => array(0 => '', 1 => __('Yes'), 'refresh' => true )) )),
	'ReportItem.reviewed_details' => array('content' => __('Reviewed Details'), 'options' => array('sort' => 'ReportItem.review_details', 'editable' => array('type' => 'text') )),
//	'multiselect' => true,
);

$td = array();
foreach ($managementreports_reportitems as $i => $managementreports_reportitem)
{
	$edit_id = false;
	if($owner)
	{
		$edit_id = array(
			'ManagementReportsReportItem.management_report_id' => $managementreports_reportitem['ManagementReportsReportItem']['management_report_id'],
			'ReportItem' => $managementreports_reportitem['ReportItem']['id'],
			'ManagementReportsReportItem' => $managementreports_reportitem['ManagementReportsReportItem']['id'],
		);
	}
	
	$td[$i] = array(
		array($this->Wrap->niceDay($managementreports_reportitem['ReportItem']['item_date']), array('value' => $managementreports_reportitem['ReportItem']['item_date'])),
		$this->Wrap->niceDay($managementreports_reportitem['ManagementReport']['report_date']),
		$managementreports_reportitem['ReportItem']['item'],
		($managementreports_reportitem['ManagementReportsReportItem']['highlighted']?__('Yes'):''),
		array($this->Local->getSortableStates($managementreports_reportitem['ManagementReportsReportItem']['item_state']), array('value' => $managementreports_reportitem['ManagementReportsReportItem']['item_state'])),
		array($this->Local->getSortableChargeCodes($managementreports_reportitem['ReportItem']['charge_code_id']), array('value' => $managementreports_reportitem['ReportItem']['charge_code_id'])),
		array($this->Local->getSortableActivities($managementreports_reportitem['ReportItem']['activity_id']), array('value' => $managementreports_reportitem['ReportItem']['activity_id'])),
		array($this->Local->getSortableReviewReasons($managementreports_reportitem['ReportItem']['review_reason_id']), array('value' => $managementreports_reportitem['ReportItem']['review_reason_id'])),
		$managementreports_reportitem['ReportItem']['review_details'],
		($managementreports_reportitem['ReportItem']['reviewed']?__('Yes'):''),
		$managementreports_reportitem['ReportItem']['reviewed_details'],
//		'multiselect' => $managementreports_reportitem['ManagementReportsReportItem']['id'],
		'edit_id' => $edit_id,
		'highlight' => ($managementreports_reportitem['ManagementReportsReportItem']['highlighted']?true:false),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Reviewed %s %s', __('Management Report'), __('Items')),
	'page_options' => $page_options,
	'search_placeholder' => __('Report Items'),
	'th' => $th,
	'td' => $td,
	// grid/inline edit options
	'use_gridedit' => $owner,
));