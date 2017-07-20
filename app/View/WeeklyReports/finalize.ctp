<?php 

$finalized_link_text = __('Mark Finalized and Send Report');

$page_options = array(
	$this->Html->link(__('Cancel'), array('action'=> 'view', $weekly_report['WeeklyReport']['id'])),
	$this->Form->postLink($finalized_link_text, 
		array('action' => 'finalize', $weekly_report['WeeklyReport']['id'], true),
		array(),
		__('Are you sure you want to mark this %s as Finalized?', __('Weekly Report'))),
);

/*
// it can support both, as there is an html template for this
// however, josh requested the text only as it is easier to read on a BB
$page_content_html = $this->element('../Emails/html/weekly_finalized_email', array(
	'weekly_report' => $weekly_report,
	'report_items' => $report_items,
	'item_states' => $item_states,
	'item_charge_code' => $item_charge_codes,
	'item_activities' => $item_activities,
));
*/
$page_content_html = '';

$page_content_text = $this->element('../Emails/text/weekly_finalized_email', array(
	'weekly_report' => $weekly_report,
	'report_items' => $report_items,
	'item_states' => $item_states,
	'item_charge_code' => $item_charge_codes,
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
		'page_title' => __('%s : %s', __('Weekly Report'), $weekly_report['WeeklyReport']['name']),
		'page_subtitle' => __('To Finalize and Send this %s, please click "%s" to the right.', __('Weekly Report'), $finalized_link_text),
		'page_options' => $page_options,
		'page_description' => __('This is how the %s will be emailed when it\'s finalized.', __('Weekly Report')),
		'page_content' => $page_content_html. $page_content_text,
	));
}