<?php 
// File: app/View/WeeklyReportsReportItems/edit.ctp
?>
<div class="top">
	<h1><?php echo __('Edit %s', __('Report Item')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('WeeklyReportsReportItem');?>
		    <fieldset>
		    	<?php
					echo $this->Form->input('id');
					echo $this->Form->input('ReportItem.id');
					echo $this->Form->input('ReportItem.item', array(
						'label' => __('Item Content'),
					));
					echo $this->Form->input('ReportItem.charge_code_id', array(
						'options' => $charge_codes,
						'div' => array('class' => 'half'),
					));
					echo $this->Form->input('ReportItem.activity_id', array(
						'div' => array('class' => 'half'),
					));
					echo $this->Wrap->divClear();
					echo $this->Form->input('ReportItem.item_date', array(
						'type' => 'date',
						'div' => array('class' => 'third'),
						'cal_options' => array(
							'minDate' => $xref_item['WeeklyReport']['report_date_start'],
							'maxDate' => $xref_item['WeeklyReport']['report_date_end'],
						),
					));
					echo $this->Form->input('item_state', array(
						'options' => $item_states,
						'div' => array('class' => 'third'),
					));
					echo $this->Form->input('highlighted', array(
						'options' => array('0' => __('No'), '1' => __('Yes')),
						'div' => array('class' => 'third'),
					));
				?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s', __('Report Item'))); ?>
	</div>
</div>