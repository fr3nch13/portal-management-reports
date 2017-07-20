<?php ?>
<!-- File: app/View/ManagementReportsReportItems/manager_multiselect_charge_code.ctp -->
<div class="top">
	<h1><?php echo __('Assign all selected %s to %s %s', __('Report Items'), 'an', __('Activity')); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('ManagementReportsReportItem', array('url' => array('action' => 'multiselect_charge_code')));?>
	    <fieldset>
	        <legend><?php echo __('Assign all selected %s to %s %s', __('Report Items'), 'an', __('Activity')); ?></legend>
	    	<?php
				echo $this->Form->input('ReportItem.activity_id', array(
					'empty' => __('[ None ]'),
				));
	    	?>
	    </fieldset>
	<?php echo $this->Form->end(__('Save')); ?>
	</div>
<?php
if(isset($selected_items) and $selected_items)
{
	$details = array();
	foreach($selected_items as $selected_item)
	{
		$details[] = array('name' => __('Item: '), 'value' => $selected_item);
	}
	echo $this->element('Utilities.details', array(
			'title' => __('Selected %s. Count: %s', __('Report Items'), count($selected_items)),
			'details' => $details,
		));
}
?>
</div>
