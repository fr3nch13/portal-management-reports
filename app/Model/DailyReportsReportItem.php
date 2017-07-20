<?php
App::uses('AppModel', 'Model');

class DailyReportsReportItem extends AppModel 
{
	public $belongsTo = array(
		'DailyReport' => array(
			'className' => 'DailyReport',
			'foreignKey' => 'daily_report_id',
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
	
	// define the fields that can be searched
	public $searchFields = array(
		'ReportItem.item',
	);
	
	public $item_states = array(
		0 => 'Unknown/Other',
		1 => 'Completed',
		2 => 'Tomorrow',
		3 => 'On Going',
		4 => 'In Progress',
		5 => 'Planned',
		6 => 'Issues',
	);
	
	public $DailyReportData = false;
	
	public function __construct($id = false, $table = null, $ds = null)
	{
		// the item_states above are listed for defaults
		// their names can be set to something else in the app config
		// this uses the app config names and overwrites the above names
		if($item_states = Configure::read('DailyReport.item_states'))
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
			if(!isset($this->data[$this->alias]['item_order']) and isset($this->data[$this->alias]['daily_report_id']))
			{
				if(!$item_order = $this->find('count', array(
					'conditions' => array(
						$this->alias.'.daily_report_id' => $this->data[$this->alias]['daily_report_id']
					),
				)))
				{
					$item_order = 0;
				}
				$item_order++;
				$this->data[$this->alias]['item_order'] = $item_order;
			}
		}
		
		return parent::beforeSave($options);
	}
	
	public function addDailyReport($data = array())
	{
		$user_id = false;
		
		if(isset($data[$this->DailyReport->alias]['user_id']))
		{	
			$user_id = $data[$this->DailyReport->alias]['user_id'];
		}
		
		$favDate = date('Y-m-d');
		if(isset($data[$this->DailyReport->alias]['report_date']))
				$favDate = date('Y-m-d', strtotime($data[$this->DailyReport->alias]['report_date']));
		
		$daily_report_import_id = false;
		if($user_id and isset($data[$this->DailyReport->alias]['import']) and $data[$this->DailyReport->alias]['import'])
		{
			$import_conditions = array(
				'DailyReport.user_id' => $user_id,
				'DailyReport.finalized' => true,
			);
			
			if(isset($data[$this->DailyReport->alias]['report_date']))
			{
				$import_conditions['DailyReport.report_date <'] = $data[$this->DailyReport->alias]['report_date'];
			}
			
			// find the last report by report_date that is before this report's report_date
			$daily_report_import_id = $this->DailyReport->field('id', $import_conditions, 'DailyReport.report_date DESC');
		}
		
		// save the new daily report
		$this->DailyReport->data = $data;
		if(!$this->DailyReport->save($this->data))
		{
			return false;
		}
		$this->DailyReportData = $this->DailyReport->read();
		
		// add report items
		foreach($data['report_items'] as $item_state => $report_items)
		{
			$this->addReportItems($this->DailyReport->id, $user_id, $report_items, $daily_report_import_id, $item_state);
		}
		
		// import favorites
		if($user_id and isset($data[$this->DailyReport->alias]['favorites']) and $data[$this->DailyReport->alias]['favorites'])
		{
			$favoriteIds = $this->ReportItem->ReportItemFavorite->getMatchedFavorites($user_id, $favDate, 'list');
			if($favoriteIds)
			{
				$favoriteData = [
					'model' => $this->alias,
					'ids' => [
						'user_id' => $user_id,
						'daily_report_id' => $this->DailyReport->id,
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
		
		if(!isset($data[0]['daily_report_id']))
		{
			$this->modelError = __('Unknown %s (%s)', __('Daily Report'), 1);
			return false;
		}
		
		if(!$daily_report_id = $data[0]['daily_report_id'])
		{
			$this->modelError = __('Unknown %s (%s)', __('Daily Report'), 2);
			return false;
		}
		
		$this->DailyReport->id = $daily_report_id;
		$item_date = $this->DailyReport->field('report_date');
		
		/// set the item_date
		foreach($data as $i => $item)
		{
			$data[$i]['item_date'] = $item_date;
		}
		
		if($this->ReportItem->saveMany($data))
		{
			// create the xref for the items and this report
			$report_item_ids = $this->ReportItem->saveManyIds;
			// create the xref for the items and this report
			$report_xref_stuff = $this->ReportItem->saveManyXrefs;
			
			return $this->saveAssociations($daily_report_id, $report_item_ids, $report_xref_stuff);
		}
		return false;
	}
	
	public function addToReport($daily_report_id = false, $data = array())
	{
		if(!$daily_report_id)
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
		
		$this->DailyReportData = $this->DailyReport->read(null, $daily_report_id);
		
		foreach($data['report_items'] as $item_state => $report_items)
		{
			$this->addReportItems($daily_report_id, $user_id, $report_items, false, $item_state);
		}
		return true;
	}
	
	public function addReportItems($daily_report_id = false, $user_id = false, $this_items = array(), $daily_report_import_id = false, $item_state = false)
	{
		$items = array();
		
		// set some defaults. this date is used in the weeklys, and useful for the management reports
		$item_date = date('Y-m-d H:i:s');
		if(isset($this->DailyReportData['DailyReport']['report_date']))
		{
			$item_date = $this->DailyReportData['DailyReport']['report_date'];
		}
		
		$report_item_schema = $this->ReportItem->schema();
		$report_item_fields = array_keys($report_item_schema);
		$remove_fields = array('id', 'created', 'modified');
		foreach($report_item_fields as $i => $report_item_field)
		{
			if(in_array($report_item_field, $remove_fields)) unset($report_item_fields[$i]);
		}
		
		// import the items from the last daily report before this reports' report_date
		if($daily_report_import_id
		and $user_id
		and $daily_report_import_id)
		{
			$last_conditions = array(
				$this->alias. '.daily_report_id' => $daily_report_import_id,
			);
			
			$last_items = array();
			$get_last = false;
			
			if($item_state === false)
			{
				$last_conditions[$this->alias. '.item_state !='] = 1;
				$get_last = true;
			}
			elseif($item_state === 1)
			{
			}
			else
			{
				$last_conditions[$this->alias. '.item_state'] = $item_state;
				$get_last = true;
			}
			
			if($get_last)
			{
				$last_items = $this->find('all', array(
					'recursive' => 0,
					'contain' => array('ReportItem'),
					'conditions' => $last_conditions,
				));
			}
			
			// try to preserve the settings for an item from the last report
			foreach($last_items as $last_item)
			{
				$item = array();
				foreach($report_item_fields as $i => $report_item_field)
				{
					if(isset($last_item['ReportItem'][$report_item_field]))
					{
						$item[$report_item_field] = $last_item['ReportItem'][$report_item_field];
					}
				}
				$item['user_id'] = $user_id;
				$item['report_item_id'] = $last_item['ReportItem']['id'];
				$item['item_state'] = $last_item[$this->alias]['item_state'];
				$item['item_order'] = $last_item[$this->alias]['item_order'];
				
				// since it's being imported as a new item, update the item's date
				$item['item_date'] = $item_date;
				$items[] = $item;
			};
		}
		
		// process the items from the batch add field
		if(isset($this_items))
		{
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
					'item_date' => $item_date,
				);
			}
		}
		
		if(!count($items))
		{
			return true;
		}
		
		// save all of the items
		if($this->ReportItem->saveMany($items))
		{
			// create the xref for the items and this report
			$report_item_ids = $this->ReportItem->saveManyIds;
			// create the xref for the items and this report
			$report_xref_stuff = $this->ReportItem->saveManyXrefs;
			
			if($report_item_ids)
			{
				$xref_data = array();
				$item_order = 0;
				
				// if completed, make sure we're adding to the bottom of the list
				if($item_state === 1)
				{
					// find out how many are in there, and make the order that count, the order will be updated below
					if($item_order = $this->find('count', array(
						'conditions' => array(
							$this->alias. '.daily_report_id' => $daily_report_id,
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
				
				
				foreach($report_item_ids as $report_item => $report_item_id)
				{
					$xref_data[$report_item_id] = array(
						'user_id' => $user_id,
						'item_order' => $item_order,
					);
					
					// import settings from an imported report item
					if(isset($report_xref_stuff[$report_item_id]))
					{
						if(isset($report_xref_stuff[$report_item_id]['item_order']))
							$xref_data[$report_item_id]['item_order'] = $report_xref_stuff[$report_item_id]['item_order'];
							
						if(isset($report_xref_stuff[$report_item_id]['item_state']))
							$xref_data[$report_item_id]['item_state'] = $report_xref_stuff[$report_item_id]['item_state'];
					}
					$item_order++;
				}
				
				return $this->saveAssociations($daily_report_id, $report_item_ids, $xref_data);
			}
		}
	}
	
	public function saveAssociations($daily_report_id = false, $report_item_ids = array(), $xref_data = array())
	{
		$existing = $this->find('list', array(
			'recursive' => -1,
			'fields' => array('DailyReportsReportItem.id', 'DailyReportsReportItem.report_item_id'),
			'conditions' => array(
				'DailyReportsReportItem.daily_report_id' => $daily_report_id,
			),
		));
		
		// get just the new ones
		$report_item_ids = array_diff($report_item_ids, $existing);
		
		// build the proper save array
		$data = array();
		foreach($report_item_ids as $report_item => $report_item_id)
		{
			$data[$report_item] = array('daily_report_id' => $daily_report_id, 'report_item_id' => $report_item_id, 'active' => 1);
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
	
	public function updateItemsStateOrders($daily_report_id = false, $item_state = false, $xref_ids = array())
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
			$out = $this->getItemsForAjax($daily_report_id);
		}
		return $out;
	}
	
	public function deleteReportItems($daily_report_id = false, $xref_ids = array())
	{
		// we want to delete the report items, along with the xref records
		// also only delete the items in this daily report
		$report_item_ids = $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias.'.daily_report_id' => $daily_report_id,
				$this->alias.'.id' => $xref_ids,
			),
			'fields' => array($this->alias.'.id', $this->alias.'.report_item_id'),
		));
		
		
		if($daily_report_id and count($report_item_ids))
		{
			$this->deleteAll(array(
				$this->alias.'.daily_report_id' => $daily_report_id,
				$this->alias.'.id' => array_keys($report_item_ids),
			));
			
			/////////////////////// 
			/////// IF YOU CHANGE HOW YOU DELETE ITEMS FROM DELETEALL TO DELETE,
			/////// MAKE SURE YOU DON'T DELETE AN ITEM ASSOCIATED WITH OTHERS (WEEKLY, DAILY, MANAGEMENT)
			$this->ReportItem->deleteAll(array('ReportItem.id' => $report_item_ids));
		}
		
		$out = $this->getItemsForAjax($daily_report_id);
		return $out;
	}
	
	public function assignChargeCodeReportItems($daily_report_id = false, $charge_code_id, $xref_ids = array())
	{
		// get the list of report_items to update
		$report_item_ids = $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias.'.daily_report_id' => $daily_report_id,
				$this->alias.'.id' => $xref_ids,
			),
			'fields' => array($this->alias.'.id', $this->alias.'.report_item_id'),
		));
		
		// update the report_items wit the new charge_code_id
		if($daily_report_id and count($report_item_ids))
		{
			$this->ReportItem->updateAll(
    			array('ReportItem.charge_code_id' => $charge_code_id),
    			array('ReportItem.id' => $report_item_ids)
			);
		}
		
		$out = $this->getItemsForAjax($daily_report_id);
		return $out;
	}
	
	public function assignActivityReportItems($daily_report_id = false, $activity_id, $xref_ids = array())
	{
		// get the list of report_items to update
		$report_item_ids = $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias.'.daily_report_id' => $daily_report_id,
				$this->alias.'.id' => $xref_ids,
			),
			'fields' => array($this->alias.'.id', $this->alias.'.report_item_id'),
		));
		
