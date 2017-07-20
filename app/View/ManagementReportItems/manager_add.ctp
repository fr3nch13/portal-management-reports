<?php 
// File: app/View/ManagementReportItems/add.ctp
?>
<div class="top">
	<h1><?php echo __('Add %s %s to a %s', __('Management'), __('Report Items'), __('Management Report')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('ManagementReportItem');?>
		    
		<?php
		$i = 0;
		foreach($item_sections as $item_section_key => $item_section_name):
		?>
		    
		    <div class="half">
		    <fieldset>
		        <h3><?php echo $item_section_name; ?></h3>
		    	<?php
					echo $this->Form->input($item_section_key. '_items', array(
						'type' => 'textarea',
						'label' => __('Items to include with the %s section.', $item_section_name),
					));
		    	?>
		    </fieldset>
		    </div>
		<?php 
			$i++;
			if ($i % 2 == 0)
			{
				echo $this->Wrap->divClear();
			}
		endforeach; 
		?>
		
		<?php echo $this->Form->end(__('Save %s %s', __('Management'), __('Report Items'))); ?>
	</div>
</div>