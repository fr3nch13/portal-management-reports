<?php 
// File: app/View/ManagementReports/index.ctp


$page_options = array(
	$this->Html->link(__('Create New %s', __('Management Report')), array('action' => 'add')),
);

// content
$th = array(
//	'ManagementReport.name' => array('content' => __('Name'), 'options' => array('sort' => 'ManagementReport.name')),
	'User.name' => array('content' => __('Owner'), 'options' => array('sort' => 'User.name')),
	'ManagementReport.report_date' => array('content' => __('Report Date'), 'options' => array('sort' => 'ManagementReport.report_date')),
	'ManagementReport.report_date_start' => array('content' => __('Start Date'), 'options' => array('sort' => 'ManagementReport.report_date_start')),
	'ManagementReport.report_date_end' => array('content' => __('End Date'), 'options' => array('sort' => 'ManagementReport.report_date_end')),
	'ManagementReport.finalized' => array('content' => __('Finalized?'), 'options' => array('sort' => 'ManagementReport.finalized')),
	'ManagementReport.finalized_date' => array('content' => __('Finalized Date'), 'options' => array('sort' => 'ManagementReport.finalized_date')),
	'ManagementReport.created' => array('content' => __('Created'), 'options' => array('sort' => 'ManagementReport.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($management_reports as $i => $management_report)
{
	$td[$i] = array(
//		$this->Html->link($management_report['ManagementReport']['name'], array('action' => 'view', $management_report['ManagementReport']['id'])),
		$management_report['User']['name'],
		$this->Wrap->niceDay($management_report['ManagementReport']['report_date']),
		$this->Wrap->niceDay($management_report['ManagementReport']['report_date_start']),
		$this->Wrap->niceDay($management_report['ManagementReport']['report_date_end']),
		$this->Wrap->yesNo($management_report['ManagementReport']['finalized']),
		$this->Wrap->niceTime($management_report['ManagementReport']['finalized_date']),
		$this->Wrap->niceTime($management_report['ManagementReport']['created']),
		array(
			$this->Html->link(__('DB'), array('action' => 'view_dashboard', $management_report['ManagementReport']['id'])).
			$this->Html->link(__('View'), array('action' => 'view', $management_report['ManagementReport']['id'])).
			$this->Html->link(__('Edit'), array('action' => 'edit', $management_report['ManagementReport']['id'])).
			$this->Html->link(__('Delete'), array('action' => 'delete', $management_report['ManagementReport']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Management Reports'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));