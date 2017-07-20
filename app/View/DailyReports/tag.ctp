<?php 
// File: app/View/DailyReports/tag.ctp


$page_options = array(
);

// content
$th = array(
	'DailyReport.name' => array('content' => __('Name'), 'options' => array('sort' => 'DailyReport.name')),
	'DailyReport.created' => array('content' => __('Created'), 'options' => array('sort' => 'DailyReport.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($daily_reports as $i => $daily_report)
{
	$td[$i] = array(
		$this->Html->link($daily_report['DailyReport']['name'], array('action' => 'view', $daily_report['DailyReport']['id'])),
		$this->Wrap->niceTime($daily_report['DailyReport']['created']),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $daily_report['DailyReport']['id'])).
			$this->Html->link(__('Edit'), array('action' => 'edit', $daily_report['DailyReport']['id'])).
			$this->Html->link(__('Delete'), array('action' => 'delete', $daily_report['DailyReport']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Daily Reports'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));