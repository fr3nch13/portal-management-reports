<?php 
$this->Local->setSortableChargeCodes($item_charge_codes);
$this->Local->setSortableStates($item_states);
$this->Local->setSortableActivities($item_activities);

$data = array();
foreach($managementreports_reportitems as $i => $managementreports_reportitem)
{
	$resource = false;
	if(isset($managementreports_reportitem['User']['ChargeCode']['name']))
		$resource = $managementreports_reportitem['User']['ChargeCode']['name'];
	
	$data[$i] = array(
		'Item' => $managementreports_reportitem['ReportItem']['item'],
		'Date' => date('m-d-Y', strtotime($managementreports_reportitem['ReportItem']['item_date'])),
//		'ExcelDate' => ceil(25569 + strtotime($managementreports_reportitem['ReportItem']['item_date']) / 86400),
//		'Highlighted' => ($managementreports_reportitem['ManagementReportsReportItem']['highlighted']?__('Yes'):''),
//		'State' => $this->Local->getSortableStates($managementreports_reportitem['ManagementReportsReportItem']['item_state']),
		'Resource' => $resource,
		'Charge Code' => $this->Local->getSortableChargeCodes($managementreports_reportitem['ReportItem']['charge_code_id']),
		'Activity' => $this->Local->getSortableActivities($managementreports_reportitem['ReportItem']['activity_id']),
//		'User' => $managementreports_reportitem['User']['name'],
	);
}

$results = $this->Exporter->view($data, array(), $this->request->params['ext'], Inflector::camelize(Inflector::singularize($this->request->params['controller'])), false, false);
echo $results;