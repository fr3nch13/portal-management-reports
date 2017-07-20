<?php 
// File: app/View/ProjectUpdates/project.ctp

$page_options = array(
	$this->Html->link(__('Add %s %s', __('Project'), __('Update')), array('action' => 'add', $project['Project']['id'])),
);

// content
$th = array(
	'ProjectUpdate.summary' => array('content' => __('Summary'), 'options' => array('sort' => 'ProjectUpdate.summary')),
	'UserAddedProjectUpdate.name' => array('content' => __('Added By'), 'options' => array('sort' => 'UserAddedProjectUpdate.name')),
	'ProjectFile.nicename' => array('content' => __('Friendly File Name'), 'options' => array('sort' => 'ProjectFile.nicename')),
	'ProjectUpdate.created' => array('content' => __('Created'), 'options' => array('sort' => 'ProjectUpdate.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($projectUpdates as $i => $projectUpdate)
{
	$actions = array(
		$this->Html->link(__('Details'), array('action' => 'view', $projectUpdate['ProjectUpdate']['id']), array('class' => 'mini-view')),
		$this->Html->link(__('Edit'), array('action' => 'edit', $projectUpdate['ProjectUpdate']['id'])),
	);
	
	if($this->Common->roleCheck(array('admin')))
	{
		$actions[] = $this->Html->link(__('Delete'), array('action' => 'delete', $projectUpdate['ProjectUpdate']['id']), array('confirm' => __('Are you sure?')));
	}
	
	$td[$i] = array(
		$this->Html->link($projectUpdate['ProjectUpdate']['summary'], array('action' => 'view', $projectUpdate['ProjectUpdate']['id']), array('class' => 'mini-view')),
		$projectUpdate['UserAddedProjectUpdate']['name'],
		$this->Html->link($projectUpdate['ProjectFile']['nicename'], array('controller' => 'project_files', 'action' => 'download', $projectUpdate['ProjectFile']['id'])),
		$this->Wrap->niceTime($projectUpdate['ProjectUpdate']['created']),
		array(
			implode("\n", $actions), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('%s %s', __('Project'), __('Updates')),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));