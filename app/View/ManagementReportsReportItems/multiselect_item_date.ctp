<?php ?>
<div class="top">
	<h1><?php echo __('Assign all selected %s to a %s', __('Report Items'), __('Date')); ?></h1>
</div>
<div class="center">
	<div class="posts form">
	<?php echo $this->Form->create('ManagementReportsReportItem');?>
	    <fieldset>
	        <legend><?php echo __('Assign all selected %s to a %s', __('Report Items'), __('Date')); ?></legend>
	    	<?php
				echo $this->Form->input('ReportItem.item_date', array(
					'type' => 'date',
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
