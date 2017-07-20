<?php 
// File: app/View/ReviewReasons/index.ctp

$page_options = array();

if(AuthComponent::user('role') == 'admin')
{
	$page_options[] = $this->Html->link(__('Add %s', __('Review Reason')), array('action' => 'add', 'admin' => true));
}

// content
$th = array(
	'ReviewReason.name' => array('content' => __('Name'), 'options' => array('sort' => 'ReviewReason.name')),
);

$td = array();
foreach ($review_reasons as $i => $review_reason)
{
	$td[$i] = array(
		$review_reason['ReviewReason']['name'],
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Review Reasons'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));