<?php 
// File: app/View/WeeklyReports/import.ctp
?>
<div class="top">
	<h1><?php echo __('Import %s', __('Weekly Report')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('WeeklyReport', array('type' => 'file'));?>
		    <fieldset>
		    	<?php
/*
					echo $this->Form->input('name', array(
						'placeholder' => __('Weekly Activity Report - %s - %s', AuthComponent::user('name'), date('m-d-Y')),
						'div' => array('class' => 'half'),
						'between' => $this->Html->tag('p', __('The Name of this %s.', __('Weekly Report'))),
					));
*/
					echo $this->Form->input('report_date', array(
						'div' => array('class' => 'half'),
						'type' => 'date',
						'label' => __('The Date for this %s.', __('Weekly Report')),
						'between' => $this->Html->tag('p', __('(Choose the date that this specific %s is/was DUE)', __('Weekly Report'))),
					));
					
/*
					echo $this->Html->tag('p', __('The Date Range for this %s.', __('Weekly Report')));
					
					echo $this->Form->input(false, array(
						'div' => array('class' => 'forth'),
						'type' => 'daterange',
						'start' => 'report_date_start',
						'end' => 'report_date_end',
						'start_options' => array(
							'label' => __('Start Date'),
						//'between' => $this->Html->tag('p', __('The name for this %s.', __('Weekly Report'))),
						),
						'end_options' => array(
							'label' => __('End Date'),
						),
					));
					echo $this->Wrap->divClear();
*/
					
					echo $this->Form->input('file', array(
						'div' => array('class' => 'half'),
						'type' => 'file',
						'label' => __('%s %s File', __('Weekly Report'), __('Excel')),
					));
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