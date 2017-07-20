<?php 
// File: app/View/ProjectFileStates/index.ctp

$page_options = array(
	$this->Html->link(__('Add %s', __('Project File State')), array('action' => 'add')),
);

// content
$th = array(
	'ProjectFileState.name' => array('content' => __('Name'), 'options' => array('sort' => 'ProjectFileState.name')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($projectFileStates as $i => $projectFileState)
{
	$td[$i] = array(
		$projectFileState['ProjectFileState']['name'],
		array(
			$this->Html->link(__('Edit'), array('action' => 'edit', $projectFileState['ProjectFileState']['id'])).
			$this->Html->link(__('Delete'), array('action' => 'delete', $projectFileState['ProjectFileState']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Project File States'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));