<?php
App::uses('AppModel', 'Model');

class WeeklyReport extends AppModel 
{
	public $validate = array(
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);
	
	public $order = array("WeeklyReport.report_date" => "desc");
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
	);
	
	public $hasAndBelongsToMany = array(
		'ReportItem' => array(
			'className' => 'ReportItem',
			'joinTable' => 'weekly_reports_report_items',
			'foreignKey' => 'weekly_report_id',
			'associationForeignKey' => 'report_item_id',
			'unique' => 'keepExisting',
			'with' => 'WeeklyReportsReportItem',
		),
	);
	
	public $hasMany = array(
		'WeeklyReportsReportItem' => array(
			'className' => 'WeeklyReportsReportItem',
			'foreignKey' => 'weekly_report_id',
			'dependent' => true,
		),
	);
	
	public $actsAs = array(
		'Tags.Taggable', 
		'Utilities.Email',
		'PhpExcel.PhpExcel', 
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'WeeklyReport.name',
	);
	
	public $manageUploads = true;
	
	public $userId = false;
	
	public function beforeSave($options = array())
	{
		if(isset($this->data[$this->alias]['report_date']))
		{
			// always make sure the weekly report is on Friday
			$this->data[$this->alias]['report_date'] = date('Y-m-d 00:00:00', strtotime('Friday', strtotime($this->data[$this->alias]['report_date'])));
		}
		
		return parent::beforeSave($options);
	}
	
	public function importReport($data = null)
	{
		$this->modelError = false;
		
		if(isset($data[$this->alias]['user_id']))
		{
			$this->userId = $data[$this->alias]['user_id'];
		}
		else
		{
			$this->userId = $data[$this->alias]['user_id'] = AuthComponent::user('id');
		}
		
		if(!$this->WeeklyReportsReportItem->importWeeklyReport($data))
		{
			if(!$this->modelError) $this->modelError = $this->WeeklyReport->modelError;
			return false;
		}
		return true;
	}
	
	public function addReport($data = null)
	{
		$this->modelError = false;
		
		if(isset($data[$this->alias]['user_id']))
		{
			$this->userId = $data[$this->alias]['user_id'];
		}
		else
		{
			$this->userId = $data[$this->alias]['user_id'] = AuthComponent::user('id');
		}
		
		if(!$this->WeeklyReportsReportItem->addWeeklyReport($data))
		{
			if(!$this->modelError) $this->modelError = $this->WeeklyReportsReportItem->modelError;
			return false;
		}
		return true;
	}
	
	public function importItemsFromExcel($id = null)
	{
		if(!$this->WeeklyReportsReportItem->importItemsFromExcel($id))
		{
			$this->modelError = $this->WeeklyReportsReportItem->modelError;
			return false;
		}
		return true;
	}
	
	public function viewExcelFile($id = null, $path = false)
	{
		if(!$id)
		{
			$id = $this->id;
		}
		if(!$id)
		{
			return false;
		}
		
		if(!$path)
		{
			$weekly_report = $this->read(null, $id);
			if(isset($weekly_report[$this->alias]['paths']['sys']))
			{
				$path = $weekly_report[$this->alias]['paths']['sys'];
			}
		}
		if(!$path)
		{
			return false;
		}
		
		if(!$html = $this->Excel_fileToHtml($path))
		{
			return false;
		}
		return $html;
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
		$report_item_ids = $this->WeeklyReportsReportItem->find('list', array(
			'fields' => array('WeeklyReportsReportItem.id', 'WeeklyReportsReportItem.report_item_id'),
			'conditions' => array('WeeklyReportsReportItem.weekly_report_id' => $id)
		));
		
		// delete the xref items first as the ReportItem->deleteAll checks for associations
		$this->WeeklyReportsReportItem->deleteAll(
			array('WeeklyReportsReportItem.weekly_report_id' => $id)
		);
		
		$this->ReportItem->deleteAll(
			array('ReportItem.id' => $report_item_ids)
		);
		
		// delete this weekly report
		return parent::delete($id, $cascade);
	}
	
	public function checkItems($id = false)
	{
		if(!$id) 
		{
			$this->modelError = __('Unknown %s ID.', __('Weekly Report'));
			return false;
		}
		
		$this->id = $id;
		if(!$this->exists())
		{
			$this->modelError = __('Unknown %s ID.', __('Weekly Report'));
			return false;
		}
		
		// go through each report item, and make sure every one has a ChargeCode, and activity assigned to it
		$report_items = $this->WeeklyReportsReportItem->find('all', array(
			'recursive' => 0,
			'contain' => array('ReportItem'),
			'conditions' => array(
				'WeeklyReportsReportItem.weekly_report_id' => $id,
			),
			'order' => array(
				'WeeklyReportsReportItem.item_state' => 'asc',
				'WeeklyReportsReportItem.item_order' => 'asc',
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
			if($report_item['WeeklyReportsReportItem']['highlighted']) 
				$highlighted++;
		}
		
		if(!$finalize)
		{
			$this->modelError = __('All %s must have a %s and a %s assigned to them.', __('Report Items'), __('Charge Code'), __('Activity'));
			return false;
		}
		
		if($highlighted != 2)
		{
			$this->modelError = __('Please %s only 2 %s. (%s %s: %s)', __('Highlight'), __('Report Items'), __('Report Items'), __('Highlighted'), $highlighted);
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
		if(isset($viewVars['weekly_report']['User']['email']))
		{
			$user_email = $viewVars['weekly_report']['User']['email'];
			$emails[$user_email] = $user_email;
			$this->Email_set('from', $user_email);
		}
		
		$this->Email_set('to', $emails);
		$this->Email_set('replyTo', $user_email);
		$subject = __('%s - %s', __('Weekly Report'), $viewVars['weekly_report']['WeeklyReport']['name']);
		$this->Email_set('subject', $subject);
		$this->Email_set('viewVars', $viewVars);
		
		// it can support both, as there is an html template for this
		// however, josh requested the text only as it is easier to read on a BB
//		$this->Email_set('emailFormat', 'both');
		$this->Email_set('emailFormat', 'text');
		$this->Email_set('template', 'weekly_finalized_email');
		
		$finalized_csv = $this->requestAction(array('controller' => 'weekly_reports', 'action' => 'finalize', $this->id, 'ext' => 'csv'), array('return'));
		if($finalized_csv)
		{
			$csv_filename = $viewVars['weekly_report']['WeeklyReport']['name']. '.csv';
			$this->Email_set('attachments', array(
				array($csv_filename => array('data' => $finalized_csv, 'mimetype' => 'text/plain'))
			));
		}
		
		return $this->Email_executeFull();
	}
	
	public function finalizedVars($id = false)
	{
		$this->modelError = false;
		
		$weekly_report = $this->find('first', array(
			'recursive' => 1,
			'conditions' => array('WeeklyReport.id' => $id),
			'contain' => array('Tag', 'User'),
		));
		
		$report_items = $this->WeeklyReportsReportItem->find('all', array(
			'recursive' => 1,
			'contain' => array('ReportItem'),
			'conditions' => array(
				'WeeklyReportsReportItem.weekly_report_id' => $id,
			),
			'order' => array(
				'WeeklyReportsReportItem.highlighted' => 'desc',
				'WeeklyReportsReportItem.item_state' => 'asc',
				'ReportItem.charge_code_id' => 'asc',
				'WeeklyReportsReportItem.item_order' => 'asc',
			),
		));
		
		$item_states = $this->WeeklyReportsReportItem->getItemStates();
		$item_charge_codes = $this->WeeklyReportsReportItem->ReportItem->ChargeCode->listForSortable();
		$item_activities = $this->WeeklyReportsReportItem->ReportItem->Activity->listForSortable();
		
		return array(
			'weekly_report' => $weekly_report,
			'item_states' => $item_states,
			'report_items' => $report_items,
			'item_charge_codes' => $item_charge_codes,
			'item_activities' => $item_activities,
		);
	}
	
	public function createName($username = false, $report_date = false)
	{
		if(!$report_date) $report_date = date('Y-m-d 00:00:00');
		
		if($username = ($username?$username:(AuthComponent::user('name')?AuthComponent::user('name'):false)))
		{
			$username = '- '. $username. ' ';
		}
		
		return __('Weekly Activity Report %s- %s', $username, date('m-d-Y', strtotime($report_date)));
	}
	
	public function formDefaults($username = false, $report_date = false, $include_name = true)
	{
		if(!$report_date) $report_date = date('Y-m-d 00:00:00');
		
		$report_date = date('Y-m-d 00:00:00', strtotime('Friday', strtotime($report_date)));

		$report_date_start = date('Y-m-d 00:00:00', strtotime('Last Saturday', strtotime($report_date)));
//		$report_date_end = date('Y-m-d 00:00:00', strtotime('Saturday', strtotime($report_date)));
		$report_date_end = $report_date;
		
		$return = array(
			'report_date' => $report_date,
			'report_date_start' => $report_date_start,
			'report_date_end' => $report_date_end,
		);
		if($include_name)
		{
			$return['name'] = $this->createName($username, $report_date);
		}
		return $return;
	}
}
