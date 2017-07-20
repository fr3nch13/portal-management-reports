<?php
App::uses('AppController', 'Controller');
class WeeklyReportsReportItemsController extends AppController 
{
	public function index($item_state = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'ReportItem.user_id' => AuthComponent::user('id'),
			'WeeklyReport.id >' => 0,
		);
		
		if($item_state !== false)
		{
			$conditions['WeeklyReportsReportItem.item_state'] = $item_state;
		}
		
		$order = array();
		$order['WeeklyReportsReportItem.item_date'] = 'desc';
		$order['WeeklyReportsReportItem.item_state'] = 'asc';
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['conditions'] = $this->WeeklyReportsReportItem->conditions($conditions, $this->passedArgs);
		
		$weeklyreports_reportitems = $this->paginate();
		$this->set('weeklyreports_reportitems', $weeklyreports_reportitems);
		$this->set('item_state', $item_state);
		$this->set('item_states', $this->WeeklyReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->WeeklyReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->WeeklyReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function highlighted() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'ReportItem.user_id' => AuthComponent::user('id'),
			'WeeklyReportsReportItem.highlighted' => true,
			'WeeklyReport.id >' => 0,
		);
		
		$order = array();
		$order['ReportItem.item_date'] = 'desc';
		$order['WeeklyReportsReportItem.item_state'] = 'asc';
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['conditions'] = $this->WeeklyReportsReportItem->conditions($conditions, $this->passedArgs);
		
