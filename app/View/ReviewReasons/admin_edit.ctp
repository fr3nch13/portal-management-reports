<?php 
// File: app/View/ReviewReasons/admin_edit.ctp
?>
<div class="top">
	<h1><?php echo __('Edit %s', __('Review Reason')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('ReviewReason');?>
		    <fieldset>
		        <legend><?php echo __('Edit %s', __('Review Reason')); ?></legend>
		    	<?php
					echo $this->Form->input('id');
					echo $this->Form->input('name');
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s', __('Review Reason'))); ?>
	</div>
</div>