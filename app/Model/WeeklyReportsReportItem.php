<?php
App::uses('AppModel', 'Model');

class WeeklyReportsReportItem extends AppModel 
{
	public $belongsTo = array(
		'WeeklyReport' => array(
			'className' => 'WeeklyReport',
			'foreignKey' => 'weekly_report_id',
		),
		'ReportItem' => array(
			'className' => 'ReportItem',
			'foreignKey' => 'report_item_id',
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
	);
	
	public $actsAs = array(
		'PhpExcel.PhpExcel', 
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'ReportItem.item',
	);
	
	public $item_states = array(
		0 => 'Unknown/Other',
		1 => 'Completed',
//		2 => 'Tomorrow',
		3 => 'On Going',
		4 => 'In Progress',
		5 => 'Planned',
		6 => 'Issues',
	);
	
	// when creating a new weekly report, 
	// this holds the daily report items for the selected range
	public $dailyReportItems = array();
	
	// holds the report items from the last weekly
	public $lastWeeklyReportItems = array();
	
	public $weeklyReportUserIds = array();
	
	
	public function __construct($id = false, $table = null, $ds = null)
	{
		// the item_states above are listed for defaults
		// their names can be set to something else in the app config
		// this uses the app config names and overwrites the above names
		if($item_states = Configure::read('WeeklyReport.item_states'))
		{
			foreach($this->item_states as $i => $item_state)
			{
				if(isset($item_states[$i]) and trim($item_states[$i])) $this->item_states[$i] = $item_states[$i];
			}
		}
		parent::__construct($id, $table, $ds);
	}
	
	public function beforeSave($options = array())
	{
		// being created
		if(!isset($this->data[$this->alias]['id']))
		{
			// needs item order
			if(!isset($this->data[$this->alias]['item_order']) and isset($this->data[$this->alias]['weekly_report_id']))
			{
				if(!$item_order = $this->find('count', array(
					'conditions' => array(
						$this->alias.'.weekly_report_id' => $this->data[$this->alias]['weekly_report_id']
					),
				)))
				{
					$item_order = 0;
				}
				$item_order++;
				$this->data[$this->alias]['item_order'] = $item_order;
			}
			
			// make sure the user_id is set
			if(!isset($this->data[$this->alias]['user_id']))
			{
				$user_id = 0;
				if(isset($this->data[$this->alias]['weekly_report_id']))
				{
					if(isset($this->weeklyReportUserIds[$this->data[$this->alias]['weekly_report_id']]))
						$user_id = $this->weeklyReportUserIds[$this->data[$this->alias]['weekly_report_id']];
					else
					{
						$weeklyReport = $this->WeeklyReport->read(null, $this->data[$this->alias]['weekly_report_id']);
						$user_id = $this->weeklyReportUserIds[$this->data[$this->alias]['weekly_report_id']] = $weeklyReport['WeeklyReport']['user_id'];
					}
				}
				if(!$user_id)
					$user_id = AuthComponent::user('id');
				$this->data[$this->alias]['user_id'] = $user_id;
			}
		}
		
		return parent::beforeSave($options);
	}
	
	public function afterFind($results = array(), $primary = false)
	{
		//// Fix the report items that don't have a set date
		return parent:: afterFind($results, $primary);
	}
	
	public function importWeeklyReport($data = array())
	{
		$user_id = false;
		
		$this->modelError = false;
		$this->WeeklyReport->modelError = false;
		
		if(isset($data[$this->WeeklyReport->alias]['user_id']))
		{	
			$user_id = $data[$this->WeeklyReport->alias]['user_id'];
		}
		else
		{
			$user_id = $data[$this->WeeklyReport->alias]['user_id'] = AuthComponent::user('id');
		}
		
		$report_date = false;
		if(isset($data[$this->WeeklyReport->alias]['report_date']))
			$report_date = $data[$this->WeeklyReport->alias]['report_date'];
		
		$report_date_start = false;
		if(isset($data[$this->WeeklyReport->alias]['report_date_start']))
			$report_date_start = $data[$this->WeeklyReport->alias]['report_date_start'];
		
		$report_date_end = false;
		if(isset($data[$this->WeeklyReport->alias]['report_date_end']))
			$report_date_end = $data[$this->WeeklyReport->alias]['report_date_end'];
		
		$import = false;
		if(isset($data[$this->WeeklyReport->alias]['import']))
			$import = $data[$this->WeeklyReport->alias]['import'];
		
		if(!isset($data[$this->WeeklyReport->alias]['user_id']) or !$data[$this->WeeklyReport->alias]['file'])
		{
			$error = __('Unknown File.');
			if(!$this->modelError) $this->modelError = $error;
			if(!$this->WeeklyReport->modelError) $this->WeeklyReport->modelError = $error;
			return false;
		}
		
		// save the new weekly report
		$this->WeeklyReport->data = $data;
		if(!$this->WeeklyReport->save($this->WeeklyReport->data))
		{
			$error = __('Unalbe to save the %s.', __('Weekly Report'));
			if(!$this->modelError) $this->modelError = $error;
			if(!$this->WeeklyReport->modelError) $this->WeeklyReport->modelError = $error;
			return false;
		}
		
		// scan the file
		if(!$results = $this->importItemsFromExcel($this->WeeklyReport->id))
		{
			$error = __('Unable to Import %s from the Excel File.', __('Report Items'));
			if(!$this->modelError) $this->modelError = $error;
			if(!$this->WeeklyReport->modelError) $this->WeeklyReport->modelError = $error;
			$this->WeeklyReport->delete($this->WeeklyReport->id);
			return false;
		}
		
		return true;
	}
	
	public function importItemsFromExcel($id)
	{
		$this->modelError = false;
		$this->WeeklyReport->modelError = false;
		
		if(!$id)
		{
			$id = $this->WeeklyReport->id;
		}
		
		if(!$id)
		{
			$error = __('No ID given for the %s.', __('Weekly Report'));
			if(!$this->modelError) $this->modelError = $error;
			if(!$this->WeeklyReport->modelError) $this->WeeklyReport->modelError = $error;
			return false;
		}
		
		// scan the file
		$results = $this->scanFile($id);
		if(!$results)
		{
			$error = __('No Results were found in the Excel File. (%s)', 1);
			if(!$this->modelError) $this->modelError = $error;
			if(!$this->WeeklyReport->modelError) $this->WeeklyReport->modelError = $error;
			return false;
		}
		
		$this->WeeklyReport->id = $id;
		$weeklyReport = $this->WeeklyReport->read(null, $id);
		$user_id = $weeklyReport['WeeklyReport']['user_id'];
		$report_date = $weeklyReport['WeeklyReport']['report_date'];
		$report_date_start = $weeklyReport['WeeklyReport']['report_date_start'];
		$report_date_end = $weeklyReport['WeeklyReport']['report_date_end'];
		$report_date_start = date('Ymd', strtotime($report_date_start));
		$report_date_end = date('Ymd', strtotime($report_date_end));
		
		// get the activity/charge_code/states
		$states = array_flip($this->getItemStates());
		$charge_codes = array_flip($this->ReportItem->ChargeCode->listForSortable(false, true));
		$activities = array_flip($this->ReportItem->Activity->listForSortable(false, true));
		
		$states['other'] = 0;
		$charge_codes['other'] = 0;
		$activities['other'] = 0;
		
		$results_keys = array();
		$results_key_map = array();
		
		//// determine which field in the array corresponds to which field in the database
		$db_field_map = array('date' => 'date', 'activity_type' => 'activity_id', 'description' => 'item', 'status' => 'state', 'group' => 'charge_code_id');
		reset($results);
		$result_keys = array_keys($results[0]);
		$result_keys = array_flip($result_keys);
		
		foreach($result_keys as $result_key => $blah)
		{
			$db_field = $this->findClosestMatch($result_key, $db_field_map, true);
			$result_keys[$result_key] = $db_field;
		}	
		
		$new_result_tmpl = $result_keys;
		sort($new_result_tmpl);
		$new_result_tmpl = array_flip($new_result_tmpl);
		$new_results = array();
		foreach($results as $i => $result)
		{
			$include = true;
			$new_result = $new_result_tmpl;
			foreach($result as $k => $v)
			{
				if(isset($result_keys[$k]))
				{
					$k = $result_keys[$k];
					$new_result[$k] = $v;
				}
				
				// find the id for the state
				if($k == 'state')
				{
					if($v)
						$v = $this->findClosestMatch($v, $states, true);
					else
						$v = 0;
				}
				
				if($k == 'activity_id')
				{
					if($v)
						$v = $this->findClosestMatch($v, $activities, true);
					else
						$v = 0;
				}
				
				if($k == 'charge_code_id')
				{
					if($v)
						$v = $this->findClosestMatch($v, $charge_codes, true);
					else
						$v = 0;
				}
				
				if($k == 'date')
				{
					if(!$v)
					{
						$v = date('Y-m-d 00:00:00', strtotime($report_date_start));
					}
					///// apparently Jerry has all of his items in his reports going back to 2012, just hidden somehow
					///// we're only interested in the ones that are within this reports start and end range
					else
					{
						$v = date('Ymd', strtotime($v));
						if($v < $report_date_start or $v > $report_date_end)
						{
							$include = false;
						}
						$v = date('Y-m-d 00:00:00', strtotime($v));
						$new_result['item_date_set'] = true;
					}
				}
				
				// save the adjusted results
				$new_result[$k] = $v;
			}
			
			if(!$new_result['item']) continue;
			
			if($include)
				$new_results[] = $new_result;
		}
		
		unset($results);
		
		if(!$new_results)
		{
			$error = __('No Results were found in the Excel File. (%s)', 2);
			if(!$this->modelError) $this->modelError = $error;
			if(!$this->WeeklyReport->modelError) $this->WeeklyReport->modelError = $error;
			return false;
		}
		
		/// save the items to the database
		$items = array();
		$item_orders = array();
		$report_item_ids = array();
		$report_xref_stuff = array();
		foreach($new_results as $new_result)
		{
			if(!isset($new_result['item']) or !$new_result['item']) continue;
			
			$item_state = (isset($new_result['state'])?$new_result['state']:0);
			
			if(!isset($item_orders[$item_state])) $item_orders[$item_state] = 0;
			
			$items[] = array(
				'item' => $new_result['item'], 
				'user_id' => $user_id, 
				'item_state' => $item_state,
				'item_order' => $item_orders[$item_state],
				'charge_code_id' => (isset($new_result['charge_code_id'])?$new_result['charge_code_id']:0),
				'activity_id' => (isset($new_result['activity_id'])?$new_result['activity_id']:0),
				'item_date' => (isset($new_result['date'])?$new_result['date']:false),
				'item_date_set' => (isset($new_result['item_date_set'])?$new_result['item_date_set']:false),
			);
			$item_orders[$item_state]++;
		}
		
		// save all of the report items
		if(count($items))
		{
			if($this->ReportItem->saveMany($items))
			{	
				// create the xref for the items and this report
				$report_item_ids = $this->ReportItem->saveManyIds;
				foreach($this->ReportItem->saveManyIds as $report_item_id)
				{
					$report_item_ids[$report_item_id] = $report_item_id;
				}
				
				foreach($this->ReportItem->saveManyXrefs as $report_item_id => $xref_stuff)
				{
					$report_xref_stuff[$report_item_id] = $xref_stuff;
				}
			}
		}
			
		if(count($report_item_ids))
		{	
			return $this->saveAssociations($id, $report_item_ids, $report_xref_stuff);
		}
/*
				$items[] = array(
					'item' => $this_item, 
					'user_id' => $user_id, 
					'item_state' => ($item_state === false?0:$item_state), 
					'item_order' => $item_order,
				);
*/
	}
	
	public function scanFile($id = null, $weekly_report_filepath = array())
	{
		if(!$id)
		{
			$id = $this->id;
		}
		if(!$id)
		{
			return false;
		}
		
		if(!$weekly_report_filepath)
		{
			$weekly_report = $this->WeeklyReport->read(null, $id);
			$weekly_report_filepath = $weekly_report['WeeklyReport']['paths']['sys'];
		}
		
		$this->modelError = false;
		if(!$results = $this->Excel_fileToArray($weekly_report_filepath))
		{
			if($this->modelError)
			{
				$this->modelError = __('An issue occurred when trying to scan the Excel file.');
			}
			return false;
		}
		return $results;
	}
	
	public function addWeeklyReport($data = array())
	{
		$user_id = false;
		
		if(isset($data[$this->WeeklyReport->alias]['user_id']))
		{	
			$user_id = $data[$this->WeeklyReport->alias]['user_id'];
		}
		
		$report_date = false;
		$favDate = date('Y-m-d');
		if(isset($data[$this->WeeklyReport->alias]['report_date']))
		{
			$report_date = $data[$this->WeeklyReport->alias]['report_date'];
			$favDate = date('Y-m-d', strtotime($data[$this->WeeklyReport->alias]['report_date']));
		}
		
		$report_date_start = false;
		if(isset($data[$this->WeeklyReport->alias]['report_date_start']))
			$report_date_start = $data[$this->WeeklyReport->alias]['report_date_start'];
		
		$report_date_end = false;
		if(isset($data[$this->WeeklyReport->alias]['report_date_end']))
			$report_date_end = $data[$this->WeeklyReport->alias]['report_date_end'];
		
		$import = false;
		if(isset($data[$this->WeeklyReport->alias]['import']))
			$import = $data[$this->WeeklyReport->alias]['import'];
		
		$import_weekly = false;
		if(isset($data[$this->WeeklyReport->alias]['import_weekly']))
		{
			$import_weekly = $data[$this->WeeklyReport->alias]['import_weekly'];
			if(!isset($this->lastWeekly))
			{
				$this->lastWeekly = $this->WeeklyReport->find('first', array(
					'conditions' => array(
						'WeeklyReport.user_id' => $user_id,
						'WeeklyReport.report_date < ' => $report_date,
						'WeeklyReport.finalized' => true,
					),
					'order' => array('WeeklyReport.report_date' => 'desc'),
				));
			}
		}
		
		// save the new weekly report
		$this->WeeklyReport->data = $data;
		if(!$this->WeeklyReport->save($this->WeeklyReport->data))
		{
			return false;
		}
		
		foreach($data['report_items'] as $item_state => $report_items)
		{
			$this->addReportItems($this->WeeklyReport->id, $user_id, $report_items, $item_state, $import, $report_date_start, $report_date_end, $import_weekly);
		}
		
		// import favorites
		if($user_id and isset($data[$this->WeeklyReport->alias]['favorites']) and $data[$this->WeeklyReport->alias]['favorites'])
		{
			$favoriteIds = $this->ReportItem->ReportItemFavorite->getMatchedFavorites($user_id, $favDate, 'list', 'week');
			if($favoriteIds)
			{
				$favoriteData = [
					'model' => $this->alias,
					'ids' => [
						'user_id' => $user_id,
						'weekly_report_id' => $this->WeeklyReport->id,
					],
					'ReportItemFavorite' => ['items' => array_keys($favoriteIds)],
				];
				$this->ReportItem->ReportItemFavorite->addToReport($favoriteData);
			}
		}
		
		return true;
	}
	
	public function favoritesAdd($data = array())
	{
		$items = array();
		$report_item_ids = array();
		$report_xref_stuff = array();
		
		if(!isset($data[0]['weekly_report_id']))
		{
			$this->modelError = __('Unknown %s (%s)', __('Weekly Report'), 1);
			return false;
		}
		
		if(!$weekly_report_id = $data[0]['weekly_report_id'])
		{
			$this->modelError = __('Unknown %s (%s)', __('Weekly Report'), 2);
			return false;
		}
		
		$this->WeeklyReport->id = $weekly_report_id;
		$item_date = $this->WeeklyReport->field('report_date_start');
		
		/// set the item_date
		foreach($data as $i => $item)
		{
			$this_item_date = $item_date;
			if(isset($item['sun']) and $item['sun'])
			{
				$this_item_date = date('Y-m-d 00:00:00', strtotime('next Sunday', strtotime($item_date)));
			}
			elseif(isset($item['mon']) and $item['mon'])
			{
				$this_item_date = date('Y-m-d 00:00:00', strtotime('next Monday', strtotime($item_date)));
			}
			elseif(isset($item['tue']) and $item['tue'])
			{
				$this_item_date = date('Y-m-d 00:00:00', strtotime('next Tuesday', strtotime($item_date)));
			}
			elseif(isset($item['wed']) and $item['wed'])
			{
				$this_item_date = date('Y-m-d 00:00:00', strtotime('next Wednesday', strtotime($item_date)));
			}
			elseif(isset($item['thu']) and $item['thu'])
			{
				$this_item_date = date('Y-m-d 00:00:00', strtotime('next Thursday', strtotime($item_date)));
			}
			elseif(isset($item['fri']) and $item['fri'])
			{
				$this_item_date = date('Y-m-d 00:00:00', strtotime('next Friday', strtotime($item_date)));
			}
			$data[$i]['item_date'] = $this_item_date;
		}
		
		if($this->ReportItem->saveMany($data))
		{
			// create the xref for the items and this report
			$report_item_ids = $this->ReportItem->saveManyIds;
			// create the xref for the items and this report
			$report_xref_stuff = $this->ReportItem->saveManyXrefs;
			
			return $this->saveAssociations($weekly_report_id, $report_item_ids, $report_xref_stuff);
		}
		return false;
	}
	
	public function addToReport($weekly_report_id = false, $data = array())
	{
		if(!$weekly_report_id)
		{
			return false;
		}
		
		if(!isset($data['report_items']))
		{
			return false;
		}
		
		$user_id = false;
		if(isset($data[$this->alias]['user_id']))
		{	
			$user_id = $data[$this->alias]['user_id'];
		}
		
		foreach($data['report_items'] as $item_state => $report_items)
		{
			$this->addReportItems($weekly_report_id, $user_id, $report_items, $item_state);
		}
		
		return true;
	}
	
	public function addReportItems($weekly_report_id = false, $user_id = false, $this_items = array(), $item_state = false, $import = false, $report_date_start = false, $report_date_end = false, $import_weekly = false)
	{		
		// ignore any states we don't need, like 'tomorrow'
		if(!isset($this->item_states[$item_state]))
		{
			return;
		}
		
		if(!isset($this->weekly_report))
		{
			$this->weekly_report = $this->WeeklyReport->read(null, $weekly_report_id);
		}
		
		$user_id = ($user_id?$user_id:$this->weekly_report['WeeklyReport']['user_id']);
		$report_date = $this->weekly_report['WeeklyReport']['report_date'];
		$report_date_start = ($report_date_start?$report_date_start:$this->weekly_report['WeeklyReport']['report_date_start']);
		$report_date_end = ($report_date_end?$report_date_end:$this->weekly_report['WeeklyReport']['report_date_end']);
		
		$items = array();
		$report_item_ids = array();
		$report_xref_stuff = array();
		
		///////// Import from last weekly report based on the report_date field
		if($import_weekly and $this->lastWeekly)
		{
			if(!count($this->lastWeeklyReportItems))
			{
				/// all but the completed ones from the last report, should only run once, instead of once per item_state
				$this->lastWeeklyReportItems = $this->find('all', array(
					'recursive' => 0,
					'conditions' => array(
						$this->alias.'.weekly_report_id' => $this->lastWeekly['WeeklyReport']['id'],
						$this->alias.'.item_state !=' => 1,
					),
				));
				
				$weekly_items = array();
				foreach($this->lastWeeklyReportItems as $i => $lastWeeklyReportItem)
				{
					$weekly_items[] = array(
						'item' => $lastWeeklyReportItem['ReportItem']['item'],
						'report_item_id' => $lastWeeklyReportItem['ReportItem']['report_item_id'], 
						'report_item_favorite_id' => $lastWeeklyReportItem['ReportItem']['report_item_favorite_id'], 
						'charge_code_id' => $lastWeeklyReportItem['ReportItem']['charge_code_id'], 
						'activity_id' => $lastWeeklyReportItem['ReportItem']['activity_id'], 
						'item_keys' => $lastWeeklyReportItem['ReportItem']['item_keys'],
						'user_id' => $user_id, 
						'highlighted' => $lastWeeklyReportItem[$this->alias]['highlighted'],
						'item_state' => $lastWeeklyReportItem[$this->alias]['item_state'], 
						'item_order' => $lastWeeklyReportItem[$this->alias]['item_order'],
						'item_date' => $this->weekly_report['WeeklyReport']['report_date_start'],
					);
				}
		
				$weekly_report_item_ids = array();
				$weekly_report_xref_stuff = array();
				// save all of the imported weekly report items
				if(count($weekly_items))
				{
					if($this->ReportItem->saveMany($weekly_items))
					{	
						// create the xref for the items and this report
						foreach($this->ReportItem->saveManyIds as $report_item_id)
						{
							$weekly_report_item_ids[$report_item_id] = $report_item_id;
						}
						
						foreach($this->ReportItem->saveManyXrefs as $report_item_id => $xref_stuff)
						{
							$weekly_report_xref_stuff[$report_item_id] = $xref_stuff;
						}
					}
				}
				
				if(count($weekly_report_item_ids))
				{	
					$this->saveAssociations($weekly_report_id, $weekly_report_item_ids, $weekly_report_xref_stuff);
				}
			}
		}
		// import the items from the daily reports
		if($import 
		and $user_id
		and $report_date_start
		and $report_date_end)
		{
			// grab all of the daily report items from the selected range
			if(!count($this->dailyReportItems))
			{
				$conditions = array(
					'DailyReportsReportItem.user_id' => $user_id,
					'DailyReport.user_id' => $user_id,
					'DailyReport.finalized' => true,
					'DailyReport.report_date BETWEEN ? AND ?' => array(
						date('Y-m-d 00:00:00', strtotime($report_date_start)),
						date('Y-m-d 23:59:59', strtotime($report_date_end)),
					),
				);
				
				$order = array(
					'DailyReport.report_date' => 'DESC',
					'DailyReportsReportItem.item_state' => 'ASC',
					'DailyReportsReportItem.item_order' => 'ASC',
				);
				
				$this->dailyReportItems = $this->ReportItem->DailyReportsReportItem->find('all', array(
					'recursive' => 0,
					'contain' => array('DailyReport', 'ReportItem'),
					'conditions' => $conditions,
					'order' => $order,
				));
			}
			
			$daily_report_id = false;
			$item_order = $complete_order = 0;
			foreach($this->dailyReportItems as $i => $dailyReportItem)
			{
				// used to make sure we're pulling only from the last finalized report
				if(!$daily_report_id)
				{
					$daily_report_id = $dailyReportItem['DailyReport']['id'];
				}
				
				$save = false;
				
				// get the completed from all of the daily reports
				if($item_state == 1 and $dailyReportItem['DailyReportsReportItem']['item_state'] == $item_state)
				{
					$save = true;
					$item_order = $complete_order;
					$complete_order++;
				}
				
				// get from the last report for the other item states
				elseif($dailyReportItem['DailyReportsReportItem']['item_state'] == $item_state)
				{
					if($daily_report_id != $dailyReportItem['DailyReport']['id'])
					{
						continue;
					}
					$save = true;
					$item_order = $dailyReportItem['DailyReportsReportItem']['item_order'];
				}
				
				if($save)
				{
					$report_item_id = $dailyReportItem['ReportItem']['id'];
					$report_item_ids[$report_item_id] = $report_item_id;
					$report_xref_stuff[$report_item_id] = array(
						'report_item_id' => $report_item_id,
						'user_id' => $dailyReportItem['DailyReportsReportItem']['user_id'],
						'item_state' => $dailyReportItem['DailyReportsReportItem']['item_state'],
						'item_order' => $item_order,
					);
				}
			}
			
			// invert the completed order, oldest first
			if($item_state == 1)
			{
				$report_xref_stuff_count = count($report_xref_stuff);
				foreach($report_xref_stuff as $report_item_id => $report_xref)
				{
					$report_xref_stuff[$report_item_id]['item_order'] = ($report_xref_stuff_count - $report_xref_stuff[$report_item_id]['item_order']);
				}
			}
		}
		
		// process the items from the batch add field
		if(isset($this_items))
		{
			$item_order = 0;
			
			// if completed, make sure we're adding to the bottom of the list
			if($item_state === 1)
			{
				// find out how many are in there, and make the order that count, the order will be updated below
				if($item_order = $this->find('count', array(
					'conditions' => array(
						$this->alias. '.weekly_report_id' => $weekly_report_id,
						$this->alias. '.item_state' => $item_state,
					),
				)))
				{
					$item_order++;
				}
				else
				{
					$item_order = 0;
				}
			}
				
			if(is_string($this_items))
			{
				$this_items = trim($this_items);
				$this_items = explode("\n", $this_items);
			}
			
			// clean up the items in the list
			foreach($this_items as $i => $this_item)
			{
				$this_item = trim($this_item);
				if(!$this_item) { unset($this_items[$i]); continue;}
				
				$items[] = array(
					'item' => $this_item, 
					'user_id' => $user_id, 
					'item_state' => ($item_state === false?0:$item_state), 
					'item_order' => $item_order,
					'item_date' => $this->weekly_report['WeeklyReport']['report_date'],
				);
				
				$item_order++;
			}
		}
		
		// save all of the report items
		if(count($items))
		{
			if($this->ReportItem->saveMany($items))
			{	
				// create the xref for the items and this report
				$report_item_ids = $this->ReportItem->saveManyIds;
				foreach($this->ReportItem->saveManyIds as $report_item_id)
				{
					$report_item_ids[$report_item_id] = $report_item_id;
				}
				
				foreach($this->ReportItem->saveManyXrefs as $report_item_id => $xref_stuff)
				{
					$report_xref_stuff[$report_item_id] = $xref_stuff;
				}
			}
		}
			
		if(count($report_item_ids))
		{	
			return $this->saveAssociations($weekly_report_id, $report_item_ids, $report_xref_stuff);
		}
	}
	
	public function saveAssociations($weekly_report_id = false, $report_item_ids = array(), $xref_data = array())
	{
		$existing = $this->find('list', array(
			'recursive' => -1,
			'fields' => array('WeeklyReportsReportItem.id', 'WeeklyReportsReportItem.report_item_id'),
			'conditions' => array(
				'WeeklyReportsReportItem.weekly_report_id' => $weekly_report_id,
			),
		));
		
		// get just the new ones
		$report_item_ids = array_diff($report_item_ids, $existing);
		
		// build the proper save array
		$data = array();
		foreach($report_item_ids as $report_item => $report_item_id)
		{
			$data[$report_item] = array('weekly_report_id' => $weekly_report_id, 'report_item_id' => $report_item_id, 'active' => 1);
			if(isset($xref_data[$report_item]))
			{
				$data[$report_item] = array_merge($xref_data[$report_item], $data[$report_item]);
			}
		}
		
		return $this->saveMany($data);
	}
	
	public function multiselect_items($data = array(), $values = array())
	{
		$this->multiselectReferer = array();
		if(isset($data[$this->alias]['multiselect_referer']))
		{
			$this->multiselectReferer = unserialize($data[$this->alias]['multiselect_referer']);
		}
		
		$ids = array();
		if(isset($data['multiple']))
		{
			$ids = $data['multiple'];
		}
		
		foreach($ids as $xref_id => $reportItem_id)
		{
			$this->ReportItem->id = $reportItem_id;
			$this->ReportItem->data = $values;
			if(!$this->ReportItem->save($this->ReportItem->data))
				return false;
		}
		return true;
	}
	
	public function multiselect_items_multiple($data = array(), $values = array())
	{
		$this->multiselectReferer = array();
		if(isset($data[$this->alias]['multiselect_referer']))
		{
			$this->multiselectReferer = unserialize($data[$this->alias]['multiselect_referer']);
		}
		
		return $this->ReportItem->saveMany($values);
	}
	
	public function updateItemsStateOrders($weekly_report_id = false, $item_state = false, $xref_ids = array())
	{
	// should only be called from the update_order method in the controller
		$manyData = array();
		foreach($xref_ids as $order => $xref_id)
		{
			$manyData[] = array(
				'id' => $xref_id,
				'item_state' => $item_state,
				'item_order' => $order,
			);
		}
		
		$out = array();
		if($this->saveMany($manyData))
		{
			$out = $this->getItemsForAjax($weekly_report_id);
		}
		return $out;
	}
	
	public function deleteReportItems($weekly_report_id = false, $xref_ids = array())
	{
		// we want to delete the report items, along with the xref records
		// also only delete the items in this weekly report
		$report_item_ids = $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias.'.weekly_report_id' => $weekly_report_id,
				$this->alias.'.id' => $xref_ids,
			),
			'fields' => array($this->alias.'.id', $this->alias.'.report_item_id'),
		));
		
		
		if($weekly_report_id and count($report_item_ids))
		{
			$this->deleteAll(array(
				$this->alias.'.weekly_report_id' => $weekly_report_id,
				$this->alias.'.id' => array_keys($report_item_ids),
			));
			
			/////////////////////// 
			/////// IF YOU CHANGE HOW YOU DELETE ITEMS FROM DELETEALL TO DELETE,
			/////// MAKE SURE YOU DON'T DELETE AN ITEM ASSOCIATED WITH OTHERS (WEEKLY, DAILY, MANAGEMENT)
			$this->ReportItem->deleteAll(array('ReportItem.id' => $report_item_ids));
		}
		
		$out = $this->getItemsForAjax($weekly_report_id);
		return $out;
	}
	
	public function assignChargeCodeReportItems($weekly_report_id = false, $charge_code_id, $xref_ids = array())
	{
		// get the list of report_items to update
		$report_item_ids = $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias.'.weekly_report_id' => $weekly_report_id,
				$this->alias.'.id' => $xref_ids,
			),
			'fields' => array($this->alias.'.id', $this->alias.'.report_item_id'),
		));
		
		// update the report_items wit the new charge_code_id
		if($weekly_report_id and count($report_item_ids))
		{
			$this->ReportItem->updateAll(
    			array('ReportItem.charge_code_id' => $charge_code_id),
    			array('ReportItem.id' => $report_item_ids)
			);
		}
		
		$out = $this->getItemsForAjax($weekly_report_id);
		return $out;
	}
	
	public function assignActivityReportItems($weekly_report_id = false, $activity_id, $xref_ids = array())
	{
		// get the list of report_items to update
		$report_item_ids = $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias.'.weekly_report_id' => $weekly_report_id,
				$this->alias.'.id' => $xref_ids,
			),
			'fields' => array($this->alias.'.id', $this->alias.'.report_item_id'),
		));
		
		// update the report_items wit the new charge_code_id
		if($weekly_report_id and count($report_item_ids))
		{
			$this->ReportItem->updateAll(
    			array('ReportItem.activity_id' => $activity_id),
    			array('ReportItem.id' => $report_item_ids)
			);
		}
		
		$out = $this->getItemsForAjax($weekly_report_id);
		return $out;
	}
	
	public function assignHighlightReportItems($weekly_report_id = false, $highlighted = true, $xref_ids = array())
	{
		
		// verify these items belong to this weekly report
		$report_item_ids = $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias.'.weekly_report_id' => $weekly_report_id,
				$this->alias.'.id' => $xref_ids,
			),
			'fields' => array($this->alias.'.id', $this->alias.'.id'),
		));
		
		if($weekly_report_id and count($report_item_ids))
		{
			$this->updateAll(
				array($this->alias.'.highlighted' => ($highlighted?true:false)),
				array($this->alias.'.id' => $xref_ids)
			);
		}
		
		$out = $this->getItemsForAjax($weekly_report_id);
		return $out;
	}
	
	public function getItemsForAjax($weekly_report_id = false)
	{
		$out = array();
		if(!$weekly_report_id)
		{
			return $out;
		}
		
		// return a list of the item_state counts
		// these will be used to update the details page counts
		$items = $this->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias.'.weekly_report_id' => $weekly_report_id,
			),
			'fields' => array(
				$this->alias.'.id', 
				$this->alias.'.item_state',
				$this->alias.'.highlighted'
			),
		));
		
		$out['all'] = count($items);
		$out['highlighted'] = 0;
		
		// fill out the item states
		$item_states = $this->getItemStates();
		foreach($item_states as $i => $item_state)
		{
			$out[$i] = 0;
		}
		
		$item_ids = array();
		foreach($items as $item)
		{
			$item_id = $item[$this->alias]['id'];
			$item_ids[$item_id] = $item_id;
			
			$item_state = $item[$this->alias]['item_state'];
			if(!isset($out[$item_state])) $out[$item_state] = 0;
			$out[$item_state]++;
			
			if($item[$this->alias]['highlighted'])
			{
				$out['highlighted']++;
			}
		}
		$out['item_ids'] = $item_ids;
		
		return $out;
	}
	
	public function getItemStates($index = false)
	{
		if($index === false) return $this->item_states;
		if(isset($this->item_states[$index])) return $this->item_states[$index];
		return false;
	}
	
	public function fixUserIssue()
	{
		Configure::write('debug', 1);
		$weeklyReportsReportItems = $this->find('all', array(
			'conditions' => array(
				'OR' => array(
					$this->alias.'.user_id' => 0,
					'ReportItem.user_id' => 0,
				),
			),
			'contain' => array('ReportItem', 'WeeklyReport'),
		));
		$this->shellOut(__('Found %s that need to be fixed', count($weeklyReportsReportItems)));
		
		$check = 0;
		$check2 = 0;
		$saveMany_xref = array();
		$saveMany_reportItem = array();
		foreach($weeklyReportsReportItems as $weeklyReportsReportItem)
		{
			$xref_id = $weeklyReportsReportItem['WeeklyReportsReportItem']['id'];
			$reportItem_id = $weeklyReportsReportItem['ReportItem']['id'];
			if($weeklyReportsReportItem['WeeklyReport']['user_id'] > 0)
			{
				if($weeklyReportsReportItem['WeeklyReportsReportItem']['user_id'] == 0)
				{
					$saveMany_xref[$xref_id] = array(
						'id' => $xref_id,
						'user_id' => $weeklyReportsReportItem['WeeklyReport']['user_id'],
						'modified' => $weeklyReportsReportItem['WeeklyReportsReportItem']['modified'],
					);
				}
				
				if($weeklyReportsReportItem['ReportItem']['user_id'] == 0)
				{
					$saveMany_reportItem[$reportItem_id] = array(
						'id' => $reportItem_id,
						'user_id' => $weeklyReportsReportItem['WeeklyReport']['user_id'],
						'modified' => $weeklyReportsReportItem['ReportItem']['modified'],
					);
				}
			}
		}
		
		$this->shellOut(__('Found %s xrefs that need to be fixed', count($saveMany_xref)));
		if(count($saveMany_xref) > 0)
		{
			$this->saveMany($saveMany_xref);
		}
		
		$this->shellOut(__('Found %s report items that need to be fixed', count($saveMany_reportItem)));
		if(count($saveMany_reportItem))
		{
			$this->ReportItem->saveMany($saveMany_reportItem);
		}
	}
}