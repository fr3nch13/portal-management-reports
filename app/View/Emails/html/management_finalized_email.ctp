<?php 
$page_options = array();

$page_description = array();

$page_content = array();

// organize the management_report_items by their sections
$section_items = array();
foreach($management_report_items as $management_report_item)
{
	$item_section = $management_report_item['ManagementReportItem']['item_section'];
	if(!isset($section_items[$item_section])) $section_items[$item_section] = array();
	
	$section_items[$item_section][] = $management_report_item['ManagementReportItem']['item'];
}

if(trim($management_report['ManagementReport']['status_title']) or trim($management_report['ManagementReport']['status_text']))
$page_content[] = $this->Local->makeManagementSection(
	$management_report['ManagementReport']['status_title'],
	$management_report['ManagementReport']['status_text'],
	(isset($section_items['status'])?$this->Html->nestedList($section_items['status']):false)
);

if(trim($management_report['ManagementReport']['planned_title']) or trim($management_report['ManagementReport']['planned_text']))
$page_content[] = $this->Local->makeManagementSection(
	$management_report['ManagementReport']['planned_title'],
	$management_report['ManagementReport']['planned_text'],
	(isset($section_items['planned'])?$this->Html->nestedList($section_items['planned']):false)
);

if(trim($management_report['ManagementReport']['issues_title']) or trim($management_report['ManagementReport']['issues_text']))
$page_content[] = $this->Local->makeManagementSection(
	$management_report['ManagementReport']['issues_title'],
	$management_report['ManagementReport']['issues_text'],
	(isset($section_items['issues'])?$this->Html->nestedList($section_items['issues']):false)
);

if(trim($management_report['ManagementReport']['impact_title']) or trim($management_report['ManagementReport']['impact_text']))
$page_content[] = $this->Local->makeManagementSection(
	$management_report['ManagementReport']['impact_title'],
	$management_report['ManagementReport']['impact_text'],
	(isset($section_items['impact'])?$this->Html->nestedList($section_items['impact']):false)
);

// Highlighted Staff Items

$page_content[] = $this->Local->makeManagementSection(
	$management_report['ManagementReport']['staff_title'],
	$management_report['ManagementReport']['staff_text'],
	$this->requestAction(array('controller' => 'management_reports_report_items', 'action' => 'management_report_highlighted_finalized', $management_report['ManagementReport']['id']), array('return'))
);

// Completed Staff Items
$page_content[] = $this->Local->makeManagementSection(
	$management_report['ManagementReport']['completed_title'],
	$management_report['ManagementReport']['completed_text'],
	$this->requestAction(array('controller' => 'management_reports_report_items', 'action' => 'management_report_finalized', $management_report['ManagementReport']['id'], 1), array('return'))
);

$page_content[] = '<p>&nbsp;</p>';
$page_content[] = $this->Html->tag('h2', __('%s by %s', __('Report Items'), __('Charge Code')));

// list of items by charge codes
foreach($itemized_charge_codes as $itemized_charge_code)
{
	$page_content[] = $this->Local->makeManagementSection(
		__('%s: %s', __('Charge Code'), $itemized_charge_code['name']),
		false,
		$this->requestAction(array('controller' => 'management_reports_report_items', 'action' => 'management_report_charge_code_finalized', $management_report['ManagementReport']['id'], $itemized_charge_code['value']), array('return'))
	);
}

$page_content[] = '<p>&nbsp;</p>';
$page_content[] = $this->Html->tag('h2', __('%s by %s', __('Report Items'), __('Activity')));

// list of items by activity
foreach($itemized_activities as $itemized_activity)
{
	$page_content[] = $this->Local->makeManagementSection(
		__('%s: %s', __('Activity'), $itemized_activity['name']),
		false,
		$this->requestAction(array('controller' => 'management_reports_report_items', 'action' => 'management_report_activity_finalized', $management_report['ManagementReport']['id'], $itemized_activity['value']), array('return'))
	);
}


echo $this->element('Utilities.page_generic', array(
	'page_title' => $management_report['ManagementReport']['title'],
	'page_subtitle' => $management_report['ManagementReport']['subtitle'],
	'page_options' => $page_options,
	'page_description' => $page_description,
	'page_content' => implode("\n", $page_content),
	'use_search' => false,
));

?>
<style type="text/css">
div.management_section 
{
	padding-bottom: 30px;
}
div.management_section h3
{
	padding-bottom: 5px;
	font-size: 130%;
}
div.management_section li
{
	margin-left: 20px;
}
</style>



