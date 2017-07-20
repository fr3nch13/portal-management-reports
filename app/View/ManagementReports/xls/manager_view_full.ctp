<?php 

$properties = array(
	'title' => $management_report['ManagementReport']['title'],
	'subject' => $management_report['ManagementReport']['subtitle'],
	'creator' => $management_report['User']['name'],
	'modifier' => $management_report['User']['name'],
	'keywords' => $management_report['ManagementReport']['tags'],
	'created' => $management_report['ManagementReport']['created'],
	'modified' => $management_report['ManagementReport']['modified'],
);;

$sheets = array();

// organize the management_report_items by their sections
$section_items = array();
foreach($management_report_items as $management_report_item)
{
	$item_section = $management_report_item['ManagementReportItem']['item_section'];
	if(!isset($section_items[$item_section])) $section_items[$item_section] = array();
	
	$section_items[$item_section][] = $management_report_item['ManagementReportItem']['item'];
}

$matrix = array();

// status
$matrix[] = array(array('content' => $management_report['ManagementReport']['status_title'], 'style' => array(
	'font.bold' => true,
	'borders.bottom.style' => 'thin',
	'borders.bottom.color.rgb' => 'FFCCCCCC',
)));
$matrix[] = array($management_report['ManagementReport']['status_text']);

if(isset($section_items['status']))
{
	foreach($section_items['status'] as $section_item)
	{
		$matrix[] = array('   · '. $section_item);
	}
}
$matrix[] = array();
$matrix[] = array();

// planned
$matrix[] = array(array('content' => $management_report['ManagementReport']['planned_title'], 'style' => array(
	'font.bold' => true,
	'borders.bottom.style' => 'thin',
	'borders.bottom.color.rgb' => 'FFCCCCCC',
)));
$matrix[] = array($management_report['ManagementReport']['planned_text']);
if(isset($section_items['planned']))
{
	foreach($section_items['planned'] as $section_item)
	{
		$matrix[] = array('   · '. $section_item);
	}
}
$matrix[] = array();
$matrix[] = array();

// issues
$matrix[] = array(array('content' => $management_report['ManagementReport']['issues_title'], 'style' => array(
	'font.bold' => true,
	'borders.bottom.style' => 'thin',
	'borders.bottom.color.rgb' => 'FFCCCCCC',
)));
$matrix[] = array($management_report['ManagementReport']['issues_text']);
if(isset($section_items['issues']))
{
	foreach($section_items['issues'] as $section_item)
	{ 
		$matrix[] = array('   · '. $section_item);
	}
}
$matrix[] = array();
$matrix[] = array();

// impact
$matrix[] = array(array('content' => $management_report['ManagementReport']['impact_title'], 'style' => array(
	'font.bold' => true,
	'borders.bottom.style' => 'thin',
	'borders.bottom.color.rgb' => 'FFCCCCCC',
)));
$matrix[] = array($management_report['ManagementReport']['impact_text']);
if(isset($section_items['impact']))
{
	foreach($section_items['impact'] as $section_item)
	{
		$matrix[] = array('   · '. $section_item);
	}
}

/// Overview Sheet
$sheets[0] = array(
	'sheet_title' => __('Overview'),
	'title' => $management_report['ManagementReport']['title'],
	'subtitle' => $management_report['ManagementReport']['subtitle'],
	'matrix' => $matrix,
);

/// Staff highlighted sheet
$sheets[1] = array(
	'sheet_title' => __('Staff Highlights'),
	'title' => __('Staff Selected Highlights'),
	'subtitle' => __('Items the staff individually selected to be highlighted.'),
	'csv' => $this->requestAction(array('controller' => 'management_reports_report_items', 'action' => 'management_report_highlighted_finalized', $management_report['ManagementReport']['id'], 'ext' => 'csv'), array('return')),
);

/// Staff completed sheet
$sheets[2] = array(
	'sheet_title' => __('Staff Completed Items'),
	'title' => __('Staff Completed Items'),
	'subtitle' => __('Items the staff completed within this report\'s timeframe..'),
	'csv' => $this->requestAction(array('controller' => 'management_reports_report_items', 'action' => 'management_report_finalized', $management_report['ManagementReport']['id'], 1, 'ext' => 'csv'), array('return')),
);

/// by charge code
$sheet_count = count($sheets);
foreach($itemized_charge_codes as $itemized_charge_code)
{
	$sheets[$sheet_count] = array(
		'sheet_title' => __('%s', str_replace(array('*',':','/','\\','?','[',']'), ' ', $itemized_charge_code['name'])),
		'title' => __('By %s - %s', __('Charge Code'), $itemized_charge_code['name']),
		'csv' => $this->requestAction(array('controller' => 'management_reports_report_items', 'action' => 'management_report_charge_code_finalized', $management_report['ManagementReport']['id'], $itemized_charge_code['value'], 'ext' => 'csv'), array('return')),
	);
	$sheet_count++;
}

// by activity
foreach($itemized_activities as $itemized_activity)
{
	/// Staff completed sheet
	$sheets[$sheet_count] = array(
		'sheet_title' => __('%s', str_replace(array('*',':','/','\\','?','[',']'), ' ', $itemized_activity['name'])),
		'title' => __('By %s - %s', __('Activity'), $itemized_activity['name']),
		'csv' => $this->requestAction(array('controller' => 'management_reports_report_items', 'action' => 'management_report_activity_finalized', $management_report['ManagementReport']['id'], $itemized_activity['value'], 'ext' => 'csv'), array('return')),
	);
	$sheet_count++;
}

			
$this->response->type('xls');
$results = $this->PhpExcel->export($sheets, $properties);
echo $results;
$this->response->download($management_report['ManagementReport']['export_filename']. '.xls');