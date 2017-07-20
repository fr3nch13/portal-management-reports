<?php

$page_options = array(
	$this->Html->link(__('View %s', __('Report')), array('action' => 'view', $weekly_report['WeeklyReport']['id'])),
	$this->Html->link(__('Download %s', __('Excel File')), array('action' => 'download', $weekly_report['WeeklyReport']['id'])),
);

echo $this->element('Utilities.page_generic', array(
	'page_title' => __('Excel file for: %s', $weekly_report['WeeklyReport']['name']),
	'page_options' => $page_options,
	'page_content' => $excel_html,
));