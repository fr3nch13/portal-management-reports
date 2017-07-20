<?php 
// File: app/View/ProjectFiles/add.ctp

?>
<div class="top">
	<h1><?php echo __('Add %s %s', __('Project'), __('File')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('ProjectFile', array('type' => 'file'));?>
		    <fieldset>
		        <legend><?php echo __('Add %s %s', __('Project'), __('File')); ?></legend>
		    	<?php
					echo $this->Form->input('project_id', array('type' => 'hidden'));
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

					$max_upload = (int)(ini_get('upload_max_filesize'));
					$max_post = (int)(ini_get('post_max_size'));
					$memory_limit = (int)(ini_get('memory_limit'));
					$upload_mb = min($max_upload, $max_post, $memory_limit);
					
					echo $this->Form->input('ProjectFile.file', array(
						'type' => 'file',
						'label' => __('Related file. (optional)'),
						'between' => __('(Max file size is %sM).', $upload_mb),
					));
					
					echo $this->Form->input('notes');
					
					echo $this->Tag->autocomplete();
		    	?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s %s', __('Project'), __('File'))); ?>
	</div>
</div>