<?php 
// File: app/View/ManagementReportsReportItems/manager_management_report_mine.ctp

$owner = false;
if($management_report['ManagementReport']['user_id'] == AuthComponent::user('id'))
{
	$owner = true;
}

$page_options = array();

if($owner)
{
	$page_options[] = $this->Html->link(__('Add %s %s', __('Staff'), __('Items')), array('action' => 'add', $management_report['ManagementReport']['id']));
//	$page_options[] = $this->Html->link(__('Send Needs Review Email'), array('action' => 'needs_review_email', $management_report['ManagementReport']['id']), array('confirm' => __('Are you sure you want to send the %s emails?', __('Review')) ));
}

$this->Local->setSortableChargeCodes($item_charge_codes);
$this->Local->setSortableStates($item_states);
$this->Local->setSortableActivities($item_activities);
$this->Local->setSortableReviewReasons($review_reasons);


// content
$th = array(
	'ReportItem.item_date' => array('content' => __('Date'), 'options' => array('sort' => 'ReportItem.item_date', 'editable' => array('type' => 'date', 'required' => true, 'default' => $this->Wrap->niceDay($management_report['ManagementReport']['report_date']) ) )),
//	'User.name' => array('content' => __('User'), 'options' => array('sort' => 'User.name')),
	'ReportItem.item' => array('content' => __('Item'), 'options' => array('sort' => 'ReportItem.item', 'editable' => array('type' => 'text', 'required' => true) )),
	'ManagementReportsReportItem.highlighted' => array('content' => __('Highlighted?'), 'options' => array('sort' => 'ManagementReportsReportItem.highlighted', 'editable' => array('type' => 'checkbox', 'options' => array(0 => '', 1 => __('Yes') )) )),
	'ManagementReportsReportItem.item_state' => array('content' => __('%s %s', __('Item'), __('State')), 'options' => array('sort' => 'ManagementReportsReportItem.item_state', 'editable' => array('type' => 'select', 'options' => $this->Local->getSortableStates()) )),
	'User.ChargeCodeName' => array('content' => __('Resource'), 'options' => array('sort' => 'User.charge_code_id')),
	'ReportItem.charge_code_id' => array('content' => __('Charge Code'), 'options' => array('sort' => 'ReportItem.charge_code_id', 'editable' => array('type' => 'select', 'required' => true, 'options' => $this->Local->getSortableChargeCodes(false, true)) )),
	'ReportItem.activity_id' => array('content' => __('Activity'), 'options' => array('sort' => 'ReportItem.activity_id', 'editable' => array('type' => 'select', 'required' => true, 'options' => $this->Local->getSortableActivities(false, true)) )),
//	'ReportItem.review' => array('content' => __('Needs Review'), 'options' => array('sort' => 'ReportItem.review', 'editable' => array('type' => 'checkbox', 'options' => array(0 => '', 1 => __('Yes') )) )),
//	'ReportItem.review_reason_id' => array('content' => __('Review Reason'), 'options' => array('sort' => 'ReportItem.review_reason_id', 'editable' => array('type' => 'select', 'options' => $this->Local->getSortableReviewReasons(false, true)) )),
//	'ReportItem.review_details' => array('content' => __('Review Details'), 'options' => array('sort' => 'ReportItem.review_details', 'editable' => array('type' => 'text') )),
//	'ReportItem.reviewed' => array('content' => __('Reviewed'), 'options' => array('sort' => 'ReportItem.review', 'editable' => array('type' => 'checkbox', 'options' => array(0 => '', 1 => __('Yes') )) )),
//	'ReportItem.reviewed_details' => array('content' => __('Reviewed Details'), 'options' => array('sort' => 'ReportItem.review_details', 'editable' => array('type' => 'text') )),
//	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
foreach ($managementreports_reportitems as $i => $managementreports_reportitem)
{
/*
	$actions = array();
	$actions[] = $this->Html->link(__('Edit'), array('action' => 'edit', $managementreports_reportitem['ManagementReportsReportItem']['id']));
*/
	
	$edit_id = false;
	if($owner)
	{
		$edit_id = array(
			'ReportItem' => $managementreports_reportitem['ReportItem']['id'],
			'ManagementReportsReportItem' => $managementreports_reportitem['ManagementReportsReportItem']['id'],
		);
	}
	
	$resource = false;
	if(isset($managementreports_reportitem['User']['ChargeCode']['name']))
		$resource = $managementreports_reportitem['User']['ChargeCode']['name'];
	
	$td[$i] = array(
		array($this->Wrap->niceDay($managementreports_reportitem['ReportItem']['item_date']), array('value' => $managementreports_reportitem['ReportItem']['item_date'])),
//		$managementreports_reportitem['User']['name'],
		array(
			$managementreports_reportitem['ReportItem']['item'],
			array('class' => 'min_width_100'),
		),
		($managementreports_reportitem['ManagementReportsReportItem']['highlighted']?__('Yes'):''),
		array($this->Local->getSortableStates($managementreports_reportitem['ManagementReportsReportItem']['item_state']), array('value' => $managementreports_reportitem['ManagementReportsReportItem']['item_state'])),
		$resource,
		array($this->Local->getSortableChargeCodes($managementreports_reportitem['ReportItem']['charge_code_id']), array('value' => $managementreports_reportitem['ReportItem']['charge_code_id'])),
		array($this->Local->getSortableActivities($managementreports_reportitem['ReportItem']['activity_id']), array('value' => $managementreports_reportitem['ReportItem']['activity_id'])),
//		($managementreports_reportitem['ReportItem']['review']?__('Yes'):''),
//		$this->Local->getSortableReviewReasons($managementreports_reportitem['ReportItem']['review_reason_id']),
//		$managementreports_reportitem['ReportItem']['review_details'],
//		($managementreports_reportitem['ReportItem']['reviewed']?__('Yes'):''),
//		$managementreports_reportitem['ReportItem']['reviewed_details'],
		'multiselect' => $managementreports_reportitem['ManagementReportsReportItem']['id'],
		'edit_id' => $edit_id,
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('%s %s %s', __('All'), __('My'), __('Items')),
	'page_subtitle' => $management_report['ManagementReport']['completed_title'],
	'page_description' => $management_report['ManagementReport']['completed_text'],
	'page_options' => $page_options,
	'search_placeholder' => __('Report Items'),
	'th' => $th,
	'td' => $td,
	'use_gridedit' => $owner,
	'use_griddelete' => $owner,
	'after_inner_table' => $this->element('favorites_import', array(
		'model' => 'ManagementReportsReportItem',
		'ids' => array(
			'management_report_id' => $management_report['ManagementReport']['id'],
			'user_id' => $management_report['ManagementReport']['user_id'],
		),
	)),
	// multiselect options
	'use_multiselect' => $owner,
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
		'controller' => 'management_reports',
		'action' => 'view',
		$this->params['pass'][0],
	),
));