<?php

$inputs = [];
$inputs['name'] = [
	'placeholder' => __('Work Out - %s - %s', AuthComponent::user('name'), date('m-d-Y')),
	'div' => ['class' => 'half'],
];
$inputs['report_date'] = [
	'div' => ['class' => 'half'],
	'type' => 'datetime',
	'value' => date('Y-m-d H:i:s'),
];
$inputs['legend_imports'] = [
	'type' => 'legend',
	'value' => __('Import Options'),
];
$inputs['import'] = [
	'div' => ['class' => 'half'],
	'type' => 'toggle',
	'checked' => true,
	'label' => __('Import %s not marked as Completed from the last report? (they\'ll be listed on the next step)', __('Report Items')),
	'after' => $this->Html->tag('p', __('These items come from the last %s by the %s (selected above), and that is marked as %s.', __('Daily Report'), __('Report Date'), __('Finalized'))),
];
$inputs['favorites'] = [
	'div' => ['class' => 'half'],
	'type' => 'toggle',
	'checked' => true,
	'label' => __('Import Favorite %s that match with this %s\'s Day/Week.', __('Report Items'), __('Daily Report')),
];
$inputs['legend_items'] = [
	'type' => 'legend',
	'value' => __('Report Items'),
];
$inputs['p_items'] = [
	'type' => 'p',
	'value' => __('List of %s for this %s. One %s per line.', __('Report Items'), __('Daily Report'), __('Report Item')),
];
foreach($item_states as $i => $item_state)
{
	$inputs['report_items.'. $i] = [
		'type' => 'textarea',
		'label' => $item_state,
		'div' => array('class' => 'third'),
		'required' => false,
	];
}

echo $this->element('Utilities.page_form_basic', [
	'inputs' => $inputs,
	'action_title' => __('Create New'),
]);