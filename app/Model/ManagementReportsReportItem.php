<?php
App::uses('AppModel', 'Model');

class ManagementReportsReportItem extends AppModel 
{
	public $belongsTo = array(
		'ManagementReport' => array(
			'className' => 'ManagementReport',
			'foreignKey' => 'management_report_id',
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
		'Utilities.Email',
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'ReportItem.item',
	);
	
	public $item_states = array();
	
	public $weeklyReportUserIds = array();
	
	
	public function __construct($id = false, $table = null, $ds = null)
	{
		// the item_states above are listed for defaults
		// their names can be set to something else in the app config
		// this uses the app config names and overwrites the above names
		// we're using the weekly item states as these items come from there
		if($item_states = Configure::read('WeeklyReport.item_states'))
		{
			$this->item_states = $this->ReportItem->WeeklyReportsReportItem->item_states;
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
			if(!isset($this->data[$this->alias]['item_order']) and isset($this->data[$this->alias]['management_report_id']))
			{
				if(!$item_order = $this->find('count', array(
					'conditions' => array(
						$this->alias.'.management_report_id' => $this->data[$this->alias]['management_report_id']
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
						$weeklyReport = $this->ReportItem->WeeklyReport->read(null, $this->data[$this->alias]['weekly_report_id']);
						$user_id = $this->weeklyReportUserIds[$this->data[$this->alias]['weekly_report_id']] = $weeklyReport['WeeklyReport']['user_id'];
					}
				}
				
				$this->data[$this->alias]['user_id'] = $user_id;
			}
		}
		return parent::beforeSave($options);
	}
	
	public function addToReport($management_report_id = false, $data = array())
	{
		if(!$management_report_id)
		{
			return false;
		}
		
		if(!isset($data[$this->alias]['report_items']))
		{
			return false;
		}
		
		if(!isset($data[$this->alias]['user_id']))
		{
			return false;
		}
		$user_id = $data[$this->alias]['user_id'];
		
		if(is_string($data[$this->alias]['report_items']))
		{
			$data[$this->alias]['report_items'] = trim($data[$this->alias]['report_items']);
			$data[$this->alias]['report_items'] = explode("\n", $data[$this->alias]['report_items']);
		}
		
		$item_state = 1;
		if(isset($data[$this->alias]['item_state']))
		{
			$item_state = $data[$this->alias]['item_state'];
		}
		
		$activity_id = 0;
		if(isset($data[$this->alias]['activity_id']))
		{
			$activity_id = $data[$this->alias]['activity_id'];
		}
		
		$charge_code_id = 0;
		if(isset($data[$this->alias]['charge_code_id']))
		{
			$charge_code_id = $data[$this->alias]['charge_code_id'];
		}
		
		$item_order = 0;
		// find out how many are in there, and make the order that count, the order will be updated below
		if($item_order = $this->find('count', array(
			'conditions' => array(
				$this->alias. '.management_report_id' => $management_report_id,
				$this->alias. '.item_state' => $data[$this->alias]['item_state'],
			),
		)))
		{
			$item_order++;
		}
		else
		{
			$item_order = 0;
		}
		
		$report_item_schema = $this->ReportItem->schema();
		$report_item_fields = array_keys($report_item_schema);
		$remove_fields = array('id', 'created', 'modified');
		foreach($report_item_fields as $i => $report_item_field)
		{
			if(in_array($report_item_field, $remove_fields)) unset($report_item_fields[$i]);
		}
				
		$xref_schema = $this->schema();
		$xref_fields = array_keys($xref_schema);
		$remove_fields = array('id', 'created', 'modified');
		foreach($xref_fields as $i => $xref_field)
		{
			if(in_array($xref_field, $remove_fields)) unset($xref_fields[$i]);
		}
		
		$items = array();
		// clean up the items in the list
		foreach($data[$this->alias]['report_items'] as $i => $this_item)
		{
			$this_item = trim($this_item);
			if(!$this_item) { unset($this_items[$i]); continue;}
			
			$item = array();
			foreach($report_item_fields as $report_item_field)
			{
				$item[$report_item_field] = (isset($data[$this->alias][$report_item_field])?$data[$this->alias][$report_item_field]:false);
			}
			
			if(isset($item['item_date']))
			{
				$item['item_date'] = date('Y-m-d H:i:s', strtotime($item['item_date']));
			}
			
			$item['item'] = $this_item;
			
			$items[] = $item;
			
			$item_order++;
		}
		
		// save all of the report items
		$report_item_ids = array();
		$report_xref_stuff = array();
		foreach($items as $i => $item )
		{
					foreach($xref_fields as $xref_field)
					{
						$report_xref_stuff[$i][$xref_field] = (isset($data[$this->alias][$xref_field])?$data[$this->alias][$xref_field]:false);
					}
			$item_order++;
			$report_xref_stuff[$i]['item_order'] = $item_order;
			$report_xref_stuff[$i]['management_report_id'] = $management_report_id;
		}
		
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
					foreach($xref_fields as $xref_field)
					{
						$report_xref_stuff[$report_item_id][$xref_field] = (isset($data[$this->alias][$xref_field])?$data[$this->alias][$xref_field]:false);
					}
					$item_order++;
					$report_xref_stuff[$report_item_id]['item_order'] = $item_order;
					$report_xref_stuff[$report_item_id]['management_report_id'] = $management_report_id;
				}
			}
		}
			
		if(count($report_item_ids))
		{	
			return $this->saveAssociations($management_report_id, $report_item_ids, $report_xref_stuff);
		}
		
		return true;
	}
	
