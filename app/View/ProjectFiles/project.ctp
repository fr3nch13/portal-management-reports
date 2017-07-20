<?php 
// File: app/View/ProjectFiles/project.ctp

$page_options = array();
$page_options[] = $this->Html->link(__('Add %s %s', __('Project'), __('File')), array('action' => 'add', $project['Project']['id']));

// content
$th = array(
	'ProjectFile.filename' => array('content' => __('File Name'), 'options' => array('sort' => 'ProjectFile.filename')),
	'ProjectFile.nicename' => array('content' => __('Friendly Name'), 'options' => array('sort' => 'ProjectFile.nicename')),
	'ProjectFileState.name' => array('content' => __('File State'), 'options' => array('sort' => 'ProjectFileState.name')),
	'ProjectFile.created' => array('content' => __('Created'), 'options' => array('sort' => 'ProjectFile.created')),
//	'ProjectFile.modified' => array('content' => __('Modified'), 'options' => array('sort' => 'ProjectFile.modified')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($projectFiles as $i => $projectFile)
{
	$actions = array(
		$this->Html->link(__('Notes'), array('action' => 'view', $projectFile['ProjectFile']['id']), array('class' => 'mini-view')),
		$this->Html->link(__('Download'), array('action' => 'download', $projectFile['ProjectFile']['id'])),
	);
	
	$actions[] = $this->Html->link(__('Edit'), array('action' => 'edit', $projectFile['ProjectFile']['id']));
	$actions[] = $this->Html->link(__('Delete'), array('action' => 'delete', $projectFile['ProjectFile']['id']), array('confirm' => 'Are you sure?'));
	$actions = implode(' ', $actions);
	
	$td[$i] = array(
		$this->Html->link($projectFile['ProjectFile']['filename'], array('action' => 'download', $projectFile['ProjectFile']['id'])),
		$projectFile['ProjectFile']['nicename'],
		$projectFile['ProjectFileState']['name'],
		$this->Wrap->niceTime($projectFile['ProjectFile']['created']),
//		$this->Wrap->niceTime($projectFile['ProjectFile']['modified']),
		array(
			$actions, 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('All %s %s', __('Project'), __('Files')),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));