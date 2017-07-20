<?php 
// File: app/View/DailyReports/add.ctp
?>
<div class="top">
	<h1><?php echo __('Add %s to %s', __('Report Items'), $daily_report['DailyReport']['name']); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('DailyReportsReportItem');?>
		    <fieldset>
		        <h3><?php echo __('Report Items'); ?></h3>
		        <p><?php echo __('List of %s for this %s. One %s per line.', __('Report Items'), __('Daily Report'), __('Report Item')); ?></p>
		        <p>&nbsp;</p>
		    	<?php
					$class = 'third';
					if(count($item_states) == 1)
					{
						$class = '';
					}
					foreach($item_states as $i => $item_state)
					{
						echo $this->Form->input('report_items.'. $i, array(
							'type' => 'textarea',
							'label' => $item_state,
							'div' => array('class' => $class),
							'required' => false,
						));
					}
				?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s', __('Report Items'))); ?>
	</div>
</div>