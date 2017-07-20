<?php 

$page_options = array(
	$this->Html->link(__('Cancel'), array('action'=> 'view', $daily_report['DailyReport']['id'])),
	$this->Form->postLink(__('Mark Finalized and Send'), 
		array('action' => 'finalize', $daily_report['DailyReport']['id'], true),
		array(),
		__('Are you sure you want to mark this %s as Finalized?', __('Daily Report'))),
);

/*
// it can support both, as there is an html template for this
// however, josh requested the text only as it is easier to read on a BB
$page_content_html = $this->element('../Emails/html/daily_finalized_email', array(
	'daily_report' => $daily_report,
	'report_items' => $report_items,
	'item_states' => $item_states,
	'item_charge_codes' => $item_charge_codes,
	'item_activities' => $item_activities,
));
*/
$page_content_html = '';

$page_content_text = $this->element('../Emails/text/daily_finalized_email', array(
	'daily_report' => $daily_report,
	'report_items' => $report_items,
	'item_states' => $item_states,
	'item_charge_codes' => $item_charge_codes,
	'item_activities' => $item_activities,
));

if(isset($email_this))
{
	echo $page_content_html;
}
else
{
	$page_content_text = $this->Html->tag('pre', $page_content_text);
	
	echo $this->element('Utilities.page_generic', array(
		'page_title' => __('%s : %s', __('Daily Report'), $daily_report['DailyReport']['name']),
		'page_options' => $page_options,
		'page_description' => __('This is how the %s will be emailed when it\'s finalized.', __('Daily Report')),
		'page_content' => $page_content_html. $page_content_text,
	));
}