<?php

$page_options = array();

$sortable_options = array(
);
$columns = array();
$column_holders = array();

$this->Local->setSortableChargeCodes($item_charge_codes);
$this->Local->setSortableStates($item_states);
$this->Local->setSortableActivities($item_activities);

foreach($item_states as $i => $item_state)
{
	$items = array();
	
	foreach($dailyreports_reportitems as $dailyreports_reportitem)
	{
		if($dailyreports_reportitem['DailyReportsReportItem']['item_state'] == $i)
		{
			$id = $dailyreports_reportitem['DailyReportsReportItem']['id'];
			$content = array();
			
			$content[] = $this->Html->link(__('Edit'), array('action' => 'edit', $id));
			
			$classes = array(
				'charge_code_class_'. ($dailyreports_reportitem['ReportItem']['charge_code_id']?$dailyreports_reportitem['ReportItem']['charge_code_id']:0),
				'activity_class_'. ($dailyreports_reportitem['ReportItem']['activity_id']?$dailyreports_reportitem['ReportItem']['activity_id']:0),
			);
			
			$items[$id] = array(
				'header' => $dailyreports_reportitem['ReportItem']['item'],
				'content' => implode(' ', $content),
				'attributes' => array(
					'charge_code_id' => $dailyreports_reportitem['ReportItem']['charge_code_id'],
					'activity_id' => $dailyreports_reportitem['ReportItem']['activity_id']
				),
				'class' => implode(' ', $classes),
			);
		}
	}
	
	$columns[$i] = array(
		'title' => __($item_state),
		'items' => $items,
		'add_link' => array('action' => 'add', $daily_report['DailyReport']['id'], $i),
	);
}
//js-masonry

echo $this->element('page_sortable', array(
	'page_title' => __('%s Items', __('Daily Report')),
	'page_options' => $page_options,
	'sortable_column_holders' => $column_holders,
	'sortable_columns' => $columns,
	'sortable_options' => array(
		'ajax_sortable_url' => $this->Html->urlModify(array('action' => 'update_order')),
		'ajax_delete_url' => $this->Html->urlModify(array('action' => 'delete')),
		'ajax_charge_code_assign_url' => $this->Html->urlModify(array('action' => 'charge_code_assign')),
		'ajax_activity_assign_url' => $this->Html->urlModify(array('action' => 'activity_assign')),
	),
	'sortable_charge_codes' => $item_charge_codes,
	'sortable_states' => $item_states,
	'sortable_activities' => $item_activities,
));
