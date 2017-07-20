<?php
App::uses('AppController', 'Controller');
class DailyReportsReportItemsController extends AppController 
{
	public function index($item_state = false) 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'ReportItem.user_id' => AuthComponent::user('id'),
			'DailyReport.id >' => 0,
		);
		
		if($item_state !== false)
		{
			$conditions['DailyReportsReportItem.item_state'] = $item_state;
		}
		
		$order = array();
		$order['DailyReportsReportItem.item_date'] = 'desc';
		$order['DailyReportsReportItem.item_state'] = 'asc';
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['conditions'] = $this->DailyReportsReportItem->conditions($conditions, $this->passedArgs);
		
		$dailyreports_reportitems = $this->paginate();
		$this->set('dailyreports_reportitems', $dailyreports_reportitems);
		$this->set('item_state', $item_state);
		$this->set('item_states', $this->DailyReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->DailyReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->DailyReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function daily_report($daily_report_id = false, $item_state = false) 
	{
		$this->Prg->commonProcess();
		
		if (!$daily_report = $this->DailyReportsReportItem->DailyReport->read(null, $daily_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Daily Report')));
		}
		
		$this->set('daily_report', $daily_report);
		
		$conditions = array(
			'DailyReportsReportItem.daily_report_id' => $daily_report_id,
		);
		
		if($item_state !== false)
		{
			$conditions['DailyReportsReportItem.item_state'] = $item_state;
		}
		
		$this->paginate['order'] = array(
			'DailyReportsReportItem.item_state' => 'desc', 
			'DailyReportsReportItem.item_order' => 'asc',
		);
		$this->paginate['conditions'] = $this->DailyReportsReportItem->conditions($conditions, $this->passedArgs);
		
		if(isset($this->request->params['named']['getcount']) and $this->request->params['named']['getcount'])
		{
			$dailyreports_reportitems = $this->paginate();
		}
		else
		{
			$this->paginate['recursive'] = 0;
			$this->paginate['contain'] = array('ReportItem');
			if(isset($this->paginate['limit'])) unset($this->paginate['limit']);
			$dailyreports_reportitems = $this->DailyReportsReportItem->find('all', $this->paginate);
		}
		
		$this->set('dailyreports_reportitems', $dailyreports_reportitems);
		$this->set('item_states', $this->DailyReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->DailyReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->DailyReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function daily_report_table($daily_report_id = false, $item_state = false) 
	{
		$this->Prg->commonProcess();
		
		if (!$daily_report = $this->DailyReportsReportItem->DailyReport->read(null, $daily_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Daily Report')));
		}
		
		$this->set('daily_report', $daily_report);
		
		$conditions = array(
			'DailyReportsReportItem.daily_report_id' => $daily_report_id,
		);
		$order = array();
		
		if($item_state !== false)
		{
			$conditions['DailyReportsReportItem.item_state'] = $item_state;
		}
		else
		{
			$order['DailyReportsReportItem.item_state'] = 'asc';
		}
		
		$order['DailyReportsReportItem.item_order'] = 'asc';
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['conditions'] = $this->DailyReportsReportItem->conditions($conditions, $this->passedArgs);
		
		$dailyreports_reportitems = $this->paginate();
		$this->set('dailyreports_reportitems', $dailyreports_reportitems);
		$this->set('item_states', $this->DailyReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->DailyReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->DailyReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function update_order($daily_report_id = false)
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
			if (!$daily_report = $this->DailyReportsReportItem->DailyReport->read(null, $daily_report_id))
			{
				throw new NotFoundException(__('Invalid %s', __('Daily Report')));
			}
		
			$this->set('daily_report', $daily_report);
			
			$results = $this->DailyReportsReportItem->updateItemsStateOrders($daily_report_id, $item_state, $items);
		}
		
		$this->set('results', $results);
	}
	
	public function add($daily_report_id = false, $item_state = false)
	{
		if (!$daily_report = $this->DailyReportsReportItem->DailyReport->read(null, $daily_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Daily Report')));
		}
		
		$this->set('daily_report', $daily_report);
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			$this->DailyReportsReportItem->create();
			$this->request->data['DailyReportsReportItem']['user_id'] = AuthComponent::user('id');
			
			
			if ($this->DailyReportsReportItem->addToReport($daily_report_id, $this->request->data))
			{
				$this->Flash->success(__('The %s have been saved', __('Report Items')));
				return $this->redirect(array('controller' => 'daily_reports', 'action' => 'view', $daily_report_id));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Report Items')));
			}
		}
		
		$item_states = $this->DailyReportsReportItem->getItemStates();
		
		// only show this field
		if($item_state !== false)
		{
			if(isset($item_states[$item_state]))
			{
				$_item_state = $item_states[$item_state];
				$item_states = array($item_state => $_item_state);
			}
		}
		$this->set('item_states', $item_states);
	}
	
	public function edit($id = false)
	{
		$this->DailyReportsReportItem->recursive = 0;
		if (!$xref_item = $this->DailyReportsReportItem->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Report Item')));
		}
		
		$this->set('xref_item', $xref_item);
		
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->DailyReportsReportItem->saveAssociated($this->request->data))
			{
				$this->Flash->success(__('The %s has been updated', __('Report Item')));
				return $this->redirect(array('controller' => 'daily_reports', 'action' => 'view', $xref_item['DailyReport']['id']));
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
		
		$this->set('item_states', $this->DailyReportsReportItem->getItemStates());
		$this->set('charge_codes', $this->DailyReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('activities', $this->DailyReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function charge_code_assign($daily_report_id = false)
	{
		$results = array();
		$this->set('results', $results);
		$this->layout = 'ajax_nodebug';
		
		if ($this->request->is('post') and $this->request->isAjax())
		{
			if(!$daily_report_id)
			{
				throw new NotFoundException(__('Unknown %s', __('Daily Report')));
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
			
			$results = $this->DailyReportsReportItem->assignChargeCodeReportItems($daily_report_id, $charge_code_id, $items);
		}
		
		$this->set('results', $results);
	}
	
	public function activity_assign($daily_report_id = false)
	{
		$results = array();
		$this->set('results', $results);
		$this->layout = 'ajax_nodebug';
		
		if ($this->request->is('post') and $this->request->isAjax())
		{
			if(!$daily_report_id)
			{
				throw new NotFoundException(__('Unknown %s', __('Daily Report')));
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
			
			$results = $this->DailyReportsReportItem->assignActivityReportItems($daily_report_id, $activity_id, $items);
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
			if(isset($this->request->data['DailyReportsReportItem']['daily_report_id']))
			{
				$this->DailyReportsReportItem->DailyReport->id = $this->request->data['DailyReportsReportItem']['daily_report_id'];
				$user_id = $this->DailyReportsReportItem->DailyReport->field('user_id');
				
				if(!isset($this->request->data['DailyReportsReportItem']['user_id']))
					$this->request->data['DailyReportsReportItem']['user_id'] = $user_id;
				
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
			$this->request->data['multiple'] = $this->DailyReportsReportItem->find('list', array(
				'fields' => array('DailyReportsReportItem.id', 'DailyReportsReportItem.report_item_id'),
				'conditions' => array('DailyReportsReportItem.id' => $ids),
				'recursive' => -1,
			));
		}
		
		if($this->request->data['DailyReportsReportItem']['multiselect_option'] == 'charge_code')
		{
			$redirect = array('action' => 'multiselect_charge_code');
		}
		elseif($this->request->data['DailyReportsReportItem']['multiselect_option'] == 'multicharge_code')
		{
			$redirect = array('action' => 'multiselect_multicharge_code');
		}
		elseif($this->request->data['DailyReportsReportItem']['multiselect_option'] == 'activity')
		{
			$redirect = array('action' => 'multiselect_activity');
		}
		elseif($this->request->data['DailyReportsReportItem']['multiselect_option'] == 'multiactivity')
		{
			$redirect = array('action' => 'multiselect_multiactivity');
		}
		elseif($this->request->data['DailyReportsReportItem']['multiselect_option'] == 'item_date')
		{
			$redirect = array('action' => 'multiselect_item_date');
		}
		elseif($this->request->data['DailyReportsReportItem']['multiselect_option'] == 'delete')
		{
			$this->DailyReportsReportItem->deleteAll(array(
				$this->DailyReportsReportItem->alias.'.id' => array_keys($this->request->data['multiple']),
			));
			
			$this->DailyReportsReportItem->ReportItem->deleteAll(array('ReportItem.id' => $this->request->data['multiple']));	
			$this->bypassReferer = true;
			return $this->redirect($this->referer());
		}
		
		if($redirect)
		{
			Cache::write('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			$this->bypassReferer = true;
			return $this->redirect($redirect);
		}
		
		if($this->DailyReportsReportItem->multiselect($this->request->data))
		{
			$this->Flash->success(__('The %s were updated for this %s.', __('Report Items'), __('Daily Report')));
			return $this->redirect($this->referer());
		}
		
		$this->Flash->error(__('The %s were NOT updated for this %s.', __('Report Items'), __('Daily Report')));
		$this->redirect($this->referer());
	}
	
//
	public function multiselect_charge_code()
	{
		$sessionData = Cache::read('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['DailyReportsReportItem'])?$this->request->data['DailyReportsReportItem']:0);
			if($this->DailyReportsReportItem->multiselect_items($sessionData, $this->request->data)) 
			{
				Cache::delete('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
				$this->Flash->success(__('The %s were updated for this %s.', __('Report Items'), __('Daily Report')));
				return $this->redirect($this->DailyReportsReportItem->multiselectReferer());
			}
			else
			{
				$this->Flash->error(__('The %s were NOT updated for this %s.', __('Report Items'), __('Daily Report')));
			}
		}
		
		$selected_items = array();
		if(isset($sessionData['multiple']))
		{
			$selected_items = $this->DailyReportsReportItem->ReportItem->find('list', array(
				'conditions' => array(
					'ReportItem.id' => $sessionData['multiple'],
				),
				'fields' => array('ReportItem.id', 'ReportItem.item'),
				'sort' => array('ReportItem.item' => 'asc'),
			));
		}
		
		$this->set('selected_items', $selected_items);
		
		// get the object types
		$this->set('charge_codes', $this->DailyReportsReportItem->ReportItem->ChargeCode->listForSortable());
	}
	
	public function multiselect_multicharge_code()
	{
		$sessionData = Cache::read('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->DailyReportsReportItem->multiselect_items_multiple($sessionData, $this->request->data['ReportItem'])) 
			{
				Cache::delete('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
				$this->Flash->success(__('The %s were updated for this %s.', __('Report Items'), __('Daily Report')));
				return $this->redirect($this->DailyReportsReportItem->multiselectReferer());
			}
			else
			{
				$this->Flash->error(__('The %s were NOT updated for this %s.', __('Report Items'), __('Daily Report')));
			}
		}

		$this->Prg->commonProcess();
		
		$ids = array();
		if(isset($sessionData['multiple']))
		{
			$ids = $sessionData['multiple'];
		}
		
		$conditions = array('DailyReportsReportItem.id' => array_keys($ids));
		$this->DailyReportsReportItem->recursive = 0;
		$this->paginate['contain'] = array('ReportItem');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('DailyReportsReportItem.created' => 'desc');
		$this->paginate['conditions'] = $this->DailyReportsReportItem->conditions($conditions, $this->passedArgs);
		$this->set('dailyreports_reportitems', $this->paginate());
		
		// get the object types
		$this->set('charge_codes', $this->DailyReportsReportItem->ReportItem->ChargeCode->listForSortable());
	}
	
	public function multiselect_activity()
	{
		$sessionData = Cache::read('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['DailyReportsReportItem'])?$this->request->data['DailyReportsReportItem']:0);
			if($this->DailyReportsReportItem->multiselect_items($sessionData, $this->request->data)) 
			{
				Cache::delete('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
				$this->Flash->success(__('The %s were updated for this %s.', __('Report Items'), __('Daily Report')));
				return $this->redirect($this->DailyReportsReportItem->multiselectReferer());
			}
			else
			{
				$this->Flash->error(__('The %s were NOT updated for this %s.', __('Report Items'), __('Daily Report')));
			}
		}
		
		$selected_items = array();
		if(isset($sessionData['multiple']))
		{
			$selected_items = $this->DailyReportsReportItem->ReportItem->find('list', array(
				'conditions' => array(
					'ReportItem.id' => $sessionData['multiple'],
				),
				'fields' => array('ReportItem.id', 'ReportItem.item'),
				'sort' => array('ReportItem.item' => 'asc'),
			));
		}
		
		$this->set('selected_items', $selected_items);
		
		// get the object types
		$this->set('activities', $this->DailyReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function multiselect_multiactivity()
	{
		$sessionData = Cache::read('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->DailyReportsReportItem->multiselect_items_multiple($sessionData, $this->request->data['ReportItem'])) 
			{
				Cache::delete('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
				$this->Flash->success(__('The %s were updated for this %s.', __('Report Items'), __('Daily Report')));
				return $this->redirect($this->DailyReportsReportItem->multiselectReferer());
			}
			else
			{
				$this->Flash->error(__('The %s were NOT updated for this %s.', __('Report Items'), __('Daily Report')));
			}
		}

		$this->Prg->commonProcess();
		
		$ids = array();
		if(isset($sessionData['multiple']))
		{
			$ids = $sessionData['multiple'];
		}
		
		$conditions = array('DailyReportsReportItem.id' => array_keys($ids));
		$this->DailyReportsReportItem->recursive = 0;
		$this->paginate['contain'] = array('ReportItem');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('DailyReportsReportItem.created' => 'desc');
		$this->paginate['conditions'] = $this->DailyReportsReportItem->conditions($conditions, $this->passedArgs);
		$this->set('dailyreports_reportitems', $this->paginate());
		
		// get the object types
		$this->set('activities', $this->DailyReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function multiselect_item_date()
	{
		$sessionData = Cache::read('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if(isset($this->request->data['ReportItem']['item_date']))
				$this->request->data['ReportItem']['item_date'] = date('Y-m-d H:i:s', strtotime($this->request->data['ReportItem']['item_date']));
			
			if($this->DailyReportsReportItem->multiselect_items($sessionData, $this->request->data)) 
			{
				Cache::delete('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
				$this->Flash->success(__('The %s were updated for this %s.', __('Report Items'), __('Daily Report')));
				return $this->redirect($this->DailyReportsReportItem->multiselectReferer());
			}
			else
			{
				$this->Flash->error(__('The %s were NOT updated for this %s.', __('Report Items'), __('Daily Report')));
			}
		}
		
		$selected_items = array();
		if(isset($sessionData['multiple']))
		{
			$selected_items = $this->DailyReportsReportItem->ReportItem->find('list', array(
				'conditions' => array(
					'ReportItem.id' => $sessionData['multiple'],
				),
				'fields' => array('ReportItem.id', 'ReportItem.item'),
				'sort' => array('ReportItem.item' => 'asc'),
			));
		}
		
		$this->set('selected_items', $selected_items);
	}
	
	public function delete($daily_report_id = false)
	{
		$results = array();
		$this->set('results', $results);
		$this->layout = 'ajax_nodebug';
		
		if ($this->request->is('post') and $this->request->isAjax())
		{
			if(!$daily_report_id)
			{
				throw new NotFoundException(__('Unknown %s', __('Daily Report')));
			}
			
			if(!isset($this->request->data['items']))
			{
				throw new NotFoundException(__('Unknown %s', __('Report Items')));
			}
			
			$items = $this->request->data['items'];
			
			/////////////////////// 
			/////// IF YOU CHANGE HOW YOU DELETE ITEMS FROM DELETEALL TO DELETE,
			/////// MAKE SURE YOU DON'T DELETE AN ITEM ASSOCIATED WITH OTHERS (WEEKLY, DAILY, MANAGEMENT)
			$results = $this->DailyReportsReportItem->deleteReportItems($daily_report_id, $items);
		}
		
		$this->set('results', $results);
	}
}