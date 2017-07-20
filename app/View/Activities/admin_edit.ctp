<?php 
// File: app/View/Activities/admin_edit.ctp
?>
<div class="top">
	<h1><?php echo __('Edit %s', __('Activity')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('Activity');?>
		    <fieldset>
		        <legend><?php echo __('Edit %s', __('Activity')); ?></legend>
		    	<?php
					echo $this->Form->input('id');
					echo $this->Form->input('name', array(
						'div' => array('class' => 'twothird'),
					));
					echo $this->Form->input('color_code_hex', array(
						'label' => __('Assigned Color'),
						'type' => 'color',
						'div' => array('class' => 'third'),
					));
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s', __('Activity'))); ?>
	</div>
</div>