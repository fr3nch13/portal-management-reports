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
	
	foreach($weeklyreports_reportitems as $weeklyreports_reportitem)
	{
		if($weeklyreports_reportitem['WeeklyReportsReportItem']['item_state'] == $i)
		{
			$id = $weeklyreports_reportitem['WeeklyReportsReportItem']['id'];
			$content = array();
			
			$content[] = $this->Html->link(__('Edit'), array('action' => 'edit', $id));
			
			$classes = array(
				'charge_code_class_'. ($weeklyreports_reportitem['ReportItem']['charge_code_id']?$weeklyreports_reportitem['ReportItem']['charge_code_id']:0),
				'activity_class_'. ($weeklyreports_reportitem['ReportItem']['activity_id']?$weeklyreports_reportitem['ReportItem']['activity_id']:0),
			);
			if($weeklyreports_reportitem['WeeklyReportsReportItem']['highlighted'])
			{
				$classes[] = 'sortable_portlet_highlighted';
			}
			
			$items[$id] = array(
				'header' => $weeklyreports_reportitem['ReportItem']['item'],
				'content' => implode(' ', $content),
				'attributes' => array(
					'charge_code_id' => $weeklyreports_reportitem['ReportItem']['charge_code_id'],
					'activity_id' => $weeklyreports_reportitem['ReportItem']['activity_id'],
					'highlighted' => $weeklyreports_reportitem['WeeklyReportsReportItem']['highlighted'],
				),
				//'date' => $this->Wrap->niceDay($weeklyreports_reportitem['ReportItem']['item_date']),
				'date' => date('m/d/Y', strtotime($weeklyreports_reportitem['ReportItem']['item_date'])),
				'class' => implode(' ', $classes),
			);
		}
	}
	
	$columns[$i] = array(
		'title' => __($item_state),
		'items' => $items,
	);
}


echo $this->element('page_sortable', array(
	'page_title' => __('%s Items', __('Weekly Report')),
	'page_options' => $page_options,
	'sortable_column_holders' => $column_holders,
	'sortable_columns' => $columns,
	'sortable_options' => array(
		'ajax_sortable_url' => $this->Html->urlModify(array('action' => 'update_order')),
		'ajax_delete_url' => $this->Html->urlModify(array('action' => 'delete')),
		'ajax_highlight_url' => $this->Html->urlModify(array('action' => 'highlight_assign', $this->passedArgs[0], 1)),
		'ajax_unhighlight_url' => $this->Html->urlModify(array('action' => 'highlight_assign', $this->passedArgs[0], 0)),
		'ajax_charge_code_assign_url' => $this->Html->urlModify(array('action' => 'charge_code_assign')),
		'ajax_activity_assign_url' => $this->Html->urlModify(array('action' => 'activity_assign')),
		'show_highlight_buttons' => true,
	),
	'sortable_charge_codes' => $item_charge_codes,
	'sortable_states' => $item_states,
	'sortable_activities' => $item_activities,
));
