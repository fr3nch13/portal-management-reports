<?php 
// File: app/View/LoginHistories/admin_index.ctp

$page_options = array(
);

// content
$th = array(
	'LoginHistory.ipaddress' => array('content' => __('Ip Address'), 'options' => array('sort' => 'LoginHistory.ipaddress')),
	'LoginHistory.user_agent' => array('content' => __('User Agent'), 'options' => array('sort' => 'LoginHistory.user_agent')),
	'LoginHistory.success' => array('content' => __('Successful'), 'options' => array('sort' => 'LoginHistory.success')),
	'LoginHistory.timestamp' => array('content' => __('Time'), 'options' => array('sort' => 'LoginHistory.timestamp')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($loginHistories as $i => $login_history)
{
	$td[$i] = array(
		$login_history['LoginHistory']['ipaddress'],
		$login_history['LoginHistory']['user_agent'],
		$this->Wrap->yesNo($login_history['LoginHistory']['success']),
		$this->Wrap->niceTime($login_history['LoginHistory']['timestamp']),
		array(
			$this->Form->postLink(__('Delete'),array('action' => 'delete', $login_history['LoginHistory']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Login History'),
	'search_placeholder' => __('login history'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));
?>