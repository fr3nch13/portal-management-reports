<?php 
// File: app/View/ManagementReportItems/manager_management_report.ctp

$owner = false;
if($management_report['ManagementReport']['user_id'] == AuthComponent::user('id'))
{
	$owner = true;
}

$page_options = array();

if($owner)
{
	$page_options[] = $this->Html->link(__('Add %s %s', __('Management'), __('Report Items'), (isset($item_sections[$item_section])?$item_sections[$item_section]:$item_section)), array('action' => 'add', $management_report['ManagementReport']['id']));
}

$page_title = __('All %s %s', __('Management'), __('Report Items'));
$page_subtitle = false;
$page_description = false;

if($item_section)
{
	$page_title = __(' %s %s', (isset($item_sections[$item_section])?$item_sections[$item_section]:$item_section), __('Management'), __('Report Items'));
	$page_subtitle = __('Section Title: %s', $management_report['ManagementReport'][$item_section.'_title']);
	$page_description = __('Section Details: %s', $this->Html->tag('pre', $management_report['ManagementReport'][$item_section.'_text']));
}

$this->Local->setSortableStates($item_sections);


// content
$th = array(
	'ManagementReportItem.item_date' => array('content' => __('Date'), 'options' => array('sort' => 'ManagementReportItem.item_date', 'editable' => array('type' => 'date') )),
	'ManagementReportItem.item' => array('content' => __('Item'), 'options' => array('sort' => 'ManagementReportItem.item', 'editable' => array('type' => 'text') )),
//	'ManagementReportItem.highlighted' => array('content' => __('Highlighted?'), 'options' => array('sort' => 'ManagementReportItem.highlighted')),
	'ManagementReportItem.item_section' => array('content' => __('%s %s', __('Item'), __('Section')), 'options' => array('sort' => 'ManagementReportItem.item_section', 'editable' => array('type' => 'select', 'options' => $this->Local->getSortableStates()) )),
//	'ManagementReportItem.charge_code_id' => array('content' => __('Charge Code'), 'options' => array('sort' => 'ManagementReportItem.charge_code_id')),
//	'ManagementReportItem.activity_id' => array('content' => __('Activity'), 'options' => array('sort' => 'ManagementReportItem.activity_id')),
//	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
	'multiselect' => true,
);

$td = array();
if(isset($item_section) and $item_section)
{
	unset($th['ManagementReportItem.item_section']);
}

$td = array();
foreach ($management_report_items as $i => $management_report_item)
{
	$actions = array();
	if($owner)
	{
		$actions[] = $this->Html->link(__('Edit'), array('action' => 'edit', $management_report_item['ManagementReportItem']['id']));
	}
	$edit_id = array(
		'ManagementReportItem' => $management_report_item['ManagementReportItem']['id'],
	);
	
	$td[$i] = array();
	$td[$i][0] = $this->Wrap->niceDay($management_report_item['ManagementReportItem']['item_date']);
	$td[$i][1] = $management_report_item['ManagementReportItem']['item'];
//	$td[$i][2] = ($management_report_item['ManagementReportItem']['highlighted']?__('Yes'):'');
	$td[$i][3] = $this->Local->getSortableStates($management_report_item['ManagementReportItem']['item_section']);
//	$td[$i][4] = $this->Local->getSortableChargeCodes($management_report_item['ManagementReportItem']['charge_code_id']);
//	$td[$i][5] = $this->Local->getSortableActivities($management_report_item['ManagementReportItem']['activity_id']);
/*
	$td[$i][] = array(
		implode(' ', $actions),
		array('class' => 'actions'),
	);
*/
	$td[$i]['multiselect'] = $management_report_item['ManagementReportItem']['id'];
	if($owner) $td[$i]['edit_id'] = $edit_id;
	
	if(isset($item_section) and $item_section)
	{
		unset($td[$i][3]);
	}
}

echo $this->element('Utilities.page_index', array(
	'page_title' => $page_title,
	'page_subtitle' => $page_subtitle,
	'page_description' => $page_description,
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	'use_gridedit' => $owner,
	'use_griddelete' => $owner,
	// multiselect options
	'use_multiselect' => $owner,
	'multiselect_options' => array(
/*
		'charge_code' => __('Set all selected %s %s to one %s', __('Management'), __('Report Items'), __('Charge Code')),
		'multicharge_code' => __('Set each selected %s %s to a %s individually', __('Management'), __('Report Items'), __('Charge Code')),
		'activity' => __('Set all selected %s %s to one %s', __('Management'), __('Report Items'), __('Activity')),
		'multiactivity' => __('Set each selected %s %s to an %s individually', __('Management'), __('Report Items'), __('Activity')),
*/
		'delete' => __('Delete selected %s %s', __('Management'), __('Report Items')),
	),
	'multiselect_referer' => array(
		'admin' => $this->params['admin'],
		'controller' => 'management_reports',
		'action' => 'view',
		$this->params['pass'][0],
	),
));