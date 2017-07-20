<?php 
// File: app/View/ProjectStatuses/admin_add.ctp
?>
<div class="top">
	<h1><?php echo __('Add %s', __('%s %s', __('Project'), __('Status'))); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create();?>
		    <fieldset>
		        <legend><?php echo __('Add %s', __('%s %s', __('Project'), __('Status'))); ?></legend>
		    	<?php
					echo $this->Form->input('name', array(
					));
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s', __('%s %s', __('Project'), __('Status')))); ?>
	</div>
</div>