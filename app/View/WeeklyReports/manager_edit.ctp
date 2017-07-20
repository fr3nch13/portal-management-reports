<?php 
// File: app/View/WeeklyReports/edit.ctp
?>
<div class="top">
	<h1><?php echo __('Edit %s', __('Weekly Report')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('WeeklyReport');?>
		    <fieldset>
		    	<?php
					echo $this->Form->input('id');
/*
					echo $this->Form->input('name', array(
						'div' => array('class' => 'half'),
						'between' => $this->Html->tag('p', __('The Name of this %s.', __('Weekly Report'))),
					));
*/
					echo $this->Form->input('report_date', array(
						'div' => array('class' => 'half'),
						'type' => 'date',
						'between' => $this->Html->tag('p', __('The Date for this %s.', __('Weekly Report'))),
					));
/*
					echo $this->Wrap->divClear();
					
					echo $this->Html->tag('p', __('The Date Range for this %s.', __('Weekly Report')));
					
					echo $this->Form->input(false, array(
						'div' => array('class' => 'half'),
						'type' => 'daterange',
						'start' => 'report_date_start',
						'end' => 'report_date_end',
						'start_options' => array(
							'label' => __('Start Date'),
						),
						'end_options' => array(
							'label' => __('End Date'),
						),
						'after' => $this->Html->tag('p', __('Note: changing these dates will NOT reimport items from %s in this range.', __('Daily Reports'))),
					));
*/
				?>
		    </fieldset>
		    <fieldset>
		        <h3><?php echo __('Extra Notes'); ?></h3>
		    	<?php
					
					echo $this->Form->input('notes', array(
						'type' => 'textarea',
						'label' => __('Extra details to include with this %s', __('Weekly Report')),
					));
					
					echo $this->Tag->autocomplete();
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s', __('Weekly Report'))); ?>
	</div>
</div>