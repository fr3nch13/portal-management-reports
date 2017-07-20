<?php 
// File: app/View/ChargeCodes/admin_add.ctp
?>
<div class="top">
	<h1><?php echo __('Add %s', __('Charge Code')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create();?>
		    <fieldset>
		        <legend><?php echo __('Add %s', __('Charge Code')); ?></legend>
		    	<?php
					echo $this->Form->input('name', array(
						'div' => array('class' => 'third'),
					));
					echo $this->Form->input('charge_code', array(
						'div' => array('class' => 'third'),
					));
					echo $this->Form->input('color_code_hex', array(
						'label' => __('Assigned Color'),
						'type' => 'color',
						'div' => array('class' => 'third'),
					));
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s', __('Charge Code'))); ?>
	</div>
</div>