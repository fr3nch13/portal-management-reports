<?php 
// File: app/View/Activities/index.ctp

$page_options = array(
	$this->Html->link(__('Add %s', __('Activity')), array('action' => 'add')),
);

// content
$th = array(
	'Activity.name' => array('content' => __('Name'), 'options' => array('sort' => 'Activity.name')),
	'Activity.color_code_hex' => array('content' => __('Color'), 'options' => array('sort' => 'Activity.color_code_hex')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($activities as $i => $activity)
{
	$td[$i] = array(
		$activity['Activity']['name'],
		array(
			$activity['Activity']['color_code_hex'],
			array('style' => 'background-color: '. $activity['Activity']['color_code_rgb'] ),
		),
		array(
			$this->Html->link(__('Edit'), array('action' => 'edit', $activity['Activity']['id'])).
			$this->Html->link(__('Delete'), array('action' => 'delete', $activity['Activity']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Activities'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));