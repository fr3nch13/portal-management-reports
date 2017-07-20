<?php 
// File: app/View/ProjectFiles/edit.ctp


?>
<div class="top">
	<h1><?php echo __('Edit %s %s', __('Project'), __('File')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create();?>
		    <fieldset>
		        <legend><?php echo __('Edit %s %s', __('Project'), __('File')); ?></legend>
		    	<?php
					echo $this->Form->input('id');
					echo $this->Form->input('nicename', array(
						'label' => __('Friendly Name'),
						'div' => array('class' => 'threeforths'),
					));
					echo $this->Form->input('project_file_state_id', array(
						'label' => __('The File State'),
						'div' => array('class' => 'forth'),
						'empty' => __('TBD'),
					));
					echo $this->Wrap->divClear();
					echo $this->Form->input('notes');
					
					echo $this->Tag->autocomplete();
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Update %s %s', __('Project'), __('File'))); ?>
	</div>
</div>