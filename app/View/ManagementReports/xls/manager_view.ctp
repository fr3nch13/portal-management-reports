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

if($management_report_items)
{
	foreach($management_report_items as $management_report_item)
	{
		$item_section = $management_report_item['ManagementReportItem']['item_section'];
		if(!isset($section_items[$item_section])) $section_items[$item_section] = array();
		
		$section_items[$item_section][] = $management_report_item['ManagementReportItem']['item'];
	}
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

/// My stuff
$matrix[] = array(array('content' => __('%s work and deliverables completed within the above date range.', AuthComponent::user('name')), 'style' => array(
	'font.bold' => true,
	'borders.bottom.style' => 'thin',
	'borders.bottom.color.rgb' => 'FFCCCCCC',
)));
 
$matrix[] = array('csv' => $this->requestAction(array('controller' => 'management_reports_report_items', 'action' => 'management_report_mine', $management_report['ManagementReport']['id'], 'ext' => 'csv'), array('return')));


// Highlighted stuff
$matrix[] = array(array('content' => __('Staff Highlights during the above date range.'), 'style' => array(
	'font.bold' => true,
	'borders.bottom.style' => 'thin',
	'borders.bottom.color.rgb' => 'FFCCCCCC',
)));
$matrix[] = array('csv' => $this->requestAction(array('controller' => 'management_reports_report_items', 'action' => 'management_report_highlighted_finalized', $management_report['ManagementReport']['id'], 'ext' => 'csv'), array('return')));

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

			
$this->response->type('xls');
$results = $this->PhpExcel->export($sheets, $properties);
echo $results;
$this->response->download($management_report['ManagementReport']['export_filename']. '.xls');