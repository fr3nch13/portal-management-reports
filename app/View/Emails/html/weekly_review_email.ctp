<?php 

$this->Html->setFull(true);

$page_options = array(
	$this->Html->link(__('Review %s', __('Report Items')), array('controller' => 'management_reports_report_items', 'action' => 'review', 'manager' => false)),
);

$this->Local->setSortableChargeCodes($item_charge_codes);
$this->Local->setSortableStates($item_states);
$this->Local->setSortableActivities($item_activities);
$this->Local->setSortableReviewReasons($review_reasons);

// content
$th = array(
	'ReportItem.item_date' => array('content' => __('Date'), 'options' => array('sort' => 'ReportItem.item_date', 'editable' => array('type' => 'date') )),
	'ReportItem.item' => array('content' => __('Item'), 'options' => array('sort' => 'ReportItem.item', 'editable' => array('type' => 'text') )),
	'ManagementReportsReportItem.highlighted' => array('content' => __('Highlighted?'), 'options' => array('sort' => 'ManagementReportsReportItem.highlighted', 'editable' => array('type' => 'checkbox', 'options' => array(0 => '', 1 => __('Yes') )) )),
	'ManagementReportsReportItem.item_state' => array('content' => __('%s %s', __('Item'), __('State')), 'options' => array('sort' => 'ManagementReportsReportItem.item_state', 'editable' => array('type' => 'select', 'options' => $this->Local->getSortableStates()) )),
	'ReportItem.charge_code_id' => array('content' => __('Charge Code'), 'options' => array('sort' => 'ReportItem.charge_code_id', 'editable' => array('type' => 'select', 'options' => $this->Local->getSortableChargeCodes(false, true)) )),
	'ReportItem.activity_id' => array('content' => __('Activity'), 'options' => array('sort' => 'ReportItem.activity_id', 'editable' => array('type' => 'select', 'options' => $this->Local->getSortableActivities(false, true)) )),
	'ReportItem.review_reason_id' => array('content' => __('Review Reason'), 'options' => array('sort' => 'ReportItem.review_reason_id', 'editable' => array('type' => 'select', 'options' => $this->Local->getSortableReviewReasons(false, true)) )),
	'ReportItem.review_details' => array('content' => __('Review Details'), 'options' => array('sort' => 'ReportItem.review_details', 'editable' => array('type' => 'text') )),
//	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach($report_items as $i => $report_item)
{
	
	$td[$i] = array(
		$this->Wrap->niceDay($report_item['ReportItem']['item_date']),
		array(
			$report_item['ReportItem']['item'],
			array('class' => 'min_width_100'),
		),
		($report_item['ManagementReportsReportItem']['highlighted']?__('Yes'):''),
		$this->Local->getSortableStates($report_item['ManagementReportsReportItem']['item_state']),
		$this->Local->getSortableChargeCodes($report_item['ReportItem']['charge_code_id']),
		$this->Local->getSortableActivities($report_item['ReportItem']['activity_id']),
		$this->Local->getSortableReviewReasons($report_item['ReportItem']['review_reason_id']),
		$report_item['ReportItem']['review_details'],
/*
		array(
			$this->Html->link(__('View %s', __('Weekly Report')), array('controller' => 'weekly_reports', 'action' => 'view', $report_item['ManagementReportsReportItem']['id'], 'manager' => false, 'admin' => false)),
			array('class' => 'actions'),
		),
*/
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('%s that need Review', __('Report Items')),
	'page_options' => $page_options,
	'page_description' => __('List of %s that need to be reviewed by you', __('Report Items')),
	'th' => $th,
	'td' => $td,
	'use_search' => false,
	'use_pagination' => false,
	'show_refresh_table' => false,
));