		$weeklyreports_reportitems = $this->paginate();
		$this->set('weeklyreports_reportitems', $weeklyreports_reportitems);
		$this->set('item_states', $this->WeeklyReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->WeeklyReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->WeeklyReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function weekly_report($weekly_report_id = false, $item_state = false) 
	{
		$this->Prg->commonProcess();
		
		if (!$weekly_report = $this->WeeklyReportsReportItem->WeeklyReport->read(null, $weekly_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Weekly Report')));
		}
		
		$this->set('weekly_report', $weekly_report);
		
		$conditions = array(
			'WeeklyReportsReportItem.weekly_report_id' => $weekly_report_id,
		);
		
		if($item_state !== false)
		{
			$conditions['WeeklyReportsReportItem.item_state'] = $item_state;
		}
		
		$this->paginate['order'] = array(
			'WeeklyReportsReportItem.item_state' => 'desc', 
			'WeeklyReportsReportItem.item_order' => 'asc',
		);
		$this->paginate['conditions'] = $this->WeeklyReportsReportItem->conditions($conditions, $this->passedArgs);
		
		if(isset($this->request->params['named']['getcount']) and $this->request->params['named']['getcount'])
		{
			$weeklyreports_reportitems = $this->paginate();
		}
		else
		{
			$this->paginate['recursive'] = 0;
			$this->paginate['contain'] = array('ReportItem');
			if(isset($this->paginate['limit'])) unset($this->paginate['limit']);
			$weeklyreports_reportitems = $this->WeeklyReportsReportItem->find('all', $this->paginate);
		}
		
		$this->set('weeklyreports_reportitems', $weeklyreports_reportitems);
		$this->set('item_states', $this->WeeklyReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->WeeklyReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->WeeklyReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function weekly_report_table($weekly_report_id = false, $item_state = false) 
	{
		$this->Prg->commonProcess();
		
		if (!$weekly_report = $this->WeeklyReportsReportItem->WeeklyReport->read(null, $weekly_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Weekly Report')));
		}
		
		$this->set('weekly_report', $weekly_report);
		
		$conditions = array(
			'WeeklyReportsReportItem.weekly_report_id' => $weekly_report_id,
		);
		$order = array(
			'ReportItem.item_date' => 'ASC',
		);
		
		if($item_state !== false)
		{
			$conditions['WeeklyReportsReportItem.item_state'] = $item_state;
		}
		else
		{
			$order['WeeklyReportsReportItem.item_state'] = 'asc';
		}
		
		$order['WeeklyReportsReportItem.item_order'] = 'asc';
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['conditions'] = $this->WeeklyReportsReportItem->conditions($conditions, $this->passedArgs);
		
		$weeklyreports_reportitems = $this->paginate();
		$this->set('weeklyreports_reportitems', $weeklyreports_reportitems);
		$this->set('item_states', $this->WeeklyReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->WeeklyReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->WeeklyReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function weekly_report_highlighted($weekly_report_id = false) 
	{
		$this->Prg->commonProcess();
		
		if (!$weekly_report = $this->WeeklyReportsReportItem->WeeklyReport->read(null, $weekly_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Weekly Report')));
		}
		
		$this->set('weekly_report', $weekly_report);
		
		$conditions = array(
			'WeeklyReportsReportItem.weekly_report_id' => $weekly_report_id,
			'WeeklyReportsReportItem.highlighted' => true,
		);
		$order = array();
		
		$order['WeeklyReportsReportItem.item_state'] = 'asc';
		$order['WeeklyReportsReportItem.item_order'] = 'asc';
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['conditions'] = $this->WeeklyReportsReportItem->conditions($conditions, $this->passedArgs);
		
		$weeklyreports_reportitems = $this->paginate();
		$this->set('weeklyreports_reportitems', $weeklyreports_reportitems);
		$this->set('item_states', $this->WeeklyReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->WeeklyReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->WeeklyReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function update_order($weekly_report_id = false)
	{
		$results = array();
		$this->set('results', $results);
		$this->layout = 'ajax_nodebug';
		
		if ($this->request->is('post') and $this->request->isAjax())
		{
			// make sure we have everything we need
			$item_state = false;
			$item_ids = array();
			
			if(!isset($this->request->data['state']))
			{
				throw new NotFoundException(__('Unknown %s %s', __('Report Item'), __('State')));
			}
			$item_state = $this->request->data['state'];
			
			if(!isset($this->request->data['items']))
			{
				throw new NotFoundException(__('Unknown %s', __('Report Items')));
			}
			$items = $this->request->data['items'];
			
			// in case we have a url encoded list
			if(is_string($items))
			{
				parse_str(html_entity_decode($items), $items);
				$items = $items['item'];
				foreach($items as $order => $item_id)
				{
					if(!trim($item_id)) unset($items[$order]);
				}
			}
			
			// all items are removed from this state, 
			// another ajax will add the items to another state
			// we're cone here
			if(!count($items))
			{
				return;
			}
			
			// verify the dail report exists
			if (!$weekly_report = $this->WeeklyReportsReportItem->WeeklyReport->read(null, $weekly_report_id))
			{
				throw new NotFoundException(__('Invalid %s', __('Weekly Report')));
			}
		
			$this->set('weekly_report', $weekly_report);
			
			$results = $this->WeeklyReportsReportItem->updateItemsStateOrders($weekly_report_id, $item_state, $items);
		}
		
		$this->set('results', $results);
	}
	
	public function add($weekly_report_id = false)
	{
		if (!$weekly_report = $this->WeeklyReportsReportItem->WeeklyReport->read(null, $weekly_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Weekly Report')));
		}
		
		$this->set('weekly_report', $weekly_report);
		
		if ($this->request->is('post'))
		{
			$this->WeeklyReportsReportItem->create();
			$this->request->data['WeeklyReportsReportItem']['user_id'] = AuthComponent::user('id');
			
			if ($this->WeeklyReportsReportItem->addToReport($weekly_report_id, $this->request->data))
			{
				$this->Flash->success(__('The %s have been saved', __('Report Items')));
				return $this->redirect(array('controller' => 'weekly_reports', 'action' => 'view', $weekly_report_id));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Report Items')));
			}
		}
		$this->set('item_states', $this->WeeklyReportsReportItem->getItemStates());
	}
	
	public function edit($id = false)
	{
		$this->WeeklyReportsReportItem->recursive = 0;
		if (!$xref_item = $this->WeeklyReportsReportItem->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Report Item')));
		}
		
		$this->set('xref_item', $xref_item);
		
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->WeeklyReportsReportItem->saveAssociated($this->request->data))
			{
				$this->Flash->success(__('The %s has been updated', __('Report Item')));
				return $this->redirect(array('controller' => 'weekly_reports', 'action' => 'view', $xref_item['WeeklyReport']['id']));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Report Items')));
			}
		}
		else
		{
			$this->request->data = $xref_item;
		}
		
		$this->set('item_states', $this->WeeklyReportsReportItem->getItemStates());
		$this->set('charge_codes', $this->WeeklyReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('activities', $this->WeeklyReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function charge_code_assign($weekly_report_id = false)
	{
		$results = array();
		$this->set('results', $results);
		$this->layout = 'ajax_nodebug';
		
		if ($this->request->is('post') and $this->request->isAjax())
		{
			if(!$weekly_report_id)
			{
				throw new NotFoundException(__('Unknown %s', __('Weekly Report')));
			}
			
			if(!isset($this->request->data['charge_code_id']))
			{
				throw new NotFoundException(__('Unknown %s', __('Charge Code')));
			}
			
			$charge_code_id = $this->request->data['charge_code_id'];
			
			if(!isset($this->request->data['items']))
			{
				throw new NotFoundException(__('Unknown %s', __('Report Items')));
			}
			
			$items = $this->request->data['items'];
			
			$results = $this->WeeklyReportsReportItem->assignChargeCodeReportItems($weekly_report_id, $charge_code_id, $items);
		}
		
		$this->set('results', $results);
	}
	
	public function activity_assign($weekly_report_id = false)
	{
		$results = array();
		$this->set('results', $results);
		$this->layout = 'ajax_nodebug';
		
		if ($this->request->is('post') and $this->request->isAjax())
		{
			if(!$weekly_report_id)
			{
				throw new NotFoundException(__('Unknown %s', __('Weekly Report')));
			}
			
			if(!isset($this->request->data['activity_id']))
			{
				throw new NotFoundException(__('Unknown %s', __('activity')));
			}
			
			$activity_id = $this->request->data['activity_id'];
			
			if(!isset($this->request->data['items']))
			{
				throw new NotFoundException(__('Unknown %s', __('Report Items')));
			}
			
			$items = $this->request->data['items'];
			
			$results = $this->WeeklyReportsReportItem->assignActivityReportItems($weekly_report_id, $activity_id, $items);
		}
		
		$this->set('results', $results);
	}
	
	public function highlight_assign($weekly_report_id = false, $highlighted = true)
	{
		$results = array();
		$this->set('results', $results);
		$this->layout = 'ajax_nodebug';
		
		if ($this->request->is('post') and $this->request->isAjax())
		{
			if(!$weekly_report_id)
			{
				throw new NotFoundException(__('Unknown %s', __('Weekly Report')));
			}
			
			if(!isset($this->request->data['items']))
			{
				throw new NotFoundException(__('Unknown %s', __('Report Items')));
			}
			
			$items = $this->request->data['items'];
			
			$results = $this->WeeklyReportsReportItem->assignHighlightReportItems($weekly_report_id, $highlighted, $items);
		}
		
		$this->set('results', $results);
	}
	
	public function gridadd()
	{
		// to mark dates as manually set
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if(isset($this->request->data['ReportItem']['item_date']))
			{
				$this->request->data['ReportItem']['item_date_set'] = true;
			}
			
			// make sure to track the user id
			if(isset($this->request->data['WeeklyReportsReportItem']['weekly_report_id']))
			{
				$this->WeeklyReportsReportItem->WeeklyReport->id = $this->request->data['WeeklyReportsReportItem']['weekly_report_id'];
				$user_id = $this->WeeklyReportsReportItem->WeeklyReport->field('user_id');
				
				if(!isset($this->request->data['WeeklyReportsReportItem']['user_id']))
					$this->request->data['WeeklyReportsReportItem']['user_id'] = $user_id;
				
				if(!isset($this->request->data['ReportItem']['user_id']))
					$this->request->data['ReportItem']['user_id'] = $user_id;
			}
		}
		
		return parent::gridadd();
	}
	
	public function multiselect()
	{
		if(!$this->request->is('post'))
		{
			throw new MethodNotAllowedException();
		}
		
		// forward to a page where the user can choose a value
		$redirect = false;
		if(isset($this->request->data['multiple']))
		{
			$ids = array();
			foreach($this->request->data['multiple'] as $id => $selected) { if($selected) $ids[] = $id; }
			$this->request->data['multiple'] = $this->WeeklyReportsReportItem->find('list', array(
				'fields' => array('WeeklyReportsReportItem.id', 'WeeklyReportsReportItem.report_item_id'),
				'conditions' => array('WeeklyReportsReportItem.id' => $ids),
				'recursive' => -1,
			));
		}
		
		if($this->request->data['WeeklyReportsReportItem']['multiselect_option'] == 'charge_code')
		{
			$redirect = array('action' => 'multiselect_charge_code');
		}
		elseif($this->request->data['WeeklyReportsReportItem']['multiselect_option'] == 'multicharge_code')
		{
			$redirect = array('action' => 'multiselect_multicharge_code');
		}
		elseif($this->request->data['WeeklyReportsReportItem']['multiselect_option'] == 'activity')
		{
			$redirect = array('action' => 'multiselect_activity');
		}
		elseif($this->request->data['WeeklyReportsReportItem']['multiselect_option'] == 'multiactivity')
		{
			$redirect = array('action' => 'multiselect_multiactivity');
		}
		elseif($this->request->data['WeeklyReportsReportItem']['multiselect_option'] == 'item_date')
		{
			$redirect = array('action' => 'multiselect_item_date');
		}
		elseif($this->request->data['WeeklyReportsReportItem']['multiselect_option'] == 'delete')
		{
			$this->WeeklyReportsReportItem->deleteAll(array(
				$this->WeeklyReportsReportItem->alias.'.id' => array_keys($this->request->data['multiple']),
			));
			
			$this->WeeklyReportsReportItem->ReportItem->deleteAll(array('ReportItem.id' => $this->request->data['multiple']));	
			$this->bypassReferer = true;
			return $this->redirect($this->referer());
		}
		
		if($redirect)
		{
			Cache::write('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			$this->bypassReferer = true;
			return $this->redirect($redirect);
		}
		
		if($this->WeeklyReportsReportItem->multiselect($this->request->data))
		{
			$this->Flash->success(__('The %s were updated for this %s.', __('Report Items'), __('Weekly Report')));
			return $this->redirect($this->referer());
		}
		
		$this->Flash->error(__('The %s were NOT updated for this %s.', __('Report Items'), __('Weekly Report')));
		$this->redirect($this->referer());
	}
	
//
	public function multiselect_charge_code()
	{
		$sessionData = Cache::read('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['ReportItem']['charge_code_id'])?$this->request->data['ReportItem']['charge_code_id']:0);
			if($multiselect_value)
			{
				if($this->WeeklyReportsReportItem->multiselect_items($sessionData, $this->request->data)) 
				{
					Cache::delete('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
					$this->Flash->success(__('The %s were updated for this %s.', __('Report Items'), __('Weekly Report')));
					return $this->redirect($this->WeeklyReportsReportItem->multiselectReferer());
				}
				else
				{
					$this->Flash->error(__('The %s were NOT updated for this %s.', __('Report Items'), __('Weekly Report')));
				}
			}
			else
			{
				$this->Flash->error(__('Please select a %s', __('Charge Code')));
			}
		}
		
		$selected_items = array();
		if(isset($sessionData['multiple']))
		{
			$selected_items = $this->WeeklyReportsReportItem->ReportItem->find('list', array(
				'conditions' => array(
					'ReportItem.id' => $sessionData['multiple'],
				),
				'fields' => array('ReportItem.id', 'ReportItem.item'),
				'sort' => array('ReportItem.item' => 'asc'),
			));
		}
		
		$this->set('selected_items', $selected_items);
		
		// get the object types
		$this->set('charge_codes', $this->WeeklyReportsReportItem->ReportItem->ChargeCode->listForSortable());
	}
	
	public function multiselect_multicharge_code()
	{
		$sessionData = Cache::read('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->WeeklyReportsReportItem->multiselect_items_multiple($sessionData, $this->request->data['ReportItem'])) 
			{
				Cache::delete('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
				$this->Flash->success(__('The %s were updated for this %s.', __('Report Items'), __('Weekly Report')));
				return $this->redirect($this->WeeklyReportsReportItem->multiselectReferer());
			}
			else
			{
				$this->Flash->error(__('The %s were NOT updated for this %s.', __('Report Items'), __('Weekly Report')));
			}
		}

		$this->Prg->commonProcess();
		
		$ids = array();
		if(isset($sessionData['multiple']))
		{
			$ids = $sessionData['multiple'];
		}
		
		$conditions = array('WeeklyReportsReportItem.id' => array_keys($ids));
		$this->WeeklyReportsReportItem->recursive = 0;
		$this->paginate['contain'] = array('ReportItem');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('WeeklyReportsReportItem.created' => 'desc');
		$this->paginate['conditions'] = $this->WeeklyReportsReportItem->conditions($conditions, $this->passedArgs);
		$this->set('weeklyreports_reportitems', $this->paginate());
		
		// get the object types
		$this->set('charge_codes', $this->WeeklyReportsReportItem->ReportItem->ChargeCode->listForSortable());
	}
	
	public function multiselect_activity()
	{
		$sessionData = Cache::read('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['ReportItem']['activity_id'])?$this->request->data['ReportItem']['activity_id']:0);
			if($multiselect_value)
			{
				if($this->WeeklyReportsReportItem->multiselect_items($sessionData, $this->request->data)) 
				{
					Cache::delete('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
					$this->Flash->success(__('The %s were updated for this %s.', __('Report Items'), __('Weekly Report')));
					return $this->redirect($this->WeeklyReportsReportItem->multiselectReferer());
				}
				else
				{
					$this->Flash->error(__('The %s were NOT updated for this %s.', __('Report Items'), __('Weekly Report')));
				}
			}
			else
			{
				$this->Flash->error(__('Please select a %s', __('Charge Code')));
			}
		}
		
		$selected_items = array();
		if(isset($sessionData['multiple']))
		{
			$selected_items = $this->WeeklyReportsReportItem->ReportItem->find('list', array(
				'conditions' => array(
					'ReportItem.id' => $sessionData['multiple'],
				),
				'fields' => array('ReportItem.id', 'ReportItem.item'),
				'sort' => array('ReportItem.item' => 'asc'),
			));
		}
		
		$this->set('selected_items', $selected_items);
		
		// get the object types
		$this->set('activities', $this->WeeklyReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function multiselect_multiactivity()
	{
		$sessionData = Cache::read('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->WeeklyReportsReportItem->multiselect_items_multiple($sessionData, $this->request->data['ReportItem'])) 
			{
				Cache::delete('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
				$this->Flash->success(__('The %s were updated for this %s.', __('Report Items'), __('Weekly Report')));
				return $this->redirect($this->WeeklyReportsReportItem->multiselectReferer());
			}
			else
			{
				$this->Flash->error(__('The %s were NOT updated for this %s.', __('Vectors'), __('Report')));
			}
		}

		$this->Prg->commonProcess();
		
		$ids = array();
		if(isset($sessionData['multiple']))
		{
			$ids = $sessionData['multiple'];
		}
		
		$conditions = array('WeeklyReportsReportItem.id' => array_keys($ids));
		$this->WeeklyReportsReportItem->recursive = 0;
		$this->paginate['contain'] = array('ReportItem');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('WeeklyReportsReportItem.created' => 'desc');
		$this->paginate['conditions'] = $this->WeeklyReportsReportItem->conditions($conditions, $this->passedArgs);
		$this->set('weeklyreports_reportitems', $this->paginate());
		
		// get the object types
		$this->set('activities', $this->WeeklyReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function multiselect_item_date()
	{
		$sessionData = Cache::read('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if(isset($this->request->data['ReportItem']['item_date']))
				$this->request->data['ReportItem']['item_date'] = date('Y-m-d H:i:s', strtotime($this->request->data['ReportItem']['item_date']));
			
			if($this->WeeklyReportsReportItem->multiselect_items($sessionData, $this->request->data)) 
			{
				Cache::delete('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
				$this->Flash->success(__('The %s were updated for this %s.', __('Report Items'), __('Weekly Report')));
				return $this->redirect($this->WeeklyReportsReportItem->multiselectReferer());
			}
			else
			{
				$this->Flash->error(__('The %s were NOT updated for this %s.', __('Report Items'), __('Weekly Report')));
			}
		}
		
		$selected_items = array();
		if(isset($sessionData['multiple']))
		{
			$selected_items = $this->WeeklyReportsReportItem->ReportItem->find('list', array(
				'conditions' => array(
					'ReportItem.id' => $sessionData['multiple'],
				),
				'fields' => array('ReportItem.id', 'ReportItem.item'),
				'sort' => array('ReportItem.item' => 'asc'),
			));
		}
		
		$this->set('selected_items', $selected_items);
	}
	
	public function delete($weekly_report_id = false)
	{
		$results = array();
		$this->set('results', $results);
		$this->layout = 'ajax_nodebug';
		
		if ($this->request->is('post') and $this->request->isAjax())
		{
			if(!$weekly_report_id)
			{
				throw new NotFoundException(__('Unknown %s', __('Weekly Report')));
			}
			
			if(!isset($this->request->data['items']))
			{
				throw new NotFoundException(__('Unknown %s', __('Report Items')));
			}
			
			$items = $this->request->data['items'];
			
			/////////////////////// 
			/////// IF YOU CHANGE HOW YOU DELETE ITEMS FROM DELETEALL TO DELETE,
			/////// MAKE SURE YOU DON'T DELETE AN ITEM ASSOCIATED WITH OTHERS (WEEKLY, DAILY, MANAGEMENT)
			$results = $this->WeeklyReportsReportItem->deleteReportItems($weekly_report_id, $items);
		}
		
		$this->set('results', $results);
	}
	
	public function griddelete()
	{
		if(!$this->request->is('ajax'))
		{
			throw new InternalErrorException(__('Request in not an Ajax request.'));
		}
		
		if(!isset($this->request->data['WeeklyReportsReportItem']['id']))
		{
			throw new InternalErrorException(__('Unknown %s Id.', __('Report Item')));
		}
		
		$this->WeeklyReportsReportItem->id = $this->request->data['WeeklyReportsReportItem']['id'];
		if(!$weekly_report_id = $this->WeeklyReportsReportItem->field('weekly_report_id'))
		{
			throw new InternalErrorException(__('Unknown %s Id.', __('Weekly Report')));
		}
		
		$results = false;
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			$results = $this->WeeklyReportsReportItem->deleteReportItems($weekly_report_id, array($this->WeeklyReportsReportItem->id => $this->WeeklyReportsReportItem->id));
		}
		
		$this->set('results', $results);
		return $this->render('Utilities./Elements/gridedit', 'ajax_nodebug');
	}
	
	public function reviewer_griddelete()
	{
		return $this->griddelete();
	}
	
	public function manager_griddelete()
	{
		return $this->griddelete();
	}
	
	public function admin_griddelete()
	{
		return $this->griddelete();
	}
	
	public function manager_weekly_report_table($weekly_report_id = false, $item_state = false) 
	{
		$this->Prg->commonProcess();
		
		if (!$weekly_report = $this->WeeklyReportsReportItem->WeeklyReport->read(null, $weekly_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Weekly Report')));
		}
		
		$this->set('weekly_report', $weekly_report);
		
		$conditions = array(
			'WeeklyReportsReportItem.weekly_report_id' => $weekly_report_id,
		);
		$order = array();
		
		if($item_state !== false)
		{
			$conditions['WeeklyReportsReportItem.item_state'] = $item_state;
		}
		else
		{
			$order['WeeklyReportsReportItem.item_state'] = 'asc';
		}
		
		$order['WeeklyReportsReportItem.item_order'] = 'asc';
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['conditions'] = $this->WeeklyReportsReportItem->conditions($conditions, $this->passedArgs);
		
		$weeklyreports_reportitems = $this->paginate();
		$this->set('weeklyreports_reportitems', $weeklyreports_reportitems);
		$this->set('item_states', $this->WeeklyReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->WeeklyReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->WeeklyReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function manager_weekly_report_highlighted($weekly_report_id = false) 
	{
		$this->Prg->commonProcess();
		
		if (!$weekly_report = $this->WeeklyReportsReportItem->WeeklyReport->read(null, $weekly_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Weekly Report')));
		}
		
		$this->set('weekly_report', $weekly_report);
		
		$conditions = array(
			'WeeklyReportsReportItem.weekly_report_id' => $weekly_report_id,
			'WeeklyReportsReportItem.highlighted' => true,
		);
		$order = array();
		
		$order['WeeklyReportsReportItem.item_state'] = 'asc';
		$order['WeeklyReportsReportItem.item_order'] = 'asc';
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['conditions'] = $this->WeeklyReportsReportItem->conditions($conditions, $this->passedArgs);
		
		$weeklyreports_reportitems = $this->paginate();
		$this->set('weeklyreports_reportitems', $weeklyreports_reportitems);
		$this->set('item_states', $this->WeeklyReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->WeeklyReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->WeeklyReportsReportItem->ReportItem->Activity->listForSortable());
	}
}