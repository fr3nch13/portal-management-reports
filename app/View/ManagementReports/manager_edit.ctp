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
		    
		    
			<?php
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

<?php 
// javascript to buffer

		$jsScript = '
	// initially get the list of weekly reports to import information from
	
	function updateWeeklyReportList()
	{
		var data = {
			"report_date_start" : $("#ManagementReportReportDateStart").val(),
			"report_date_end" : $("#ManagementReportReportDateEnd").val(),
		}
		var updateDiv = $("#weekly_reports_holder");
		
		console.log(data);
		$.ajax({
			type: "GET",
			url: "'. $this->Html->url(array(
				"controller" => "weekly_reports",
				"action" => "report_range",
				"manager" => true,
				"admin" => false,
			)). '/"+$("#ManagementReportReportDateStart").val()+"/"+$("#ManagementReportReportDateEnd").val(),
			beforeSend: function( xhr ) {
				updateDiv.text( "'. __('Updating the list of %s...', __('Weekly Reports')). '" );
			},
			success: function(response){
				updateDiv.html(response);
				
/*
                if(response.success == "1"){
                    if(action == "save"){
                        $(".entry-form").fadeOut("fast",function(){
                            $(".table-list").append(""+response.fname+""+response.lname+""+response.email+""+response.phone+"<a href="#" id=""+response.row_id+"" class="del">Delete</a>");  
                            $(".table-list tr:last").effect("highlight", {
                                color: "#4BADF5"
                            }, 1000);
                        });
                    }else if(action == "delete"){
                        var row_id = response.item_id;
                        $("a[id=\'"+row_id+"\']").closest("tr").effect("highlight", {
                            color: "#4BADF5"
                        }, 1000);
                        $("a[id=\'"+row_id+"\']").closest("tr").fadeOut();
                    }
                }
*/
            },
            error: function(res){
            	updateDiv.text( "'. __('Error occurred when Updating the list of %s...', __('Weekly Reports')). '" );
/*
                alert("Unexpected error! Try again.");
*/
            }
        });
	}
	
	// add ability to watch the timepicker and update table when the range changes 
	$("#ManagementReportReportDateStartFalse").datepicker("option", "onClose", function(dateText,inst) { updateWeeklyReportList(); } );
	$("#ManagementReportReportDateEndFalse").datepicker("option", "onClose", function(dateText,inst) { updateWeeklyReportList(); } );
	
	
	updateWeeklyReportList();
';
//$this->Js->buffer($jsScript); 