	public function favoritesAdd($data = array())
	{
		$items = array();
		$report_item_ids = array();
		$report_xref_stuff = array();
		
		if(!isset($data[0]['management_report_id']))
		{
			$this->modelError = __('Unknown %s (%s)', __('Management Report'), 1);
			return false;
		}
		
		if(!$management_report_id = $data[0]['management_report_id'])
		{
			$this->modelError = __('Unknown %s (%s)', __('Management Report'), 2);
			return false;
		}
		
		$this->ManagementReport->id = $management_report_id;
		$item_date = $this->ManagementReport->field('report_date_start');
		
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
			
			return $this->saveAssociations($management_report_id, $report_item_ids, $report_xref_stuff);
		}
		return false;
	}
	
	public function importFromSelectedWeeklys($management_report_id = false, $user_id = false, $weekly_report_ids = array(), $management_report_data = array())
	{
		if(!$management_report_id) return false;
		if(!$user_id) return false;
		if(!$weekly_report_ids) return false;
		if(!$management_report_data) return false;
		
		$report_date_start = $report_date_end = false;
		if(isset($management_report_data['report_date_start']))
			$report_date_start = $management_report_data['report_date_start'];
		if(isset($management_report_data['report_date_end']))
			$report_date_end = $management_report_data['report_date_end'];
		if(!$report_date_start or !$report_date_end)
			return false;
		
		$items = array();
		$report_item_ids = array();
		$report_xref_stuff = array();
		
		// order of the weekly report items
		$order = array(
			'WeeklyReport.report_date' => 'ASC',
			'WeeklyReport.user_id' => 'DESC',
			'WeeklyReportsReportItem.item_state' => 'ASC',
			'WeeklyReportsReportItem.item_order' => 'ASC',
		);
		
		/// grab all of the completed and/or highlighted items
		$conditions = array(
			'WeeklyReport.id' => $weekly_report_ids,
			'WeeklyReport.finalized' => true,
			'OR' => array(
				'WeeklyReportsReportItem.item_state' => 1,
				'WeeklyReportsReportItem.highlighted' => true,
			),
			'ReportItem.item_date BETWEEN ? AND ?' => array(
				date('Y-m-d 00:00:00', strtotime($report_date_start)),
				date('Y-m-d 23:59:59', strtotime($report_date_end)),
			),
		);
		
		if($weeklyReportItems = $this->ReportItem->WeeklyReportsReportItem->find('all', array(
			'recursive' => 0,
			'contain' => array('WeeklyReport', 'ReportItem'),
			'conditions' => $conditions,
			'order' => $order,
		)))
		{
			
			$item_order = 0;
			foreach($weeklyReportItems as $i => $weeklyReportItem)
			{
				$report_item_id = $weeklyReportItem['ReportItem']['id'];
				$report_item_ids[$report_item_id] = $report_item_id;
				$report_xref_stuff[$report_item_id] = array(
					'report_item_id' => $report_item_id,
					'weekly_report_id' => $weeklyReportItem['WeeklyReportsReportItem']['weekly_report_id'],
					'user_id' => ($weeklyReportItem['WeeklyReportsReportItem']['user_id']?$weeklyReportItem['WeeklyReportsReportItem']['user_id']:$weeklyReportItem['WeeklyReport']['user_id']),
					'item_state' => $weeklyReportItem['WeeklyReportsReportItem']['item_state'],
					'highlighted' => $weeklyReportItem['WeeklyReportsReportItem']['highlighted'],
					'item_order' => $item_order,
				);
				$item_order++;
			}
		}
		
		return $this->saveAssociations($management_report_id, $report_item_ids, $report_xref_stuff);
	}
	
	public function saveAssociations($management_report_id = false, $report_item_ids = array(), $xref_data = array())
	{
		$existing = $this->find('list', array(
			'recursive' => -1,
			'fields' => array('ManagementReportsReportItem.id', 'ManagementReportsReportItem.report_item_id'),
			'conditions' => array(
				'ManagementReportsReportItem.management_report_id' => $management_report_id,
			),
		));
		
		// get just the new ones
		$report_item_ids = array_diff($report_item_ids, $existing);
		
		// build the proper save array
		$data = array();
		foreach($report_item_ids as $report_item => $report_item_id)
		{
			$data[$report_item] = array('management_report_id' => $management_report_id, 'report_item_id' => $report_item_id, 'active' => 1);
			if(isset($xref_data[$report_item]))
			{
				$data[$report_item] = array_merge($xref_data[$report_item], $data[$report_item]);
			}
		}
		
		return $this->saveMany($data);
	}
	
	public function sendReviewEmails($management_report_id = false)
	{
		$this->modelError = false;
		if(!$management_report = $this->ManagementReport->read(null, $management_report_id))
		{
			$this->modelError = __('Unknown %s', __('Management Report'));
			return false;
		}
		
		
		/// find all of the report items that are related to this management report
		//  marked for review, and not marked for reviewed
		if(!$report_item_ids = $this->find('list', array(
			'recursive' => 0,
			'contain' => array('ReportItem'),
			'fields' => array(
				'ManagementReportsReportItem.id', 'ReportItem.id',
			),
			'conditions' => array(
				'ManagementReportsReportItem.management_report_id' => $management_report_id,
				'ReportItem.review' => true,
				'ReportItem.reviewed' => false,
			),
		)))
		{
			$this->modelError = __('No %s are Marked for Review', __('Report Items'));
			return false;
		}
		
		// get the report items
		if(!$report_items = $this->ReportItem->find('all', array(
			'recursive' => 0,
			'conditions' => array(
				'ReportItem.id' => $report_item_ids,
				'WeeklyReportsReportItem.weekly_report_id >' => 0,
			),
			'sort' => array(
				'ReportItem.user_id' => 'asc',
				'WeeklyReportsReportItem.weekly_report_id' => 'asc',
			),
		)))
		{
			$this->modelError = __('Unable to find marked %s associated with a %s', __('Report Items'), __('Weekly Report'));
			return false;
		}
		// sort them out by user
		
		$emails = array();
		foreach($report_items as $report_item)
		{
			$user_id = $report_item['User']['id'];
			
			// build the initial settings for each email
			if(!isset($emails[$user_id]))
			{
				$emails[$user_id] = array(
					'to' => $report_item['User']['email'],
					'count' => 0,
					'report_items' => array(),
				);
			}
			
			$emails[$user_id]['count']++;
			$emails[$user_id]['report_items'][] = $report_item;
		}
		
		$item_states = $this->getItemStates();
		$item_charge_codes = $this->ReportItem->ChargeCode->listForSortable();
		$item_activities = $this->ReportItem->Activity->listForSortable();
		$review_reasons = $this->ReportItem->ReviewReason->listForSortable();
		
		$full_results = array();
		foreach($emails as $user_id => $email)
		{
		 	// rebuild it to use the EmailBehavior from the Utilities Plugin
		 	$this->Email_reset();
		 	
		 	$this->Email_set('to', $email['to']);
			$this->Email_set('replyTo', $email['to']);
			$subject = __('%s need Review. Count: %s', __('Report Items'),  $email['count']);
			$this->Email_set('subject', $subject);
			$this->Email_set('viewVars', array(
				'report_items' => $email['report_items'],
				'item_states' => $item_states,
				'item_charge_code' => $item_charge_codes,
				'item_activities' => $item_activities,
				'review_reasons' => $review_reasons,
			));
			
			$this->Email_set('emailFormat', 'both');
			$this->Email_set('template', 'weekly_review_email');
			
			$results = $this->Email_executeFull();
			$full_results[$user_id] = $results;
		}
		return $full_results;
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
	
	public function updateItemsStateOrders($management_report_id = false, $item_state = false, $xref_ids = array())
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
			$out = $this->getItemsForAjax($management_report_id);
		}
		return $out;
	}
	
	public function deleteReportItems($management_report_id = false, $xref_ids = array())
	{
		// we want to delete the report items, along with the xref records
		// also only delete the items in this management report
		$report_item_ids = $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias.'.management_report_id' => $management_report_id,
				$this->alias.'.id' => $xref_ids,
			),
			'fields' => array($this->alias.'.id', $this->alias.'.report_item_id'),
		));
		
		
		if($management_report_id and count($report_item_ids))
		{
			$this->deleteAll(array(
				$this->alias.'.management_report_id' => $management_report_id,
				$this->alias.'.id' => array_keys($report_item_ids),
			));
			
			/////////////////////// 
			/////// IF YOU CHANGE HOW YOU DELETE ITEMS FROM DELETEALL TO DELETE,
			/////// MAKE SURE YOU DON'T DELETE AN ITEM ASSOCIATED WITH OTHERS (WEEKLY, DAILY, MANAGEMENT)
			$this->ReportItem->deleteAll(array('ReportItem.id' => $report_item_ids));
		}
		
		$out = $this->getItemsForAjax($management_report_id);
		return $out;
	}
	
	public function assignChargeCodeReportItems($management_report_id = false, $charge_code_id, $xref_ids = array())
	{
		// get the list of report_items to update
		$report_item_ids = $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias.'.management_report_id' => $management_report_id,
				$this->alias.'.id' => $xref_ids,
			),
			'fields' => array($this->alias.'.id', $this->alias.'.report_item_id'),
		));
		
		// update the report_items wit the new charge_code_id
		if($management_report_id and count($report_item_ids))
		{
			$this->ReportItem->updateAll(
    			array('ReportItem.charge_code_id' => $charge_code_id),
    			array('ReportItem.id' => $report_item_ids)
			);
		}
		
		$out = $this->getItemsForAjax($management_report_id);
		return $out;
	}
	
	public function assignActivityReportItems($management_report_id = false, $activity_id, $xref_ids = array())
	{
		// get the list of report_items to update
		$report_item_ids = $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias.'.management_report_id' => $management_report_id,
				$this->alias.'.id' => $xref_ids,
			),
			'fields' => array($this->alias.'.id', $this->alias.'.report_item_id'),
		));
		
		// update the report_items wit the new charge_code_id
		if($management_report_id and count($report_item_ids))
		{
			$this->ReportItem->updateAll(
    			array('ReportItem.activity_id' => $activity_id),
    			array('ReportItem.id' => $report_item_ids)
			);
		}
		
		$out = $this->getItemsForAjax($management_report_id);
		return $out;
	}
	
	public function assignHighlightReportItems($management_report_id = false, $highlighted = true, $xref_ids = array())
	{
		
		// verify these items belong to this management report
		$report_item_ids = $this->find('list', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias.'.management_report_id' => $management_report_id,
				$this->alias.'.id' => $xref_ids,
			),
			'fields' => array($this->alias.'.id', $this->alias.'.id'),
		));
		
		if($management_report_id and count($report_item_ids))
		{
			$this->updateAll(
				array($this->alias.'.highlighted' => ($highlighted?true:false)),
				array($this->alias.'.id' => $xref_ids)
			);
		}
		
		$out = $this->getItemsForAjax($management_report_id);
		return $out;
	}
	
	public function getItemsForAjax($management_report_id = false)
	{
		$out = array();
		if(!$management_report_id)
		{
			return $out;
		}
		
		// return a list of the item_state counts
		// these will be used to update the details page counts
		$items = $this->find('all', array(
			'recursive' => -1,
			'conditions' => array(
				$this->alias.'.management_report_id' => $management_report_id,
			),
			'fields' => array(
				$this->alias.'.id', 
				$this->alias.'.item_state',
				$this->alias.'.highlighted'
			),
		));
		
		$out['all'] = count($items);
		$out['highlighted'] = 0;
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
		$managementReportsReportItems = $this->find('all', array(
			'conditions' => array(
				'OR' => array(
					$this->alias.'.user_id' => 0,
					'ReportItem.user_id' => 0,
				),
			),
			'contain' => array('ReportItem', 'ReportItem.WeeklyReportsReportItem'),
		));
		$this->shellOut(__('Found %s that need to be fixed', count($managementReportsReportItems)));
		$check = 0;
		$check2 = 0;
		$saveMany_xref = array();
		$saveMany_reportItem = array();
		foreach($managementReportsReportItems as $managementReportsReportItem)
		{
			$xref_id = $managementReportsReportItem['ManagementReportsReportItem']['id'];
			$reportItem_id = $managementReportsReportItem['ReportItem']['id'];
			if($managementReportsReportItem['ReportItem']['user_id'] > 0)
			{
				if($managementReportsReportItem['ManagementReportsReportItem']['user_id'] == 0)
				{
					$saveMany_xref[$xref_id] = array(
						'id' => $xref_id,
						'user_id' => $managementReportsReportItem['ReportItem']['user_id'],
						'modified' => $managementReportsReportItem['ManagementReportsReportItem']['modified'],
					);
				}
			}
			elseif($managementReportsReportItem['ReportItem']['WeeklyReportsReportItem']['user_id'] > 0)
			{
				if($managementReportsReportItem['ManagementReportsReportItem']['user_id'] == 0)
				{
					$saveMany_xref[$xref_id] = array(
						'id' => $xref_id,
						'user_id' => $managementReportsReportItem['WeeklyReportsReportItem']['ReportItem']['user_id'],
						'modified' => $managementReportsReportItem['ManagementReportsReportItem']['modified'],
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
//			$this->ReportItem->saveMany($saveMany_reportItem);
		}
	}
}