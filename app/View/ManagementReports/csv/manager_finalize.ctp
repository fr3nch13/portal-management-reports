<?php 
$this->Local->setSortableChargeCodes($item_charge_codes);
$this->Local->setSortableStates($item_states);
$this->Local->setSortableActivities($item_activities);

$data = array();
foreach($report_items as $i => $report_item)
{
	$data[$i] = array(
		'Highlighted' => ($report_item['ManagementReportsReportItem']['highlighted']?__('Yes'):''),
		'Item' => $report_item['ReportItem']['item'],
		'Status' => $this->Local->getSortableStates($report_item['ManagementReportsReportItem']['item_state']),
		'ChargeCode' => $this->Local->getSortableChargeCodes($report_item['ReportItem']['charge_code_id']),
		'Activity' => $this->Local->getSortableActivities($report_item['ReportItem']['activity_id']),
	);
}

$results = $this->Exporter->view($data, array(), $this->request->params['ext'], Inflector::camelize(Inflector::singularize($this->request->params['controller'])), false, false);
echo $results;