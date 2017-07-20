<?php 
$page_content = array();

$this->Local->setSortableChargeCodes($item_charge_codes);
$this->Local->setSortableActivities($item_activities);

$sep = str_repeat('-', 30);

$page_content[] = __('Name: %s', $weekly_report['WeeklyReport']['name']);
$page_content[] = __('Report Date: %s', $this->Wrap->niceDay($weekly_report['WeeklyReport']['report_date']));
$page_content[] = __('Start Date: %s', $this->Wrap->niceDay($weekly_report['WeeklyReport']['report_date_start']));
$page_content[] = __('End Date: %s', $this->Wrap->niceDay($weekly_report['WeeklyReport']['report_date_end']));
$page_content[] = __('Tags: %s', $weekly_report['WeeklyReport']['tags']);
$page_content[] = __('User: %s', $weekly_report['User']['name']);
$page_content[] = '';
$page_content[] = __('Notes:');
$page_content[] = $weekly_report['WeeklyReport']['notes'];

// pull the highlighted items into their own section
$page_content[] = '';
$page_content[] = __('Highlighted');
$page_content[] = $sep;

foreach($report_items as $i => $report_item)
{
	if($report_item['WeeklyReportsReportItem']['highlighted'])
	{
		$item = $report_item['ReportItem']['item'];
		if(substr($item, 0 ,2) != '- ')
			$item = '- '. $item;
		
		$page_content[] = $item;
		unset($report_items[$i]);
	}
}
$page_content[] = '';

foreach($item_states as $item_state_id => $item_state_name)
{
	$page_content[] = '';
	$page_content[] = $item_state_name;
	$page_content[] = $sep;
	
	$charge_code_name = false;
	foreach($report_items as $report_item)
	{
		if($report_item['WeeklyReportsReportItem']['item_state'] != $item_state_id)
			continue;
		
		if($report_item['ReportItem']['charge_code_id'])
		{
			$charge_code_id = $report_item['ReportItem']['charge_code_id'];
			if($this->Local->getSortableChargeCodes($charge_code_id))
			{
				if($charge_code_name != $this->Local->getSortableChargeCodes($charge_code_id))
				{
					$charge_code_name = $this->Local->getSortableChargeCodes($charge_code_id);
					
					$page_content[] = '--- '. $charge_code_name;
				}
			}
		}
		
		$item = $report_item['ReportItem']['item'];
		if(substr($item, 0 ,2) != '- ')
			$item = '- '. $item;
		
		$page_content[] = $item;
	}
}

$page_content = implode("\n", $page_content);

echo $page_content;