<?php 
// File: app/View/WeeklyReports/admin_index.ctp


$page_options = array(
	//$this->Html->link(__('Create New %s', __('Weekly Report')), array('action' => 'add')),
);

// content
$th = array(
	'WeeklyReport.name' => array('content' => __('Name'), 'options' => array('sort' => 'WeeklyReport.name')),
	'User.name' => array('content' => __('Owner'), 'options' => array('sort' => 'User.name')),
	'WeeklyReport.report_date' => array('content' => __('Report Date'), 'options' => array('sort' => 'WeeklyReport.report_date')),
	'WeeklyReport.report_date_start' => array('content' => __('Start Date'), 'options' => array('sort' => 'WeeklyReport.report_date_start')),
	'WeeklyReport.report_date_end' => array('content' => __('End Date'), 'options' => array('sort' => 'WeeklyReport.report_date_end')),
	'WeeklyReport.finalized' => array('content' => __('Finalized?'), 'options' => array('sort' => 'WeeklyReport.finalized')),
	'WeeklyReport.finalized_date' => array('content' => __('Finalized Date'), 'options' => array('sort' => 'WeeklyReport.finalized_date')),
	'WeeklyReport.created' => array('content' => __('Created'), 'options' => array('sort' => 'WeeklyReport.created')),
//	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($weekly_reports as $i => $weekly_report)
{
	$td[$i] = array(
		$this->Html->link($weekly_report['WeeklyReport']['name'], array('action' => 'view', $weekly_report['WeeklyReport']['id'])),
		$weekly_report['User']['name'],
		$this->Wrap->niceDay($weekly_report['WeeklyReport']['report_date']),
		$this->Wrap->niceDay($weekly_report['WeeklyReport']['report_date_start']),
		$this->Wrap->niceDay($weekly_report['WeeklyReport']['report_date_end']),
		$this->Wrap->yesNo($weekly_report['WeeklyReport']['finalized']),
		$this->Wrap->niceTime($weekly_report['WeeklyReport']['finalized_date']),
		$this->Wrap->niceTime($weekly_report['WeeklyReport']['created']),
/*
		array(
			$this->Html->link(__('View'), array('action' => 'view', $weekly_report['WeeklyReport']['id'])).
			$this->Html->link(__('Edit'), array('action' => 'edit', $weekly_report['WeeklyReport']['id'])).
			$this->Html->link(__('Delete'), array('action' => 'delete', $weekly_report['WeeklyReport']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
*/
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Weekly Reports'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));