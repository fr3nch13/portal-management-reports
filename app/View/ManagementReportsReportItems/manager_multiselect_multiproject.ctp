<?php 
// File: app/View/ManagementReportsReportItems/manager_multiselect_multicharge_code.ctp

$this->Local->setSortableChargeCodes($charge_codes);

// content
$th = array(
	'ReportItem.item' => array('content' => __('Item'), 'options' => array('sort' => 'ReportItem.item')),
	'ReportItem.charge_code_id' => array('content' => __('Current %s', __('Charge Code')), 'options' => array('sort' => 'ReportItem.charge_code_id')),
	'charge_code_id' => array('content' => __('Select %s', __('Charge Code')), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($managementreports_reportitems as $i => $managementreports_reportitem)
{
	$actions = $this->Form->input('ReportItem.'.$i.'.id', array('type' => 'hidden', 'value' => $managementreports_reportitem['ReportItem']['id']));
	$actions .= $this->Form->input('ReportItem.'.$i.'.charge_code_id', array(
	        					'div' => false,
	        					'label' => false,
								'empty' => __('[ None ]'),
	        					'options' => $charge_codes,
	        					'selected' => $managementreports_reportitem['ReportItem']['charge_code_id'],
	        				));
	
	$td[$i] = array(
		$managementreports_reportitem['ReportItem']['item'],
		$this->Local->getSortableChargeCodes($managementreports_reportitem['ReportItem']['charge_code_id']),
		array(
			$actions,
			array('class' => 'actions'),
		),
	);
}

$before_table = false;
$after_table = false;

if($td)
{
	$before_table = $this->Form->create('ManagementReportsReportItem', array('url' => array('action' => 'multiselect_multicharge_code')));
	$after_table = $this->Form->end(__('Save'));
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Select an %s for each %s', __('Charge Code'), __('Report Item')),
	'use_search' => false,
	'th' => $th,
	'td' => $td,
	'before_table' => $before_table,
	'after_table' => $after_table,
));