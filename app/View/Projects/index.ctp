<?php 
// File: app/View/Project/admin_index.ctp

$page_options = array(
	$this->Html->link(__('Add %s', __('Project')), array('action' => 'add')),
);

// content
$th = array(
	'Project.name' => array('content' => __('Name'), 'options' => array('sort' => 'Project.name')),
	'ProjectStatus.name' => array('content' => __('Status'), 'options' => array('sort' => 'ProjectStatus.name')),
	'Project.request_date' => array('content' => __('Request Date'), 'options' => array('sort' => 'Project.request_date')),
	'Project.target_date' => array('content' => __('Target Completion Date'), 'options' => array('sort' => 'Project.target_date')),
	'UserAddedProject.name' => array('content' => __('Added By'), 'options' => array('sort' => 'UserAddedProject.name')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($projects as $i => $project)
{
	$actions = array(
		$this->Html->link(__('View'), array('action' => 'view', $project['Project']['id'])),
		$this->Html->link(__('Edit'), array('action' => 'edit', $project['Project']['id'])),
	);
	
	if($this->Common->roleCheck(array('admin')))
	{
		$actions[] = $this->Html->link(__('Delete'), array('action' => 'delete', $project['Project']['id']), array('confirm' => __('Are you sure?')));
	}
	
	$td[$i] = array(
		$this->Html->link($project['Project']['name'], array('action' => 'view', $project['Project']['id'])),
		$project['ProjectStatus']['name'],
		$this->Wrap->niceDay($project['Project']['request_date']),
		$this->Wrap->niceDay($project['Project']['target_date']),
		$project['UserAddedProject']['name'],
		array(
			implode("\n", $actions), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Projects'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));