		// update the report_items wit the new charge_code_id
		if($daily_report_id and count($report_item_ids))
		{
			$this->ReportItem->updateAll(
    			array('ReportItem.activity_id' => $activity_id),
    			array('ReportItem.id' => $report_item_ids)
			);
		}
		
		$out = $this->getItemsForAjax($daily_report_id);
		return $out;
	}
	
	public function updateDates($daily_report_id = false, $daily_report_date = false)
	{
		if(!$daily_report_id) return false;
		if(!$daily_report_date) return false;
		
		if($report_item_ids = $this->find('list', array(
			'conditions' => array(
				$this->alias.'.daily_report_id' => $daily_report_id,
			),
			'fields' => array(
				$this->alias.'.report_item_id', 
				$this->alias.'.report_item_id'
			),
		)))
		{
			$this->ReportItem->updateAll(
				array($this->ReportItem->alias.'.item_date' => $this->getDataSource()->value($daily_report_date, 'string')),
				array($this->ReportItem->alias.'.id' => $report_item_ids)
			);
		}
	}
	
	public function getItemsForAjax($daily_report_id = false)
	{
		$out = array();
		if(!$daily_report_id)
		{
			return $out;
		}
		
		// return a list of the item_state counts
		// these will be used to update the details page counts
		$items = $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias.'.daily_report_id' => $daily_report_id,
			),
			'fields' => array(
				$this->alias.'.id', 
				$this->alias.'.item_state'
			),
		));
		
		$out['all'] = count($items);
		foreach($items as $item_id => $item_state)
		{
			if(!isset($out[$item_state])) $out[$item_state] = 0;
			$out[$item_state]++;
		}
		$out['item_ids'] = array_keys($items);
		
		return $out;
	}
	
	public function getItemStates($index = false)
	{
		if($index === false) return $this->item_states;
		if(isset($this->item_states[$index])) return $this->item_states[$index];
		return false;
	}
}