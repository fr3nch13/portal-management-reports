<?php 
// File: app/View/Users/admin_index.ctp

$page_options = array(
	$this->Html->link(__('Login History'), array('controller' => 'login_histories')),
);

// content
$th = array(
	'User.name' => array('content' => __('Name'), 'options' => array('sort' => 'User.name')),
	'User.email' => array('content' => __('Email'), 'options' => array('sort' => 'User.email')),
	'User.adaccount' => array('content' => __('AD Account'), 'options' => array('sort' => 'User.adaccount')),
	'User.active' => array('content' => __('Active'), 'options' => array('sort' => 'User.active')),
	'User.role' => array('content' => __('Role'), 'options' => array('sort' => 'User.role')),
	'User.charge_code_id' => array('content' => __('Primary Project'), 'options' => array('sort' => 'ChargeCode.name', 'editable' => array('type' => 'select', 'options' => $chargeCodes) )),
	'User.manager' => array('content' => __('Manager'), 'options' => array('sort' => 'User.manager')),
	'User.lastlogin' => array('content' => __('Last Login'), 'options' => array('sort' => 'User.lastlogin')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),

);

$td = array();
foreach ($users as $i => $user)
{
	$edit_id = array(
		'User' => $user['User']['id'],
	);
	
	$td[$i] = array(
		$this->Html->link($user['User']['name'], array('controller' => 'users', 'action' => 'view', $user['User']['id'])),
		$this->Html->link($user['User']['email'], 'mailto:'. $user['User']['email']),
		$user['User']['adaccount'],
		$this->Wrap->yesNo($user['User']['active']),
		$this->Wrap->userRole($user['User']['role']),
		array(
			(isset($user['ChargeCode']['name'])?$user['ChargeCode']['name']:'&nbsp;'),
			array('class' => 'nowrap', 'value' => (isset($user['ChargeCode']['id'])?$user['ChargeCode']['id']:0)),
		),
		array(
			$this->Form->postLink($this->Wrap->yesNo($user['User']['manager']), array('action' => 'toggle', 'manager', $user['User']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
		$this->Wrap->niceTime($user['User']['lastlogin']),
		array(
			$this->Html->link(__('View'), array('action' => 'view', $user['User']['id'])). 
			$this->Html->link(__('Edit'), array('action' => 'edit', $user['User']['id'])),
			array('class' => 'actions'),
		),
		'edit_id' => $edit_id,
	);
}

$use_gridedit = false;
if($this->Wrap->roleCheck(array('admin')))
	$use_gridedit = true;

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Manage Users'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	'use_gridedit' => $use_gridedit,
));