<?php 
// File: app/View/ManagementReports/tag.ctp


$page_options = array(
);

// content
$th = array(
	'ManagementReport.name' => array('content' => __('Name'), 'options' => array('sort' => 'ManagementReport.name')),
	'ManagementReport.created' => array('content' => __('Created'), 'options' => array('sort' => 'ManagementReport.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($management_reports as $i => $management_report)
{
	$td[$i] = array(
		$this->Html->link($management_report['ManagementReport']['name'], array('action' => 'view', $management_report['ManagementReport']['id'])),
		$this->Wrap->niceTime($management_report['ManagementReport']['created']),
		array(
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