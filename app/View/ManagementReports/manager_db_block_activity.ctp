<?php

$stats = [
	'total' => ['name' => __('Total with an %s Assigned', __('Activity')), 'value' => count($reportItems), 'color' => 'FFFFFF'],
];

foreach($activities as $activity)
{
	$id = $activity['Activity']['id'];
	$stats['Activity.'.$id] = [
		'name' => $activity['Activity']['name'],
		'value' => 0,
		'color' => str_replace('#', '', $activity['Activity']['color_code_hex']),
	];
}

foreach($reportItems as $reportItem)
{
	if($reportItem['Activity']['id'])
	{
		$reportItem_type_id = $reportItem['Activity']['id'];
		if(!isset($stats['Activity.'.$reportItem_type_id]))
		{
			$stats['Activity.'.$reportItem_type_id] = [
				'name' => $reportItem['Activity']['name'],
				'value' => 0,
				'color' => str_replace('#', '', $reportItem['Activity']['color_code_hex']),
			];
		}
		$stats['Activity.'.$reportItem_type_id]['value']++;
	}	
}

$stats = Hash::sort($stats, '{s}.value', 'desc');

$pie_data = [[__('Activity'), __('num %s', __('Items')) ]];
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

echo $this->element('Utilities.object_dashboard_block', [
	'title' => __('%s %s by %s', $scope, __('Report Items'), __('Activity') ),
	'description' => __('Excluding items that have a 0 count.'),
	'content' => $content,
]);