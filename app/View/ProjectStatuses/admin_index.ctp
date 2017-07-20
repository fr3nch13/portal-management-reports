<?php 
// File: app/View/ProjectStatuses/admin_index.ctp

$page_options = array(
	$this->Html->link(__('Add %s', __('%s %s', __('Project'), __('Status'))), array('action' => 'add')),
);

// content
$th = array(
	'ProjectStatus.name' => array('content' => __('Name'), 'options' => array('sort' => 'Charge Code.name')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($projectStatuses as $i => $projectStatus)
{
	$td[$i] = array(
		$projectStatus['ProjectStatus']['name'],
		
		array(
			$this->Html->link(__('Edit'), array('action' => 'edit', $projectStatus['ProjectStatus']['id'])).
			$this->Html->link(__('Delete'), array('action' => 'delete', $projectStatus['ProjectStatus']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('%s %s', __('Project'), __('Status')),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));