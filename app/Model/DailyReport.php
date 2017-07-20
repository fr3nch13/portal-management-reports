<?php
App::uses('AppModel', 'Model');

class DailyReport extends AppModel 
{	
	public $validate = array(
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
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
			'joinTable' => 'daily_reports_report_items',
			'foreignKey' => 'daily_report_id',
			'associationForeignKey' => 'report_item_id',
			'unique' => 'keepExisting',
			'with' => 'DailyReportsReportItem',
		),
	);
	
	public $hasMany = array(
		'DailyReportsReportItem' => array(
			'className' => 'DailyReportsReportItem',
			'foreignKey' => 'daily_report_id',
			'dependent' => true,
		),
	);
	
	public $actsAs = array(
		'Tags.Taggable', 
		'Utilities.Email',
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'DailyReport.name',
	);
	
	public $report_date = false;
	
	public function beforeSave($options = array())
	{
		// scan for keywords
		if(isset($this->data[$this->alias]['report_date']))
		{
			$this->report_date = $this->data[$this->alias]['report_date'];
		}
		
		return parent::beforeSave($options);
	}
	
	public function afterSave($created = false, $options = array())
	{
		if(!$created and $this->report_date)
		{
			// update the dates of all of the report items in this report to match the reports' date
			$this->DailyReportsReportItem->updateDates($this->id, $this->report_date);
			$this->report_date = false;
		}
		
		return parent::afterSave($created, $options);
	}
	
	public function addReport($data = null)
	{
		$this->markLast = true;
		if(isset($data[$this->alias]['user_id']))
		{
			$this->userId = $data[$this->alias]['user_id'];
		}
		
		return $this->DailyReportsReportItem->addDailyReport($data);
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
		
		$this->id = $id;
		
		// find all report items associated with this report
		// the habtm relationship won't delete them.
		$report_item_ids = $this->DailyReportsReportItem->find('list', array(
			'fields' => array('DailyReportsReportItem.id', 'DailyReportsReportItem.report_item_id'),
			'conditions' => array('DailyReportsReportItem.daily_report_id' => $id)
		));
		
		// delete the xref items first as the ReportItem->deleteAll checks for associations
		$this->DailyReportsReportItem->deleteAll(
			array('DailyReportsReportItem.daily_report_id' => $id)
		);
		
		// delete the report items
		$this->ReportItem->deleteAll(
			array('ReportItem.id' => $report_item_ids)
		);
		
		// delete this daily report
		return parent::delete($id, $cascade);
	}
	
	public function checkItems($id = false)
	{
		if(!$id) 
		{
			$this->modelError = __('Unknown %s ID.', __('Daily Report'));
			return false;
		}
		
		$this->id = $id;
		if(!$this->exists())
		{
			$this->modelError = __('Unknown %s ID.', __('Daily Report'));
			return false;
		}
		
		// go through each report item, and make sure every one has a ChargeCode, and activity assigned to it
		$report_items = $this->DailyReportsReportItem->find('all', array(
			'recursive' => 0,
			'contain' => array('ReportItem'),
			'conditions' => array(
				'DailyReportsReportItem.daily_report_id' => $id,
			),
			'order' => array(
				'DailyReportsReportItem.item_state' => 'asc',
				'DailyReportsReportItem.item_order' => 'asc',
			),
		));
		
		$finalize = true;
		foreach($report_items as $i => $report_item)
		{
			if(!$report_item['ReportItem']['charge_code_id'])
				$finalize = false;
			if(!$report_item['ReportItem']['activity_id'])
				$finalize = false;
		}
		
		if(!$finalize)
		{
			$this->modelError = __('All %s must have a %s and a %s assigned to them.', __('Report Items'), __('Charge Code'), __('Activity'));
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
		if(isset($viewVars['daily_report']['User']['email']))
		{
			$user_email = $viewVars['daily_report']['User']['email'];
			$emails[$user_email] = $user_email;
			$this->Email_set('from', $user_email);
		}
		
		$this->Email_set('to', $emails);
		$this->Email_set('replyTo', $user_email);
		$this->Email_set('subject', __('%s - %s', __('Daily Report'), $viewVars['daily_report']['DailyReport']['name']));
		$this->Email_set('viewVars', $viewVars);
		
		// it can support both, as there is an html template for this
		// however, josh requested the text only as it is easier to read on a BB
//		$this->Email_set('emailFormat', 'both');
		$this->Email_set('emailFormat', 'text');
		$this->Email_set('template', 'daily_finalized_email');
		
		$finalized_csv = $this->requestAction(array('controller' => 'daily_reports', 'action' => 'finalize', $this->id, 'ext' => 'csv'), array('return'));
		if($finalized_csv)
		{
			$csv_filename = $viewVars['daily_report']['DailyReport']['name']. '.csv';
			$this->Email_set('attachments', array(
				array($csv_filename => array('data' => $finalized_csv, 'mimetype' => 'text/plain'))
			));
		}
		
		Configure::write('debug', 0);
		
		return $this->Email_executeFull();
	}
	
	public function finalizedVars($id = false)
	{
		$daily_report = $this->find('first', array(
			'recursive' => 1,
			'conditions' => array('DailyReport.id' => $id),
			'contain' => array('Tag', 'User'),
		));
		
		$report_items = $this->DailyReportsReportItem->find('all', array(
			'recursive' => 1,
			'contain' => array('ReportItem'),
			'conditions' => array(
				'DailyReportsReportItem.daily_report_id' => $id,
			),
			'order' => array(
				'DailyReportsReportItem.item_state' => 'asc',
				'ReportItem.charge_code_id' => 'asc',
				'DailyReportsReportItem.item_order' => 'asc',
			),
		));
		
		$item_states = $this->DailyReportsReportItem->getItemStates();
		$item_charge_codes = $this->DailyReportsReportItem->ReportItem->ChargeCode->listForSortable();
		$item_activities = $this->DailyReportsReportItem->ReportItem->Activity->listForSortable();
		
		return array(
			'daily_report' => $daily_report,
			'item_states' => $item_states,
			'report_items' => $report_items,
			'item_charge_codes' => $item_charge_codes,
			'item_activities' => $item_activities,
		);
	}
}
