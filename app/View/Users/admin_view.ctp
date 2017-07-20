<?php 
// File: app/View/Users/admin_view.ctp

$page_options = array(
	$this->Form->postLink(__('Toggle Manager State'),array('action' => 'toggle', 'manager', $user['User']['id']),array('confirm' => 'Are you sure?')),
	$this->Html->link(__('Edit'), array('action' => 'edit', $user['User']['id'])),
);

$details_left = array(
	array('name' => __('Email'), 'value' => $this->Html->link($user['User']['email'], 'mailto:'. $user['User']['email'])),
	array('name' => __('AD Account'), 'value' => $user['User']['adaccount']),
	array('name' => __('Active'), 'value' => $this->Wrap->yesNo($user['User']['active'])),
	array('name' => __('Role'), 'value' => $this->Wrap->userRole($user['User']['role'])),
	array('name' => __('Manager'), 'value' => $this->Wrap->yesNo($user['User']['manager'])),
);
$details_right = array(
	array('name' => __('Last Login'), 'value' => $this->Wrap->niceTime($user['User']['lastlogin'])),
	array('name' => __('Created'), 'value' => $this->Wrap->niceTime($user['User']['created'])),
	array('name' => __('Modified'), 'value' => $this->Wrap->niceTime($user['User']['modified'])),
);

$stats = array(
);

$tabs = array(
	array(
		'key' => 'logins',
		'title' => __('Login History'),
		'url' => array('controller' => 'login_histories', 'action' => 'user', $user['User']['id']),
	),
);

echo $this->element('Utilities.page_compare', array(
	'page_title' => __('%s: %s', __('User'), $user['User']['name']),
	'page_options' => $page_options,
	'details_left_title' => ' ',
	'details_left' => $details_left,
	'details_right_title' => ' ',
	'details_right' => $details_right,
	'stats' => $stats,
	'tabs' => $tabs,
));