<?php 
// File: app/View/ManagementReportsReportItem/add.ctp
?>
<div class="top">
	<h1><?php echo __('Add %s %s %s to a %s', __('Completed'), __('Staff'), __('Report Items'), __('Management Report')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('ManagementReportsReportItem');?>
		    <fieldset>
		        <p>&nbsp;</p>
		    	<?php
		    		echo $this->Form->input('id');

					echo $this->Form->input('user_id', array(
						'type' => 'hidden',
					));
					echo $this->Form->input('ReportItem.user_id', array(
						'type' => 'hidden',
					));
					echo $this->Form->input('ReportItem.item', array(
						'type' => 'textarea',
						'label' => __('%s %s', __('Staff'), __('Report Item')),
						'required' => false,
					));
				?>
		    </fieldset>
		    <fieldset>
		        <h3><?php echo __('%s %s %s %s', __('Completed'), __('Staff'), __('Report Items'), __('Options')); ?></h3>
		        <p><?php echo __('These %s will apply to all new %s listed above. ', __('Options'), __('Report Items')); ?></p>
		        <p>&nbsp;</p>
		    	<?php
					echo $this->Form->input('item_state', array(
						'type' => 'hidden',
						'value' => 1,
					));
					echo $this->Form->input('ReportItem.item_date', array(
						'type' => 'date',
						'div' => array('class' => 'third'),
						'cal_options' => array(
							'minDate' => $xref_item['ManagementReport']['report_date_start'],
							'maxDate' => $xref_item['ManagementReport']['report_date_end'],
						),
					));
					
					echo $this->Form->input('highlighted', array(
						'options' => array('0' => __('No'), '1' => __('Yes')),
						'div' => array('class' => 'third'),
					));
					echo $this->Wrap->divClear();
					echo $this->Form->input('item_state', array(
						'empty' => __('[None]'),
						'div' => array('class' => 'third'),
						'options' => $item_states,
					));
					echo $this->Form->input('ReportItem.charge_code_id', array(
						'options' => $charge_codes,
						'empty' => __('[None]'),
						'div' => array('class' => 'third'),
					));
					echo $this->Form->input('ReportItem.activity_id', array(
						'empty' => __('[None]'),
						'div' => array('class' => 'third'),
					));
				?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s', __('Report Items'))); ?>
	</div>
</div>