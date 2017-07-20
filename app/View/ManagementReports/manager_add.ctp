<?php 
// File: app/View/ManagementReports/add.ctp
?>
<div class="top">
	<h1><?php echo __('Create New %s', __('Management Report')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('ManagementReport');?>
		    <fieldset>
		    	<?php
					echo $this->Form->input('title', array(
						'between' => $this->Html->tag('p', __('The Title of this %s.', __('Management Report'))),
					));
					echo $this->Wrap->divClear();
					
					echo $this->Form->input('report_date', array(
						'div' => array('class' => 'half'),
						'type' => 'date',
						'between' => $this->Html->tag('p', __('The Date for this %s.', __('Management Report'))),
					));
					
					echo $this->Html->tag('p', __('The Date Range for this %s.', __('Management Report')));
					
					echo $this->Form->input(false, array(
						'div' => array('class' => 'forth'),
						'type' => 'daterange',
						'start' => 'report_date_start',
						'end' => 'report_date_end',
						'start_options' => array(
							'label' => __('Start Date'),
						),
						'end_options' => array(
							'label' => __('End Date'),
						),
					));
				?>
		    </fieldset>
		    <div class="half">
		    <fieldset>
		        <h3><?php echo __('%s %s', __('Staff'), __('Highlighted')); ?></h3>
		    	<?php
					echo $this->Form->input('staff_title', array(
						'label' => __('The %s for the %s %s section.', __('Title'), __('Staff'), __('Highlighted')),
					));
					
					echo $this->Form->input('staff_text', array(
						'type' => 'textarea',
						'label' => __('Extra details to include with the %s %s section.', __('Staff'), __('Highlighted')),
					));
		    	?>
		    </fieldset>
		    </div>
		    
		    <div class="half">
		    <fieldset>
		        <h3><?php echo __('%s %s', __('Staff'), __('Completed')); ?></h3>
		    	<?php
					echo $this->Form->input('completed_title', array(
						'label' => __('The %s for the %s %s section.', __('Title'), __('Staff'), __('Completed')),
						'required' => 'required',
					));
					
					echo $this->Form->input('completed_text', array(
						'type' => 'textarea',
						'label' => __('Extra details to include with the %s %s section.', __('Staff'), __('Completed')),
					));
		    	?>
		    </fieldset>
		    </div>
		    
		    <?php echo $this->Wrap->divClear(); ?>
		    
		    <div>
		    <h4><?php echo __('Select Weekly Reports'); ?></h4>
		        <p><?php echo __('Select which %s to import Highlighted %s from. Note, if you change the above range, you\'ll have to reselect the %s.', __('Weekly Reports'), __('Report Items'), __('Weekly Reports')); ?></p>
		        <div id="weekly_reports_holder">
		        
		        </div>
		    </div>
		    
		    <?php echo $this->Wrap->divClear(); ?>
		    
			<?php
			if(isset($item_sections['staff'])) unset($item_sections['staff']);
			if(isset($item_sections['completed'])) unset($item_sections['completed']);
			$i = 0;
			foreach($item_sections as $item_section_key => $item_section_name):
			?>
		    
		    <div class="half">
		    <fieldset>
		        <h3><?php echo $item_section_name; ?></h3>
		    	<?php
					
					echo $this->Form->input($item_section_key. '_title', array(
						'label' => __('The %s for the %s section.', __('Title'), $item_section_name),
					));
					
					echo $this->Form->input($item_section_key. '_text', array(
						'type' => 'textarea',
						'label' => __('Extra details to include with the %s section.', $item_section_name),
					));
					
					echo $this->Form->input('ManagementReportItem.'.$item_section_key. '_items', array(
						'type' => 'textarea',
						'label' => __('Items to include with the %s section.', $item_section_name),
						'between' => $this->Form->input($item_section_key. '_import', array(
							'type' => 'checkbox',
							'checked' => 'checked',
							'label' => __('Import %s %s from previous %s', $item_section_name, __('Items'), __('Management Report')),
						))
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
		    
		    <?php echo $this->Wrap->divClear(); ?>
		    
		    <fieldset>
		        <h3><?php echo __('Extra Notes'); ?></h3>
		    	<?php
					
					echo $this->Form->input('notes', array(
						'type' => 'textarea',
						'label' => __('Extra details to include with this %s', __('Management Report')),
					));
					
					echo $this->Tag->autocomplete();
		    	?>
		    </fieldset>
		    
		    <?php echo $this->Wrap->divClear(); ?>
		<?php echo $this->Form->end(__('Save %s', __('Management Report'))); ?>
	</div>
</div>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function ()
{
	<?php
	$baseUrl = $this->Html->url(array(
		"controller" => "weekly_reports",
		"action" => "report_range",
		"manager" => true,
		"admin" => false,
	));
	?>
	function updateWeeklyReportList()
	{
		var data = {
			"report_date_start" : $("#ManagementReportReportDateStart").val(),
			"report_date_end" : $("#ManagementReportReportDateEnd").val(),
		}
		var updateDiv = $("#weekly_reports_holder");
		
		var start = encodeURIComponent($("#ManagementReportReportDateStart").val());
		var end = encodeURIComponent($("#ManagementReportReportDateEnd").val());
		
		console.log(data);
		$.ajax({
			type: "GET",
			url: "<?=$baseUrl ?>/"+start+"/"+end,
			beforeSend: function( xhr ) {
				updateDiv.text( "'. __('Updating the list of %s...', __('Weekly Reports')). '" );
			},
			success: function(response){
				updateDiv.html(response);
            },
            error: function(res){
            	updateDiv.text( "'. __('Error occurred when Updating the list of %s...', __('Weekly Reports')). '" );
            }
        });
	}
	
	// add ability to watch the timepicker and update table when the range changes 
	$("#ManagementReportReportDateStart_false").datepicker("option", "onClose", function(dateText,inst) { updateWeeklyReportList(); } );
	$("#ManagementReportReportDateEnd_false").datepicker("option", "onClose", function(dateText,inst) { updateWeeklyReportList(); } );
	
	
	updateWeeklyReportList();
});
//]]>
</script>