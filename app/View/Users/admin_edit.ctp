<?php

$inputs = [];
$inputs['id'] = [];
$inputs['paginate_items'] = [
	'div' => ['class' => 'third'],
	'label' => __('Items per table.'),
	'description' => __('How many items should show up in a table by default.'),
	'options' => [
		'10' => '10',
		'25' => '25',
		'50' => '50',
		'100' => '100',
		'150' => '150',
		'200' => '200',
	],
];
$inputs['charge_code_id'] = [
	'div' => ['class' => 'third'],
	'label' => __('Primary Project'),
	'description' => __('This user\'s primary project/charge code.'),
];
$inputs['manager'] = [
	'div' => ['class' => 'third'],
	'type' => 'toggle',
	'description' => __('Is this user considered a manager for things like management reports?'),
];

echo $this->element('Utilities.page_form_basic', [
	'page_title' => __('Edit settings for user: %s', $this->request->data['User']['name']),
	'inputs' => $inputs,
]);