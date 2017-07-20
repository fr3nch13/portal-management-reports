<?php 
// File: app/View/ReviewReasons/index.ctp

$page_options = array(
	$this->Html->link(__('Add %s', __('Review Reason')), array('action' => 'add')),
);

// content
$th = array(
	'ReviewReason.name' => array('content' => __('Name'), 'options' => array('sort' => 'ReviewReason.name')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($review_reasons as $i => $review_reason)
{
	$td[$i] = array(
		$review_reason['ReviewReason']['name'],
		array(
			$this->Html->link(__('Edit'), array('action' => 'edit', $review_reason['ReviewReason']['id'])).
			$this->Html->link(__('Delete'), array('action' => 'delete', $review_reason['ReviewReason']['id']),array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Review Reasons'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));