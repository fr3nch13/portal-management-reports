<?php 
$page_options = array();

$page_description = array();
$page_description[] = __('Name: %s', $weekly_report['WeeklyReport']['name']);
$page_description[] = __('Report Date: %s', $this->Wrap->niceDay($weekly_report['WeeklyReport']['report_date']));
$page_description[] = __('Start Date: %s', $this->Wrap->niceDay($weekly_report['WeeklyReport']['report_date_start']));
$page_description[] = __('End Date: %s', $this->Wrap->niceDay($weekly_report['WeeklyReport']['report_date_end']));
$page_description[] = __('Tags: %s', $weekly_report['WeeklyReport']['tags']);
$page_description[] = __('User: %s', $weekly_report['User']['name']);
$page_description[] = __('Notes:');
$page_content[] = $weekly_report['WeeklyReport']['notes'];
$page_description = $this->Html->nestedList($page_description);

$this->Local->setSortableChargeCodes($item_charge_codes);
$this->Local->setSortableStates($item_states);
$this->Local->setSortableActivities($item_activities);

// content
$th = array(
	'ReportItem.item' => array('content' => __('Item'), 'options' => array('sort' => 'ReportItem.item')),
	'WeeklyReportsReportItem.highlighted' => array('content' => __('Highlighted'), 'options' => array('sort' => 'WeeklyReportsReportItem.highlighted')),
	'WeeklyReportsReportItem.item_state' => array('content' => __('%s %s', __('Item'), __('State')), 'options' => array('sort' => 'WeeklyReportsReportItem.item_state')),
	'ReportItem.charge_code_id' => array('content' => __('Charge Code'), 'options' => array('sort' => 'ReportItem.charge_code_id')),
	'ReportItem.activity_id' => array('content' => __('Activity'), 'options' => array('sort' => 'ReportItem.activity_id')),
);

$td = array();
foreach($report_items as $i => $report_item)
{
	
	$td[$i] = array(
		$report_item['ReportItem']['item'],
		($report_item['WeeklyReportsReportItem']['highlighted']?__('Yes'):''),
		$this->Local->getSortableStates($report_item['WeeklyReportsReportItem']['item_state']),
		$this->Local->getSortableChargeCodes($report_item['ReportItem']['charge_code_id']),
		$this->Local->getSortableActivities($report_item['ReportItem']['activity_id']),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Report Items'),
	'page_options' => $page_options,
	'page_description' => $page_description,
	'th' => $th,
	'td' => $td,
	'use_search' => false,
	'use_pagination' => false,
));