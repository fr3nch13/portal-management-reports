<?php
App::uses('AppModel', 'Model');

class ReportItem extends AppModel 
{

	public $displayField = 'item';
	
	public $validate = array(
		'item' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
//				'required' => true,
			),
		),
	);
	
	public $hasAndBelongsToMany = array(
		'DailyReport' => array(
			'className' => 'DailyReport',
			'joinTable' => 'daily_reports_report_items',
			'foreignKey' => 'daily_report_id',
			'associationForeignKey' => 'report_item_id',
			'unique' => 'keepExisting',
			'with' => 'DailyReportsReportItem',
		),
		'WeeklyReport' => array(
			'className' => 'WeeklyReport',
			'joinTable' => 'weekly_reports_report_items',
			'foreignKey' => 'weekly_report_id',
			'associationForeignKey' => 'report_item_id',
			'unique' => 'keepExisting',
			'with' => 'WeeklyReportsReportItem',
		),
		'ManagementReport' => array(
			'className' => 'ManagementReport',
			'joinTable' => 'management_reports_report_items',
			'foreignKey' => 'management_report_id',
			'associationForeignKey' => 'report_item_id',
			'unique' => 'keepExisting',
			'with' => 'ManagementReportsReportItem',
		),
	);
	
	public $hasMany = array(
		'DailyReportsReportItem' => array(
			'className' => 'DailyReportsReportItem',
			'foreignKey' => 'report_item_id',
			'dependent' => true,
		),
		'WeeklyReportsReportItem' => array(
			'className' => 'WeeklyReportsReportItem',
			'foreignKey' => 'report_item_id',
			'dependent' => true,
		),
		'ManagementReportsReportItem' => array(
			'className' => 'ManagementReportsReportItem',
			'foreignKey' => 'report_item_id',
			'dependent' => true,
		),
	);
	
	public $hasOne = array(
		'DailyReportsReportItem' => array(
			'className' => 'DailyReportsReportItem',
			'foreignKey' => 'report_item_id',
			'dependent' => false,
		),
		'WeeklyReportsReportItem' => array(
			'className' => 'WeeklyReportsReportItem',
			'foreignKey' => 'report_item_id',
			'dependent' => false,
		),
		'ManagementReportsReportItem' => array(
			'className' => 'ManagementReportsReportItem',
			'foreignKey' => 'report_item_id',
			'dependent' => false,
		),
	);
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'ChargeCode' => array(
			'className' => 'ChargeCode',
			'foreignKey' => 'charge_code_id',
			'plugin_snapshot' => true,
		),
		'Activity' => array(
			'className' => 'Activity',
			'foreignKey' => 'activity_id',
			'plugin_snapshot' => true,
		),
		'ReviewReason' => array(
			'className' => 'ReviewReason',
			'foreignKey' => 'review_reason_id',
			'plugin_snapshot' => true,
		),
		'ReportItemFavorite' => array(
			'className' => 'ReportItemFavorite',
			'foreignKey' => 'report_item_favorite_id',
		),
	);
	
	public $actsAs = array(
		'Tags.Taggable', 
		'Utilities.Extractor', 
		'Snapshot.Stat' => array(
			'entities' => array(
				'all' => array(),
				'created' => array(),
				'modified' => array(),
			),
		),
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'ReportItem.item',
		'User.name',
		'Activity.name',
		'ChargeCode.name',
	);
	
	// valid actions to take against multiselect items
	public $multiselectOptions = array('delete', 'charge_code', 'activity');
	
	public $saveManyIds = array();
	public $saveManyXref = array();
	public $saveManyXrefs = array();
	
			/////////////////////// 
			/////// IF YOU CHANGE HOW YOU DELETE ITEMS FROM DELETEALL TO DELETE,
			/////// MAKE SURE YOU DON'T DELETE AN ITEM ASSOCIATED WITH OTHERS (WEEKLY, DAILY, MANAGEMENT, $hasMany ABOVE)
	
	public function beforeSave($options = array())
	{
		// scan for keywords
		if(isset($this->data[$this->alias]['item']) and !isset($this->data[$this->alias]['item_keys']))
		{
			$this->data[$this->alias]['item'] = trim($this->data[$this->alias]['item'], '- ');
			
			$keywords = $this->extractKeywords($this->data[$this->alias]['item']);
			
			usort($keywords, function($a, $b) {
				return strlen($b) - strlen($a);
			});
			
			$this->data[$this->alias]['item_keys'] = implode(' ', $keywords);
		}
		
		if(isset($this->data[$this->alias]['item_date']))
		{
			$this->data[$this->alias]['item_date'] = date('Y-m-d H:i:s', strtotime($this->data[$this->alias]['item_date']));
		}
		
		// save/cache/pass this info for later reference
		$this->saveManyXref = $this->data[$this->alias];
		
		return parent::beforeSave($options);
	}
	
	public function afterSave($created = false, $options = array())
	{
		if($this->id)
		{
			$this->saveManyIds[$this->id] = $this->id;
			$this->saveManyXrefs[$this->id] = $this->saveManyXref;
		}
	}
	
	public function saveMany($data = array(), $options = array())
	{
	 	// reset the ids array
	 	$this->saveManyIds = array();
	 	
	 	return parent::saveMany($data, $options);
	}
	
	public function verifyDeletable($id = false, $data = array())
	{
		if(!$id) return false;
		if(!$data)
		{
			if(!$data = $this->find('first', array(
				'recursive' => 1,
				'conditions' => array(
					$this->alias.'.id' => $id,
				),
			))) return false;
		}
		
		$deleteable = true;
		foreach($this->hasMany as $modelAlias => $assocDetails)
		{
			if(isset($data[$modelAlias]))
			{
					// it's associated with another model somewhere
				if(count($data[$modelAlias])) $deleteable = false;
			}
		}
		return $deleteable;
	}
	
	public function deleteAll($conditions = array(), $cascade = true, $callbacks = false)
	{
		$report_items = $this->find('all', array('conditions' => $conditions, 'recursive' => 1));
		
		// only delete it if it has no other relationships
		$delete_ids = array();
		foreach($report_items as $report_item)
		{
			if($this->verifyDeletable($report_item[$this->alias]['id'], $report_item))
			{
				$delete_ids[$report_item[$this->alias]['id']] = $report_item[$this->alias]['id'];
			}
		}
		return parent::deleteAll(array(
			$this->alias.'.id' => $delete_ids,
		), $cascade, $callbacks);
	}
	
	public function snapshotStats()
	{
		$entities = $this->Snapshot_dynamicEntities();
		return array();
	}
}