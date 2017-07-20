<?php 
// File: app/View/ManagementReports/manager_defaults.ctp
?>
<div class="top">
	<h1><?php echo __('%s Defaults', __('Management Report')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('ManagementReport');?>
		    <fieldset>
		    	<?php
		    		echo $this->Form->input('ManagementReportDefault.id');
					echo $this->Form->input('ManagementReportDefault.title', array(
						'label' => __('Report Title'),
						'between' => $this->Html->tag('p', __('The %s Main Title for the %s.', __('Default'), __('Management Report'))),
					));
				?>
		    </fieldset>
		    
		    <div class="half">
		    <fieldset>
		        <h3><?php echo __('Status'); ?></h3>
		    	<?php
					echo $this->Form->input('ManagementReportDefault.status_title', array(
						'label' => __('The %s %s for the %s section.', __('Default'), __('Title'), __('Status')),
						'required' => 'required',
					));
					
					echo $this->Form->input('ManagementReportDefault.status_text', array(
						'type' => 'textarea',
						'label' => __('Extra details to include with the %s section.', __('Status')),
					));
		    	?>
		    </fieldset>
		    </div>
		    
		    <div class="half">
		    <fieldset>
		        <h3><?php echo __('Planned'); ?></h3>
		    	<?php
					echo $this->Form->input('ManagementReportDefault.planned_title', array(
						'label' => __('The %s %s for the %s section.', __('Default'), __('Title'), __('Planned')),
						'required' => 'required',
					));
					
					echo $this->Form->input('ManagementReportDefault.planned_text', array(
						'type' => 'textarea',
						'label' => __('Extra details to include with the %s section.', __('Planned')),
					));
		    	?>
		    </fieldset>
		    </div>
		    
		    <?php $this->Wrap->divClear(); ?>
		    
		    <div class="half">
		    <fieldset>
		        <h3><?php echo __('Completed'); ?></h3>
		    	<?php
					echo $this->Form->input('ManagementReportDefault.completed_title', array(
						'label' => __('The %s %s for the %s section.', __('Default'), __('Title'), __('Completed')),
						'required' => 'required',
					));
					
					echo $this->Form->input('ManagementReportDefault.completed_text', array(
						'type' => 'textarea',
						'label' => __('Extra details to include with the %s section.', __('Completed')),
					));
		    	?>
		    </fieldset>
		    </div>
		    
		    <div class="half">
		    <fieldset>
		        <h3><?php echo __('Staff'); ?></h3>
		    	<?php
					echo $this->Form->input('ManagementReportDefault.staff_title', array(
						'label' => __('The %s %s for the %s section.', __('Default'), __('Title'), __('Staff')),
						'required' => 'required',
					));
					
					echo $this->Form->input('ManagementReportDefault.staff_text', array(
						'type' => 'textarea',
						'label' => __('Extra details to include with the %s section.', __('Staff')),
					));
		    	?>
		    </fieldset>
		    </div>
		    
		    <?php $this->Wrap->divClear(); ?>
		    
		    <div class="half">
		    <fieldset>
		        <h3><?php echo __('Issues'); ?></h3>
		    	<?php
					echo $this->Form->input('ManagementReportDefault.issues_title', array(
						'label' => __('The %s %s for the %s section.', __('Default'), __('Title'), __('Issues')),
						'required' => 'required',
					));
					
					echo $this->Form->input('ManagementReportDefault.issues_text', array(
						'type' => 'textarea',
						'label' => __('Extra details to include with the %s section.', __('Issues')),
					));
		    	?>
		    </fieldset>
		    </div>
		    
		    <div class="half">
		    <fieldset>
		        <h3><?php echo __('Impact'); ?></h3>
		    	<?php
					echo $this->Form->input('ManagementReportDefault.impact_title', array(
						'label' => __('The %s %s for the %s section.', __('Default'), __('Title'), __('Impact')),
						'required' => 'required',
					));
					
					echo $this->Form->input('ManagementReportDefault.impact_text', array(
						'type' => 'textarea',
						'label' => __('Extra details to include with the %s section.', __('Impact')),
					));
		    	?>
		    </fieldset>
		    </div>
		    
		    <?php $this->Wrap->divClear(); ?>
		<?php echo $this->Form->end(__('Save %s', __('%s Defaults', __('Management Report')))); ?>
	</div>
</div>
<?php
/*
		'title' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'subtitle' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		
		'statustitle' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'statustext' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'plannedtitle' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'plannedtext' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		
		'completedtitle' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'completedtext' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'issuestitle' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'issuestext' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		
		'impacttitle' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'impacttext' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		
		'stafftitle' => array('type' => 'string', 'null' => false, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		
*/
