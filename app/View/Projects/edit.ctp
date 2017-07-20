<?php 
// File: app/View/Project/admin_add.ctp
?>
<div class="top">
	<h1><?php echo __('Edit %s', __('Project')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create();?>
		    <fieldset>
		        <legend><?php echo __('Edit %s', __('Project')); ?></legend>
		    	<?php
					echo $this->Form->input('id');
					echo $this->Form->input('name', array(
						'div' => array('class' => 'forth'),
					));
					echo $this->Form->input('project_status_id', array(
						'div' => array('class' => 'forth'),
						'empty' => __('TBD'),
					));
					echo $this->Form->input('request_date', array(
						'div' => array('class' => 'forth'),
					));
					echo $this->Form->input('target_date', array(
						'div' => array('class' => 'forth'),
						'label' => __('Target Completion Date'),
					));
					echo $this->Wrap->divClear();
					echo $this->Form->input('details', array(
					));
					
					echo $this->Tag->autocomplete();
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s', __('Project'))); ?>
	</div>
</div>