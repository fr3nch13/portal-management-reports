<?php

$th = [];
$th[9999] = ['content' => ' X '];

foreach($users as $user)
{
	$user_id = $user['User']['id'];
	$th[$user_id] = ['content' => __('%s (%s)', $user['User']['name'], $user['ChargeCode']['name'])];
}

$matrix = [];
foreach($reportItems as $reportItem)
{
	$user_id = $reportItem['User']['id'];
	$chargeCode_id = $reportItem['ChargeCode']['id'];
	$mkey = $user_id.'-'.$chargeCode_id;
	
	if(!isset($matrix[$mkey]))
		$matrix[$mkey] = 0;
	$matrix[$mkey]++;
}

$td = [];
$i = 0;
foreach($chargeCodes as $chargeCode)
{
	$chargeCode_id = $chargeCode['ChargeCode']['id'];
	$td[$i] = [];
	
	$td[$i][0] = $chargeCode['ChargeCode']['name'];
	
	foreach($users as $user)
	{
		$user_id = $user['User']['id'];
		$mkey = $user_id.'-'.$chargeCode_id;
		
		$count = 0;
		if(isset($matrix[$mkey]))
			$count = $matrix[$mkey];
		$td[$i][$user_id] = __(' %s ', $count);
	}
	
	$i++;
}

echo $this->element('Utilities.page_index', [
	'page_title' => __('%s - Counts', __('Charge Code')),
	'th' => $th,
	'td' => $td,
	'use_pagination' => false,
	'use_search' => false,
]);