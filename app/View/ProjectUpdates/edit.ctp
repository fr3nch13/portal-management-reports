<?php 
// File: app/View/ProjectUpdates/edit.ctp
?>
<div class="top">
	<h1><?php echo __('Edit %s %s', __('Project'), __('Update')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create();?>
		    <fieldset>
		        <legend><?php echo __('Edit %s', __('Project'), __('Update')); ?></legend>
		    	<?php
					echo $this->Form->input('id');
					echo $this->Form->input('summary', array(
						'div' => array('class' => 'threeforths'),
					));
					echo $this->Form->input('project_status_id', array(
						'div' => array('class' => 'forth'),
						'label' => __('Update %s %s', __('Project'), __('Status')),
						'empty' => __('[ Don\'t update %s ]', __('Status')),
					));
					echo $this->Wrap->divClear();
					echo $this->Form->input('details');
					
					echo $this->Tag->autocomplete();
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s %s', __('Project'), __('Update'))); ?>
	</div>
</div>