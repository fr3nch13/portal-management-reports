<?php 
// File: app/View/Projects/view.ctp

$page_options = array();

$details = array(
	array('name' => __('Status'), 'value' => $project['ProjectStatus']['name']),
	array('name' => __('Request Date'), 'value' => $this->Wrap->niceTime($project['Project']['request_date'])),
	array('name' => __('Target Date'), 'value' => $this->Wrap->niceTime($project['Project']['target_date'])),
	array('name' => __('Created'), 'value' => $this->Wrap->niceTime($project['Project']['created'])),
	array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($project['Project']['modified'])),
);

$stats = array();
$tabs = array();

$stats[] = array(
	'id' => 'projectUpdates',
	'name' => __('%s %s', __('Project'), __('Updates')), 
	'ajax_count_url' => array('controller' => 'project_updates', 'action' => 'project', $project['Project']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);

$tabs[] = array(
	'key' => 'projectUpdates',
	'title' => __('%s %s', __('Project'), __('Updates')), 
	'url' => array('controller' => 'project_updates', 'action' => 'project', $project['Project']['id']),
);

$stats[] = array(
	'id' => 'ProjectFiles',
	'name' => __('Files'), 
	'ajax_count_url' => array('controller' => 'project_files', 'action' => 'project', $project['Project']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);

$tabs[] = array(
	'key' => 'ProjectFiles',
	'title' => __('Files'), 
	'url' => array('controller' => 'project_files', 'action' => 'project', $project['Project']['id']),
);

$tabs[] = array(
	'key' => 'notes',
	'title' => __('Details'),
	'content' => $this->Wrap->descView($project['Project']['details']),
);

$stats[] = array(
	'id' => 'tagsProject',
	'name' => __('Tags'), 
	'ajax_count_url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'project', $project['Project']['id']),
	'tab' => array('tabs', (count($tabs) + 1)), // the tab to display
);	
$tabs[] = array(
	'key' => 'tags',
	'title' => __('Tags'),
	'url' => array('plugin' => 'tags', 'controller' => 'tags', 'action' => 'tagged', 'project', $project['Project']['id']),
);


echo $this->element('Utilities.page_view', array(
	'page_title' => __('%s : %s', __('Project'), $project['Project']['name']),
	'page_options' => $page_options,
	'details_title' => __('Details'),
	'details' => $details,
	'stats' => $stats,
	'tabs_id' => 'tabs',
	'tabs' => $tabs,
));