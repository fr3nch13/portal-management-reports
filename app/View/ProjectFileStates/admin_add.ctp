<?php 
// File: app/View/ProjectFileStates/admin_add.ctp
?>
<div class="top">
	<h1><?php echo __('Add %s', __('Project File State')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('ProjectFileState');?>
		    <fieldset>
		        <legend><?php echo __('Add %s', __('Project File State')); ?></legend>
		    	<?php
					echo $this->Form->input('name');
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s', __('Project File State'))); ?>
	</div>
</div>