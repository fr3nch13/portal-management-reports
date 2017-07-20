<?php 
// File: app/View/Project/admin_add.ctp
?>
<div class="top">
	<h1><?php echo __('Add %s', __('Project')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('Project', array('type' => 'file'));?>
		    <fieldset>
		        <legend><?php echo __('Add %s', __('Project')); ?></legend>
		    	<?php
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

					$max_upload = (int)(ini_get('upload_max_filesize'));
					$max_post = (int)(ini_get('post_max_size'));
					$memory_limit = (int)(ini_get('memory_limit'));
					$upload_mb = min($max_upload, $max_post, $memory_limit);
					
					echo $this->Form->input('ProjectFile.file', array(
						'type' => 'file',
						'label' => __('Related file. (optional)'),
						'between' => __('(Max file size is %sM).', $upload_mb),
					));
					
					echo $this->Tag->autocomplete();
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s', __('Project'))); ?>
	</div>
</div>