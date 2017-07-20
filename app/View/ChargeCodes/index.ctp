<?php 
// File: app/View/ChargeCodes/index.ctp

$page_options = array();

if(AuthComponent::user('role') == 'admin')
{
	$page_options[] = $this->Html->link(__('Add %s', __('Charge Code')), array('action' => 'add', 'admin' => true));
}

// content
$th = array(
	'ChargeCode.name' => array('content' => __('Name'), 'options' => array('sort' => 'ChargeCode.name')),
	'ChargeCode.charge_code' => array('content' => __('Charge Code'), 'options' => array('sort' => 'ChargeCode.charge_code')),
	'ChargeCode.color_code_hex' => array('content' => __('Color'), 'options' => array('sort' => 'ChargeCode.color_code_hex')),
);

$td = array();
foreach ($charge_codes as $i => $charge_code)
{
	$td[$i] = array(
		$charge_code['ChargeCode']['name'],
		$charge_code['ChargeCode']['charge_code'],
		array(
			$charge_code['ChargeCode']['color_code_hex'],
			array('style' => 'background-color: '. $charge_code['ChargeCode']['color_code_rgb'] ),
		),
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Charge Code Charges'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
));