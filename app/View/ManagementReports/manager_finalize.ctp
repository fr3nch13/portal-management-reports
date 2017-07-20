<?php 

$page_options = array(
	$this->Html->link(__('Cancel'), array('action'=> 'view', $management_report['ManagementReport']['id'])),
	$this->Form->postLink(__('Mark Finalized and Send'), 
		array('action' => 'finalize', $management_report['ManagementReport']['id'], true),
		array(),
		__('Are you sure you want to mark this %s as Finalized?', __('Management Report'))),
);

// it can support both, as there is an html template for this
// however, josh requested the text only as it is easier to read on a BB
$page_content_html = '';
$page_content_html = $this->element('../Emails/html/management_finalized_email', array(
	'management_report' => $management_report,
/*
	'item_states' => $item_states,
	'item_charge_codes' => $item_charge_codes,
	'item_activities' => $item_activities,
	'staff_report_items' => $staff_report_items,
*/
	'item_sections' => $item_sections,
	'management_report_items' => $management_report_items,
));

$page_content_text = '';
/*
$page_content_text = $this->element('../Emails/text/management_finalized_email', array(
	'management_report' => $management_report,
	'item_states' => $item_states,
	'item_charge_codes' => $item_charge_codes,
	'item_activities' => $item_activities,
	'staff_report_items' => $staff_report_items,
	'item_sections' => $item_sections,
	'management_report_items' => $management_report_items,
));
*/

if(isset($email_this))
{
	echo $page_content_html;
}
else
{
	$page_content_text = $this->Html->tag('pre', $page_content_text);
	
	echo $this->element('Utilities.page_generic', array(
		'page_title' => __('%s %s', __('Finalize'), __('Management Report')),
		'page_options' => $page_options,
		'page_description' => $this->Html->tag('span', __('This is how the %s will be emailed when it\'s finalized.', __('Management Report')), array('class' => 'no-print')),
		'page_content' => $page_content_html. $page_content_text,
	));
}
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function () {
	$('div#body_content > div.top').addClass('no-print');

});
//]]>
</script>