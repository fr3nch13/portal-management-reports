<?php 
// File: app/View/ManagementReportItems/manager_edit.ctp
?>
<div class="top">
	<h1><?php echo __('Edit %s %s', __('Management'), __('Report Item')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('ManagementReportItem');?>
		    <fieldset>
		    	<?php
					echo $this->Form->input('id');
					echo $this->Form->input('item', array(
						'label' => __('Item Content'),
					));
/*
					echo $this->Form->input('charge_code_id', array(
						'div' => array('class' => 'half'),
					));
					echo $this->Form->input('activity_id', array(
						'div' => array('class' => 'half'),
					));
					echo $this->Wrap->divClear();
*/
					echo $this->Form->input('item_date', array(
						'type' => 'date',
						'div' => array('class' => 'half'),
						'cal_options' => array(
							'minDate' => $management_report_item['ManagementReport']['report_date_start'],
							'maxDate' => $management_report_item['ManagementReport']['report_date_end'],
						),
					));
					echo $this->Form->input('item_section', array(
						'options' => $item_sections,
						'div' => array('class' => 'half'),
					));
/*
					echo $this->Form->input('highlighted', array(
						'options' => array('0' => __('No'), '1' => __('Yes')),
						'div' => array('class' => 'third'),
					));
*/
				?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s %s', __('Management'), __('Report Item'))); ?>
	</div>
</div>