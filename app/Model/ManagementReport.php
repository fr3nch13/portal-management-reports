<?php
App::uses('AppModel', 'Model');

class ManagementReport extends AppModel 
{
	public $validate = array(
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
	);
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
	);
	
	public $hasAndBelongsToMany = array(
		'ReportItem' => array(
			'className' => 'ReportItem',
			'joinTable' => 'management_reports_report_items',
			'foreignKey' => 'management_report_id',
			'associationForeignKey' => 'report_item_id',
			'unique' => 'keepExisting',
			'with' => 'ManagementReportsReportItem',
		),
	);
	
	public $hasMany = array(
		'ManagementReportItem' => array(
			'className' => 'ManagementReportItem',
			'foreignKey' => 'management_report_id',
			'dependent' => true,
		),
	);
	
	public $actsAs = array(
		'Tags.Taggable', 
		'Utilities.Email',
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'ManagementReport.name',
	);
	
	public $userId = false;
	
	public function afterFind($results = array(), $primary = false)
	{
		foreach($results as $i => $result)
		{
			if(isset($results[$i][$this->alias]))
			{
				if(!isset($results[$i][$this->alias]['subtitle']))
				{
					$results[$i][$this->alias]['subtitle'] = $this->subTitle($result[$this->alias]);
				}
				if(!isset($results[$i][$this->alias]['export_filename']))
				{
					$results[$i][$this->alias]['export_filename'] = $this->exportFileName($result[$this->alias]);
				}
			}
		}
		
		return parent::afterFind($results, $primary);
	}
	
	public function exportFileName($data)
	{
		$date_nice = $date_start = $date_end = false;
		
		$date_nice = date('F j, Y');
		if(isset($data['report_date']))
		{
			$date_nice = date('F j, Y', strtotime($data['report_date']));
		}
		
		$date_start = date('m-d-Y', strtotime('-2 Weeks'));
		if(isset($data['report_date_start']))
		{
			$date_start = date('m-d-Y', strtotime($data['report_date_start']));
		}
		
		$date_end = date('m-d-Y');
		if(isset($data['report_date_end']))
		{
			$date_end = date('m-d-Y', strtotime($data['report_date_end']));
		}
		
		$filename = __('Management Report - %s (%s - %s)', $date_nice, $date_start, $date_end);
		
		return $filename;
	}
	
	public function subTitle($data)
	{
		$date_nice = $date_start = $date_end = false;
		
		$date_nice = date('F j, Y');
		if(isset($data['report_date']))
		{
			$date_nice = date('F j, Y', strtotime($data['report_date']));
		}
		
		$date_start = date('m/d/y', strtotime('-2 Weeks'));
		if(isset($data['report_date_start']))
		{
			$date_start = date('m/d/y', strtotime($data['report_date_start']));
		}
		
		$date_end = date('m/d/y');
		if(isset($data['report_date_end']))
		{
			$date_end = date('m/d/y', strtotime($data['report_date_end']));
		}
		
		$subTitle = __('Management Report: %s (inclusive dates %s - %s)', $date_nice, $date_start, $date_end);
		
		return $subTitle;
	}
	
	public function addReport($data = null)
	{
		$this->modelError = false;
		$user_id = false;
		
		if(isset($data[$this->alias]['user_id']))
		{	
			$user_id = $data[$this->alias]['user_id'];
		}
		
		if(!$user_id)
		{
			$this->modelError = __('Unknown User');
			return false;
		}
		
		$report_date = false;
		if(isset($data[$this->alias]['report_date']))
			$report_date_end = $data[$this->alias]['report_date'];
		
		$report_date_start = false;
		if(isset($data[$this->alias]['report_date_start']))
			$report_date_start = $data[$this->alias]['report_date_start'];
		
		$report_date_end = false;
		if(isset($data[$this->alias]['report_date_end']))
			$report_date_end = $data[$this->alias]['report_date_end'];
		
		$import = false;
		if(isset($data[$this->alias]['import']))
			$import = $data[$this->alias]['import'];
		
		///// find the weekly reports to import
		$weekly_report_ids = array();
		if(isset($data['multiple']))
		{
			foreach($data['multiple'] as $weekly_report_id => $selected)
			{
				//$this->addReportItems($this->ManagementReport->id, $user_id, $report_items, $item_state, $import, $report_date_start, $report_date_end);
				if($selected)
					$weekly_report_ids[$weekly_report_id] = $weekly_report_id;
			}
		}
		
		///// find the different sections marked for import
		$import_item_sections = array();
		foreach($data[$this->alias] as $field => $value)
		{
			if(!preg_match('/import$/i', $field)) continue;
			if(!$value) continue;
			$parts = explode('_', $field);
			$this_item_section = $parts[0];
			$import_item_sections[$this_item_section] = $this_item_section;
		}
		
		///// save the new management report
		$this->data = $data;
		if(!$this->save($this->data))
		{
			return false;
		}
		
		// add the items from the form to this report 
		$this->ManagementReportItem->addToReport($this->id, $data);
		
		// import the selected section items from the last management report
		$this->ManagementReportItem->importFromLastReport($this->id, $user_id, $import_item_sections, $data[$this->alias]);
		
		// import the highlighted/completed items from the selected weekly reports
		$this->ManagementReportsReportItem->importFromSelectedWeeklys($this->id, $user_id, $weekly_report_ids, $data[$this->alias]);

		
		return true;
	}
	
	public function delete($id = null, $cascade = true)
	{
		if(!$id)
		{
			$id = $this->id;
		}
		if(!$id)
		{
			return false;
		}
		
		// find all report items associated with this report
		// the habtm relationship won't delete them.
		$report_item_ids = $this->ManagementReportsReportItem->find('list', array(
			'fields' => array('ManagementReportsReportItem.id', 'ManagementReportsReportItem.report_item_id'),
			'conditions' => array('ManagementReportsReportItem.management_report_id' => $id)
		));
		
		// delete the xref items first as the ReportItem->deleteAll checks for associations
		$this->ManagementReportsReportItem->deleteAll(
			array('ManagementReportsReportItem.management_report_id' => $id)
		);
		
		// delete the management report items
		$this->ManagementReportItem->deleteAll(
			array('ManagementReportItem.management_report_id' => $id)
		);
		
		// delete the report items
		$this->ReportItem->deleteAll(
			array('ReportItem.id' => $report_item_ids)
		);
		
		// delete this management report
		return parent::delete($id, $cascade);
	}
	
	public function checkItems($id = false)
	{
		if(!$id) 
		{
			$this->modelError = __('Unknown %s ID.', __('Management Report'));
			return false;
		}
		
		$this->id = $id;
		if(!$this->exists())
		{
			$this->modelError = __('Unknown %s ID.', __('Management Report'));
			return false;
		}
		
		// go through each report item, and make sure every one has a ChargeCode, and activity assigned to it
		$report_items = $this->ManagementReportsReportItem->find('all', array(
			'recursive' => 0,
			'contain' => array('ReportItem'),
			'conditions' => array(
				'ManagementReportsReportItem.management_report_id' => $id,
			),
			'order' => array(
				'ManagementReportsReportItem.item_state' => 'asc',
				'ManagementReportsReportItem.item_order' => 'asc',
			),
		));
		
		$finalize = true;
		$highlighted = 0;
		foreach($report_items as $i => $report_item)
		{
			if(!$report_item['ReportItem']['charge_code_id'])
				$finalize = false;
			if(!$report_item['ReportItem']['activity_id'])
				$finalize = false;
			if($report_item['ManagementReportsReportItem']['highlighted']) 
				$highlighted++;
		}
		
		if(!$finalize)
		{
			$this->modelError = __('All %s %s must have a %s and a %s assigned to them.', __('Staff'), __('Report Items'), __('Charge Code'), __('Activity'));
			return false;
		}
		
		if(!$highlighted)
		{
			$this->modelError = __('No %s %s are %s', __('Staff'), __('Report Items'), __('Highlighted'));
			return false;
		}
		
		return true;
	}
	
	public function finalize($id = false)
	{	
		// some specific validation
		if(!$this->checkItems($id))
		{
			return false;
		}
		
		if(!$this->save(array(
			'finalized' => true,
			'finalized_date' => date('Y-m-d H:i:s'),
		)))
		{
			return false;
		}
		
		$managerEmails = $this->User->managerEmails();
		foreach($managerEmails as $managerEmail)
		{
			$emails[$managerEmail] = $managerEmail;
		}
	 	
	 	// rebuild it to use the EmailBehavior from the Utilities Plugin
	 	$this->Email_reset();
		
		// set the variables so we can use view templates
		if(!$viewVars = $this->finalizedVars($id))
		{
			return false;
		}
		
		$user_email = '';
		if(isset($viewVars['management_report']['User']['email']))
		{
			$user_email = $viewVars['management_report']['User']['email'];
			$emails[$user_email] = $user_email;
			$this->Email_set('from', $user_email);
		}
		
		$this->Email_set('to', $emails);
		$this->Email_set('replyTo', $user_email);
		$subject = __('%s - %s', __('Management Report'), date('m-d-Y', strtotime($viewVars['management_report']['ManagementReport']['report_date'])));
		$this->Email_set('subject', $subject);
		$this->Email_set('viewVars', $viewVars);
		
		// it can support both, as there is an html template for this
		// however, josh requested the text only as it is easier to read on a BB
//		$this->Email_set('emailFormat', 'both');
		$this->Email_set('emailFormat', 'html');
		$this->Email_set('template', 'management_finalized_email');
		
		$csv_attachments = array();
		
		foreach($viewVars['itemized_charge_codes'] as $itemized_charge_code)
		{
			$itemized_charge_code_csv = $this->requestAction(array('controller' => 'management_reports_report_items', 'action' => 'management_report_charge_code_finalized', $this->id, $itemized_charge_code['value'], 'ext' => 'csv'), array('return'));
			if($itemized_charge_code_csv)
			{
				$csv_filename = __('staff_by_charge_code-%s-%s.csv', Inflector::slug($itemized_charge_code['name']), date('m-d-Y', strtotime($viewVars['management_report']['ManagementReport']['report_date'])));
				$csv_attachments[] = array($csv_filename => array('data' => $itemized_charge_code_csv, 'mimetype' => 'text/plain'));
			}
		}
		
		foreach($viewVars['itemized_activities'] as $itemized_activity)
		{
			$itemized_activity_csv = $this->requestAction(array('controller' => 'management_reports_report_items', 'action' => 'management_report_activity_finalized', $this->id, $itemized_activity['value'], 'ext' => 'csv'), array('return'));
			if($itemized_activity_csv)
			{
				$csv_filename = __('staff_by_activity-%s-%s.csv', Inflector::slug($itemized_activity['name']), date('m-d-Y', strtotime($viewVars['management_report']['ManagementReport']['report_date'])));
				$csv_attachments[] = array($csv_filename => array('data' => $itemized_activity_csv, 'mimetype' => 'text/plain'));
			}
		}
		
		$staff_highlighted_csv = $this->requestAction(array('controller' => 'management_reports_report_items', 'action' => 'management_report_highlighted_finalized', $this->id, 'ext' => 'csv'), array('return'));
		if($staff_highlighted_csv)
		{
			$csv_filename = 'staff_highlighted-'. date('m-d-Y', strtotime($viewVars['management_report']['ManagementReport']['report_date'])). '.csv';
			$csv_attachments[] = array($csv_filename => array('data' => $staff_highlighted_csv, 'mimetype' => 'text/plain'));
		}
		
		$staff_completed_csv = $this->requestAction(array('controller' => 'management_reports_report_items', 'action' => 'management_report_finalized', $this->id, 'ext' => 'csv'), array('return'));
		if($staff_completed_csv)
		{
			$csv_filename = 'staff_completed-'. date('m-d-Y', strtotime($viewVars['management_report']['ManagementReport']['report_date'])). '.csv';
			$csv_attachments[] = array($csv_filename => array('data' => $staff_completed_csv, 'mimetype' => 'text/plain'));
		}
		
		if($csv_attachments)
		{
			$this->Email_set('attachments', $csv_attachments);
		}
		
		return $this->Email_executeFull();
	}
	
	public function finalizedVars($id = false)
	{
		$this->modelError = false;
		
		$management_report = $this->find('first', array(
			'recursive' => 1,
			'conditions' => array('ManagementReport.id' => $id),
			'contain' => array('Tag', 'User'),
		));
		
		$charge_code_ids = $this->ManagementReportsReportItem->find('list', array(
			'recursive' => 0,
			'contain' => array('ReportItem'),
			'conditions' => array(
				'ManagementReportsReportItem.management_report_id' => $id,
			),
			'fields' => array('ReportItem.charge_code_id', 'ReportItem.charge_code_id'),
		));
		
		$itemized_charge_codes = $this->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable(false, false, $charge_code_ids);
		
		
		$activity_ids = $this->ManagementReportsReportItem->find('list', array(
			'recursive' => 0,
			'contain' => array('ReportItem'),
			'conditions' => array(
				'ManagementReportsReportItem.management_report_id' => $id,
			),
			'fields' => array('ReportItem.activity_id', 'ReportItem.activity_id'),
		));
		
		$itemized_activities = $this->ManagementReportsReportItem->ReportItem->Activity->listForSortable(false, false, $activity_ids);
		
		$item_states = $this->ManagementReportsReportItem->getItemStates();
		$item_charge_codes = $this->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable();
		$item_activities = $this->ManagementReportsReportItem->ReportItem->Activity->listForSortable();
		
		$management_report_items = $this->ManagementReportItem->find('all', array(
			'conditions' => array(
				'ManagementReportItem.management_report_id' => $id,
			),
			'order' => array(
				'ManagementReportItem.item_section' => 'asc',
				'ManagementReportItem.item_order' => 'asc',
			),
		));
		
		$item_sections = $this->ManagementReportItem->getItemSections(false, true);
		
		return array(
			'management_report' => $management_report,
			'item_states' => $item_states,
			'item_charge_code' => $item_charge_codes,
			'item_activities' => $item_activities,
			'itemized_charge_codes' => $itemized_charge_codes,
			'itemized_activities' => $itemized_activities,
//			'staff_report_items' => $staff_report_items,
			'item_sections' => $item_sections,
			'management_report_items' => $management_report_items,
		);
	}
}
