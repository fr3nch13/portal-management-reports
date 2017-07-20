<?php

$stats = [
	'total' => ['name' => __('Total with an %s Assigned', __('Charge Code')), 'value' => count($reportItems), 'color' => 'FFFFFF'],
];

foreach($chargeCodes as $chargeCode)
{
	$id = $chargeCode['ChargeCode']['id'];
	$stats['ChargeCode.'.$id] = [
		'name' => $chargeCode['ChargeCode']['name'],
		'value' => 0,
		'color' => str_replace('#', '', $chargeCode['ChargeCode']['color_code_hex']),
	];
}

foreach($reportItems as $reportItem)
{
	if($reportItem['ChargeCode']['id'])
	{
		$reportItem_type_id = $reportItem['ChargeCode']['id'];
		if(!isset($stats['ChargeCode.'.$reportItem_type_id]))
		{
			$stats['ChargeCode.'.$reportItem_type_id] = [
				'name' => $reportItem['ChargeCode']['name'],
				'value' => 0,
				'color' => str_replace('#', '', $reportItem['ChargeCode']['color_code_hex']),
			];
		}
		$stats['ChargeCode.'.$reportItem_type_id]['value']++;
	}	
}

$stats = Hash::sort($stats, '{s}.value', 'desc');

$pie_data = [[__('Charge Code'), __('num %s', __('Items')) ]];
$pie_options = ['slices' => []];
foreach($stats as $i => $stat)
{
	if($i == 'total')
	{
		$stats[$i]['pie_exclude'] = true;
		$stats[$i]['color'] = 'FFFFFF';
		continue;
	}
	if(!$stat['value'])
	{
		unset($stats[$i]);
		continue;
	}
	$pie_data[] = [$stat['name'], $stat['value'], $i];
	$pie_options['slices'][] = ['color' => '#'. $stat['color']];
}

$content = $this->element('Utilities.object_dashboard_chart_pie', [
	'title' => '',
	'data' => $pie_data,
	'options' => $pie_options,
]);

$content .= $this->element('Utilities.object_dashboard_stats', [
	'title' => '',
	'details' => $stats,
]);

$scope = __('All');
if(isset($user['User']['id']))
{
	$scope = __('%s (%s)', $user['User']['name'], $user['ChargeCode']['name']);
}

echo $this->element('Utilities.object_dashboard_block', [
	'title' => __('%s %s by %s', $scope, __('Report Items'), __('Charge Code') ),
	'description' => __('Excluding items that have a 0 count.'),
	'content' => $content,
]);