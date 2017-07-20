<?php 
// File: app/View/DailyReports/edit.ctp
?>
<div class="top">
	<h1><?php echo __('Edit %s', __('Daily Report')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('DailyReport');?>
		    <fieldset>
		    	<?php
					echo $this->Form->input('id');
					echo $this->Form->input('name', array(
						'placeholder' => __('Work Out - %s - %s', AuthComponent::user('name'), date('m-d-Y')),
						'div' => array('class' => 'half'),
					));
					echo $this->Form->input('report_date', array(
						'div' => array('class' => 'half'),
					));
				?>
		    </fieldset>
		    <fieldset>
		        <h3><?php echo __('Extra Notes'); ?></h3>
		    	<?php
					
					echo $this->Form->input('notes', array(
						'type' => 'textarea',
						'label' => __('Extra details to include with this %s', __('Daily Report')),
					));
					
					echo $this->Tag->autocomplete();
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s', __('Daily Report'))); ?>
	</div>
</div>