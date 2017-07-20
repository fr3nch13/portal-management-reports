<?php 
// File: app/View/WeeklyReportsReportItems/add.ctp
?>
<div class="top">
	<h1><?php echo __('Add %s to %s', __('Report Items'), $weekly_report['WeeklyReport']['name']); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('WeeklyReportsReportItem');?>
		    <fieldset>
		        <h3><?php echo __('Report Items'); ?></h3>
		        <p><?php echo __('List of %s for this %s. One %s per line.', __('Report Items'), __('Weekly Report'), __('Report Item')); ?></p>
		        <p>&nbsp;</p>
		    	<?php
					
					foreach($item_states as $i => $item_state)
					{
						echo $this->Form->input('report_items.'. $i, array(
							'type' => 'textarea',
							'label' => $item_state,
							'div' => array('class' => 'third'),
							'required' => false,
						));
					}
				?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s', __('Report Items'))); ?>
	</div>
</div>