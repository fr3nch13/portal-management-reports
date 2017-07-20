<?php 
$page_content = array();

$this->Local->setSortableChargeCodes($item_charge_codes);
$this->Local->setSortableActivities($item_activities);

$sep = str_repeat('-', 30);

$page_content[] = __('Name: %s', $daily_report['DailyReport']['name']);
$page_content[] = __('Date: %s', $this->Wrap->niceTime($daily_report['DailyReport']['report_date']));
$page_content[] = __('Tags: %s', $daily_report['DailyReport']['tags']);
$page_content[] = __('User: %s', $daily_report['User']['name']);
$page_content[] = '';
$page_content[] = __('Notes:');
$page_content[] = $daily_report['DailyReport']['notes'];

foreach($item_states as $item_state_id => $item_state_name)
{
	$page_content[] = '';
	$page_content[] = $item_state_name;
	$page_content[] = $sep;
	
	$charge_code_name = false;
	foreach($report_items as $report_item)
	{
		if($report_item['DailyReportsReportItem']['item_state'] != $item_state_id)
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