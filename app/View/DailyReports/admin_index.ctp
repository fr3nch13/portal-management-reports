<?php 
// File: app/View/DailyReports/admin_index.ctp


$page_options = array(
	$this->Html->link(__('Create New %s', __('Daily Report')), array('action' => 'add')),
);

// content
$th = array(
	'DailyReport.name' => array('content' => __('Name'), 'options' => array('sort' => 'DailyReport.name')),
	'User.name' => array('content' => __('Owner'), 'options' => array('sort' => 'User.name')),
	'DailyReport.report_date' => array('content' => __('Date/Time'), 'options' => array('sort' => 'DailyReport.report_date')),
	'DailyReport.finalized' => array('content' => __('Finalized?'), 'options' => array('sort' => 'DailyReport.finalized')),
	'DailyReport.finalized_date' => array('content' => __('Finalized Date'), 'options' => array('sort' => 'DailyReport.finalized_date')),
	'DailyReport.created' => array('content' => __('Created'), 'options' => array('sort' => 'DailyReport.created')),
//	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($daily_reports as $i => $daily_report)
{
	$td[$i] = array(
//		$this->Html->link($daily_report['DailyReport']['name'], array('action' => 'view', $daily_report['DailyReport']['id'])),
		$daily_report['DailyReport']['name'],
		$daily_report['User']['name'],
		$this->Wrap->niceTime($daily_report['DailyReport']['report_date']),
		$this->Wrap->yesNo($daily_report['DailyReport']['finalized']),
		$this->Wrap->niceTime($daily_report['DailyReport']['finalized_date']),
//		$this->Wrap->yesNo($daily_report['DailyReport']['last']),
		$this->Wrap->niceTime($daily_report['DailyReport']['created']),
/*
		array(
			$this->Html->link(__('View'), array('action' => 'view', $daily_report['DailyReport']['id'])).
			$this->Html->link(__('Edit'), array('action' => 'edit', $daily_report['DailyReport']['id'])).
			$this->Html->link(__('Delete'), array('action' => 'delete', $daily_report['DailyReport']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
*/
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Daily Reports'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));