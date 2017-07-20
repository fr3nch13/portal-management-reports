<?php 
// File: app/View/Activities/index.ctp

$page_options = array();

if(AuthComponent::user('role') == 'admin')
{
	$page_options[] = $this->Html->link(__('Add %s', __('Activity')), array('action' => 'add', 'admin' => true));
}

// content
$th = array(
	'Activity.name' => array('content' => __('Name'), 'options' => array('sort' => 'Activity.name')),
	'Activity.color_code_hex' => array('content' => __('Color'), 'options' => array('sort' => 'Activity.color_code_hex')),
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
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Activities'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));