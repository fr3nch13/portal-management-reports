<?php

$inputs = [];
$inputs['report_date'] = [
	'div' => ['class' => 'half'],
	'type' => 'date',
	'label' => __('The Date for this %s.', __('Weekly Report')),
	'between' => $this->Html->tag('p', __('(Choose the date that this specific %s is/was DUE)', __('Weekly Report'))),
];
$inputs['legend_imports'] = [
	'type' => 'legend',
	'value' => __('Import Options'),
];
$inputs['import'] = [
	'div' => ['class' => 'half'],
	'type' => 'toggle',
	'checked' => true,
	'label' => __('Import %s from the <b>%s</b> in the selected range? (they\'ll be listed on the next step)', __('Report Items'), __('Daily Reports')),
	'after' => $this->Html->tag('p', __('It will import all %s, from the range, and the rest will be from the last %s', __('Completed'), __('Daily Report'))),
];
$inputs['import_weekly'] = [
	'div' => ['class' => 'half'],
	'type' => 'toggle',
	'checked' => false,
	'label' => __('Import %s from the last <b>%s</b>?', __('Report Items'), __('Weekly Report')),
	'after' => $this->Html->tag('p', __('It will import all but Completed items from the last cronological <b>%s</b> by the Report Date, <b>that is Finalized</b>.', __('Weekly Report'))),
];
$inputs['favorites'] = [
	'type' => 'toggle',
	'checked' => true,
	'label' => __('Import Favorite %s that match with this %s\'s Week.', __('Report Items'), __('Weekly Report')),
];
$inputs['legend_items'] = [
	'type' => 'legend',
	'value' => __('Report Items'),
];
$inputs['p_items'] = [
	'type' => 'p',
	'value' => __('List of %s for this %s. One %s per line.', __('Report Items'), __('Weekly Report'), __('Report Item')),
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
$inputs['legend_notes'] = [
	'type' => 'legend',
	'value' => __('Extra Notes'),
];
$inputs['notes'] = [
	'type' => 'textarea',
	'label' => __('Extra details to include with this %s', __('Weekly Report')),
];

echo $this->element('Utilities.page_form_basic', [
	'inputs' => $inputs,
	'action_title' => __('Create New'),
]);