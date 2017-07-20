<?php
App::uses('AppController', 'Controller');
class ManagementReportsReportItemsController extends AppController 
{
	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'ReportItem.user_id' => AuthComponent::user('id'),
			'ManagementReport.id >' => 0,
		);
		
		$order = array();
		$order['ManagementReportsReportItem.item_date'] = 'desc';
		$order['ManagementReportsReportItem.item_state'] = 'asc';
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['conditions'] = $this->ManagementReportsReportItem->conditions($conditions, $this->passedArgs);
		
		$managementreports_reportitems = $this->paginate();
		$this->set('managementreports_reportitems', $managementreports_reportitems);
		$this->set('item_states', $this->ManagementReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->ManagementReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function review() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'ReportItem.user_id' => AuthComponent::user('id'),
			'ReportItem.review' => true,
			'ReportItem.reviewed' => false,
			'ManagementReport.id >' => 0,
		);
		$this->paginate['group'] = array('ReportItem.id');
		
		$order = array();
		$order['ManagementReportsReportItem.item_date'] = 'desc';
		$order['ManagementReportsReportItem.item_state'] = 'asc';
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['conditions'] = $this->ManagementReportsReportItem->conditions($conditions, $this->passedArgs);
		
		$managementreports_reportitems = $this->paginate();
		$this->set('managementreports_reportitems', $managementreports_reportitems);
		$this->set('item_states', $this->ManagementReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->ManagementReportsReportItem->ReportItem->Activity->listForSortable());
		$this->set('review_reasons', $this->ManagementReportsReportItem->ReportItem->ReviewReason->listForSortable());
	}
	
	public function reviewed() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'ReportItem.user_id' => AuthComponent::user('id'),
			'ReportItem.review' => true,
			'ReportItem.reviewed' => true,
			'ManagementReport.id >' => 0,
		);
		
		$this->paginate['group'] = array('ReportItem.id');
		
		$order = array();
		$order['ManagementReportsReportItem.item_date'] = 'desc';
		$order['ManagementReportsReportItem.item_state'] = 'asc';
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['conditions'] = $this->ManagementReportsReportItem->conditions($conditions, $this->passedArgs);
		
		$managementreports_reportitems = $this->paginate();
		$this->set('managementreports_reportitems', $managementreports_reportitems);
		$this->set('item_states', $this->ManagementReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->ManagementReportsReportItem->ReportItem->Activity->listForSortable());
		$this->set('review_reasons', $this->ManagementReportsReportItem->ReportItem->ReviewReason->listForSortable());
	}
	
	public function manager_management_report($management_report_id = false, $item_state = false) 
	{
		$this->Prg->commonProcess();
		
		if (!$management_report = $this->ManagementReportsReportItem->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$this->set('management_report', $management_report);
		
		$conditions = array(
			'ManagementReportsReportItem.management_report_id' => $management_report_id,
		);
		
		if($item_state !== false)
		{
			$conditions['ManagementReportsReportItem.item_state'] = $item_state;
		}
		
		$this->paginate['order'] = array(
			'ManagementReportsReportItem.item_state' => 'desc', 
			'ManagementReportsReportItem.item_order' => 'asc',
		);
		$this->paginate['conditions'] = $this->ManagementReportsReportItem->conditions($conditions, $this->passedArgs);
		
		if(isset($this->request->params['named']['getcount']) and $this->request->params['named']['getcount'])
		{
			$managementreports_reportitems = $this->paginate();
		}
		else
		{
			$this->paginate['recursive'] = 0;
			$this->paginate['contain'] = array('ReportItem');
			if(isset($this->paginate['limit'])) unset($this->paginate['limit']);
			$managementreports_reportitems = $this->ManagementReportsReportItem->find('all', $this->paginate);
		}
		
		$this->set('managementreports_reportitems', $managementreports_reportitems);
		$this->set('item_states', $this->ManagementReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->ManagementReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function manager_management_report_table($management_report_id = false, $item_state = false) 
	{
		$this->Prg->commonProcess();
		
		if (!$management_report = $this->ManagementReportsReportItem->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$this->set('management_report', $management_report);
		
		$conditions = array(
			'ManagementReportsReportItem.management_report_id' => $management_report_id,
		);
		$order = array();
		
		$order['ReportItem.item_date'] = 'asc';
		
		if($item_state !== false)
		{
			$conditions['ManagementReportsReportItem.item_state'] = $item_state;
		}
		else
		{
			$order['ManagementReportsReportItem.item_state'] = 'asc';
		}
		
		$order['ManagementReportsReportItem.item_order'] = 'asc';
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['contain'] = array('User', 'User.ChargeCode', 'ManagementReportsReportItem', 'WeeklyReportsReportItem', 'WeeklyReportsReportItem.WeeklyReport', 'Activity', 'ChargeCode');
		$this->paginate['conditions'] = $this->ManagementReportsReportItem->ReportItem->conditions($conditions, $this->passedArgs);
		
		$managementreports_reportitems = $this->paginate('ReportItem');
		$this->set('managementreports_reportitems', $managementreports_reportitems);
		$this->set('item_states', $this->ManagementReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->ManagementReportsReportItem->ReportItem->Activity->listForSortable());
		$this->set('review_reasons', $this->ManagementReportsReportItem->ReportItem->ReviewReason->listForSortable());
	}
	
	public function manager_management_report_finalized($management_report_id = false, $item_state = false) 
	{
		$this->Prg->commonProcess();
		
		if (!$management_report = $this->ManagementReportsReportItem->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$this->set('management_report', $management_report);
		
		$conditions = array(
			'ManagementReportsReportItem.management_report_id' => $management_report_id,
		);
		
		if($item_state !== false)
		{
			$conditions['ManagementReportsReportItem.item_state'] = $item_state;
		}
		
		$order = array(
			'ReportItem.item_date' => 'asc',
			'ManagementReportsReportItem.item_state' => 'desc', 
			'ManagementReportsReportItem.item_order' => 'asc',
		);
		
		if(isset($this->paginate['limit'])) unset($this->paginate['limit']);
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['contain'] = array('User', 'User.ChargeCode', 'ManagementReportsReportItem', 'WeeklyReportsReportItem');
		$this->paginate['conditions'] = $this->ManagementReportsReportItem->ReportItem->conditions($conditions, $this->passedArgs);
		
		$managementreports_reportitems = $this->ManagementReportsReportItem->ReportItem->find('all', $this->paginate);
		$this->set('managementreports_reportitems', $managementreports_reportitems);
		$this->set('item_states', $this->ManagementReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->ManagementReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function manager_management_report_highlighted($management_report_id = false) 
	{
		$this->Prg->commonProcess();
		
		if (!$management_report = $this->ManagementReportsReportItem->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$this->set('management_report', $management_report);
		
		$conditions = array(
			'ManagementReportsReportItem.management_report_id' => $management_report_id,
			'ManagementReportsReportItem.highlighted' => true,
		);
		$order = array();
		
		$order['ReportItem.item_date'] = 'asc';
		$order['ManagementReportsReportItem.item_state'] = 'asc';
		$order['ManagementReportsReportItem.item_order'] = 'asc';
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['contain'] = array('User', 'User.ChargeCode', 'ManagementReportsReportItem', 'WeeklyReportsReportItem');
		$this->paginate['conditions'] = $this->ManagementReportsReportItem->ReportItem->conditions($conditions, $this->passedArgs);
		
		$managementreports_reportitems = $this->paginate('ReportItem');
		$this->set('managementreports_reportitems', $managementreports_reportitems);
		$this->set('item_states', $this->ManagementReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->ManagementReportsReportItem->ReportItem->Activity->listForSortable());
		$this->set('review_reasons', $this->ManagementReportsReportItem->ReportItem->ReviewReason->listForSortable());
	}
	
	public function manager_management_report_highlighted_finalized($management_report_id = false) 
	{
		$this->Prg->commonProcess();
		
		if (!$management_report = $this->ManagementReportsReportItem->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$this->set('management_report', $management_report);
		
		$conditions = array(
			'ManagementReportsReportItem.management_report_id' => $management_report_id,
			'ManagementReportsReportItem.highlighted' => true,
		);
		$order = array();
		
		$order['ReportItem.item_date'] = 'asc';
		$order['ManagementReportsReportItem.item_state'] = 'asc';
		$order['ManagementReportsReportItem.item_order'] = 'asc';
		
		if(isset($this->paginate['limit'])) unset($this->paginate['limit']);
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['contain'] = array('User', 'User.ChargeCode', 'ManagementReportsReportItem', 'WeeklyReportsReportItem');
		$this->paginate['conditions'] = $this->ManagementReportsReportItem->ReportItem->conditions($conditions, $this->passedArgs);
		
		$managementreports_reportitems = $this->ManagementReportsReportItem->ReportItem->find('all', $this->paginate);
		$this->set('managementreports_reportitems', $managementreports_reportitems);
		$this->set('item_states', $this->ManagementReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->ManagementReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function manager_management_report_mine($management_report_id = false) 
	{
		$this->Prg->commonProcess();
		
		if (!$management_report = $this->ManagementReportsReportItem->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$this->set('management_report', $management_report);
		
		$conditions = array(
			'ManagementReportsReportItem.management_report_id' => $management_report_id,
			'ManagementReportsReportItem.user_id' => AuthComponent::user('id'),
		);
		$order = array();
		
		$order['ReportItem.item_date'] = 'asc';
		$order['ManagementReportsReportItem.item_state'] = 'asc';
		$order['ManagementReportsReportItem.item_order'] = 'asc';
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['contain'] = array('User', 'User.ChargeCode', 'ManagementReportsReportItem', 'WeeklyReportsReportItem');
		$this->paginate['conditions'] = $this->ManagementReportsReportItem->ReportItem->conditions($conditions, $this->passedArgs);
		
		$managementreports_reportitems = $this->paginate('ReportItem');
		$this->set('managementreports_reportitems', $managementreports_reportitems);
		$this->set('item_states', $this->ManagementReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->ManagementReportsReportItem->ReportItem->Activity->listForSortable());
		$this->set('review_reasons', $this->ManagementReportsReportItem->ReportItem->ReviewReason->listForSortable());
	}
	
	public function manager_management_report_charge_code($management_report_id = false, $charge_code_id = false) 
	{
		$this->Prg->commonProcess();
		
		if (!$charge_code_id)
		{
			throw new NotFoundException(__('Invalid %s', __('Charge Code')));
		}
		
		if (!$management_report = $this->ManagementReportsReportItem->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$this->set('management_report', $management_report);
		
		$conditions = array(
			'ManagementReportsReportItem.management_report_id' => $management_report_id,
			'ReportItem.charge_code_id' => $charge_code_id,
		);
		
		$order = array();
		$order['ReportItem.item_date'] = 'asc';
		$order['ManagementReportsReportItem.item_state'] = 'asc';
		$order['ManagementReportsReportItem.item_order'] = 'asc';
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['contain'] = array('User', 'User.ChargeCode', 'ManagementReportsReportItem', 'WeeklyReportsReportItem');
		$this->paginate['conditions'] = $this->ManagementReportsReportItem->ReportItem->conditions($conditions, $this->passedArgs);
		
		$managementreports_reportitems = $this->paginate('ReportItem');
		$this->set('managementreports_reportitems', $managementreports_reportitems);
		$this->set('item_states', $this->ManagementReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->ManagementReportsReportItem->ReportItem->Activity->listForSortable());
		$this->set('review_reasons', $this->ManagementReportsReportItem->ReportItem->ReviewReason->listForSortable());
	}
	
	public function manager_management_report_charge_code_finalized($management_report_id = false, $charge_code_id = false) 
	{
		$this->Prg->commonProcess();
		
		if (!$charge_code_id)
		{
			throw new NotFoundException(__('Invalid %s', __('Charge Code')));
		}
		
		if (!$management_report = $this->ManagementReportsReportItem->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$this->set('management_report', $management_report);
		
		$conditions = array(
			'ManagementReportsReportItem.management_report_id' => $management_report_id,
			'ReportItem.charge_code_id' => $charge_code_id,
		);
		
		$order = array();
		$order['ReportItem.item_date'] = 'asc';
		$order['ManagementReportsReportItem.item_state'] = 'asc';
		$order['ManagementReportsReportItem.item_order'] = 'asc';
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['contain'] = array('User', 'User.ChargeCode', 'ManagementReportsReportItem', 'WeeklyReportsReportItem');
		$this->paginate['conditions'] = $this->ManagementReportsReportItem->ReportItem->conditions($conditions, $this->passedArgs);
		
		$managementreports_reportitems = $this->paginate('ReportItem');
		$this->set('managementreports_reportitems', $managementreports_reportitems);
		$this->set('item_states', $this->ManagementReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->ManagementReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function manager_management_report_activity($management_report_id = false, $activity_id = false) 
	{
		$this->Prg->commonProcess();
		
		if (!$activity_id)
		{
			throw new NotFoundException(__('Invalid %s', __('Charge Code')));
		}
		
		if (!$management_report = $this->ManagementReportsReportItem->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$this->set('management_report', $management_report);
		
		$conditions = array(
			'ManagementReportsReportItem.management_report_id' => $management_report_id,
			'ReportItem.activity_id' => $activity_id,
		);
		
		$order = array();
		$order['ReportItem.item_date'] = 'asc';
		$order['ManagementReportsReportItem.item_state'] = 'asc';
		$order['ManagementReportsReportItem.item_order'] = 'asc';
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['contain'] = array('User', 'User.ChargeCode', 'ManagementReportsReportItem', 'WeeklyReportsReportItem');
		$this->paginate['conditions'] = $this->ManagementReportsReportItem->ReportItem->conditions($conditions, $this->passedArgs);
		
		$managementreports_reportitems = $this->paginate('ReportItem');
		$this->set('managementreports_reportitems', $managementreports_reportitems);
		$this->set('item_states', $this->ManagementReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->ManagementReportsReportItem->ReportItem->Activity->listForSortable());
		$this->set('review_reasons', $this->ManagementReportsReportItem->ReportItem->ReviewReason->listForSortable());
	}
	
	public function manager_management_report_activity_finalized($management_report_id = false, $activity_id = false) 
	{
		$this->Prg->commonProcess();
		
		if (!$activity_id)
		{
			throw new NotFoundException(__('Invalid %s', __('Charge Code')));
		}
		
		if (!$management_report = $this->ManagementReportsReportItem->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$this->set('management_report', $management_report);
		
		$conditions = array(
			'ManagementReportsReportItem.management_report_id' => $management_report_id,
			'ReportItem.activity_id' => $activity_id,
		);
		
		$order = array();
		$order['ReportItem.item_date'] = 'asc';
		$order['ManagementReportsReportItem.item_state'] = 'asc';
		$order['ManagementReportsReportItem.item_order'] = 'asc';
		
		$this->paginate['order'] = $order;
		
		$this->paginate['recursive'] = 0;
		$this->paginate['contain'] = array('User', 'User.ChargeCode', 'ManagementReportsReportItem', 'WeeklyReportsReportItem');
		$this->paginate['conditions'] = $this->ManagementReportsReportItem->ReportItem->conditions($conditions, $this->passedArgs);
		
		$managementreports_reportitems = $this->paginate('ReportItem');
		$this->set('managementreports_reportitems', $managementreports_reportitems);
		$this->set('item_states', $this->ManagementReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->ManagementReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function manager_update_order($management_report_id = false)
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
			if (!$management_report = $this->ManagementReportsReportItem->ManagementReport->read(null, $management_report_id))
			{
				throw new NotFoundException(__('Invalid %s', __('Management Report')));
			}
		
			$this->set('management_report', $management_report);
			
			$results = $this->ManagementReportsReportItem->updateItemsStateOrders($management_report_id, $item_state, $items);
		}
		
		$this->set('results', $results);
	}
	
	public function manager_add($management_report_id = false)
	{
		if (!$management_report = $this->ManagementReportsReportItem->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$this->set('management_report', $management_report);
		
		if ($this->request->is('post'))
		{
			$this->ManagementReportsReportItem->create();
			
			if ($this->ManagementReportsReportItem->addToReport($management_report_id, $this->request->data))
			{
				$this->Flash->success(__('The %s have been saved', __('Report Items')));
				return $this->redirect(array('controller' => 'management_reports', 'action' => 'view', $management_report_id));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Report Items')));
			}
		}
		$this->set('item_states', $this->ManagementReportsReportItem->getItemStates());
		$this->set('charge_codes', $this->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('activities', $this->ManagementReportsReportItem->ReportItem->Activity->listForSortable());
		$this->set('users', $this->ManagementReportsReportItem->User->listForSortable());
	}
	
	public function manager_edit($id = false)
	{
		$this->ManagementReportsReportItem->recursive = 0;
		if (!$xref_item = $this->ManagementReportsReportItem->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Report Item')));
		}
		
		$this->set('xref_item', $xref_item);
		
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ManagementReportsReportItem->saveAssociated($this->request->data))
			{
				$this->Flash->error(__('The %s has been updated', __('Report Item')));
				return $this->redirect(array('controller' => 'management_reports', 'action' => 'view', $xref_item['ManagementReport']['id']));
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
		$this->set('xref_item', $xref_item);
		$this->set('item_states', $this->ManagementReportsReportItem->getItemStates());
		$this->set('charge_codes', $this->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('activities', $this->ManagementReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function manager_needs_review_email($management_report_id = false)
	{
		if (!$management_report = $this->ManagementReportsReportItem->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}

		if ($this->ManagementReportsReportItem->sendReviewEmails($management_report_id)) 
		{
			$this->Flash->success(__('%s emails were sent.', __('Review Reason')));
			return $this->redirect($this->referer());
		}
		
		$this->Flash->error(__('Unable to send %s emails. Reason: %s', __('Review Reason'), $this->ManagementReportsReportItem->modelError));
		return $this->redirect($this->referer());
	}
	
	public function manager_griddelete()
	{
		if(!$this->request->is('ajax'))
		{
			throw new InternalErrorException(__('Request in not an Ajax request.'));
		}
		
		if(!isset($this->request->data['ManagementReportsReportItem']['id']))
		{
			throw new InternalErrorException(__('Unknown %s Id.', __('Report Item')));
		}
		
		$this->ManagementReportsReportItem->id = $this->request->data['ManagementReportsReportItem']['id'];
		if(!$management_report_id = $this->ManagementReportsReportItem->field('management_report_id'))
		{
			throw new InternalErrorException(__('Unknown %s Id.', __('Management Report')));
		}
		
		$results = false;
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			$results = $this->ManagementReportsReportItem->deleteReportItems($management_report_id, array($this->ManagementReportsReportItem->id => $this->ManagementReportsReportItem->id));
		}
		
		$this->set('results', $results);
		return $this->render('Utilities./Elements/gridedit', 'ajax_nodebug');
	}
	
	public function manager_charge_code_assign($management_report_id = false)
	{
		$results = array();
		$this->set('results', $results);
		$this->layout = 'ajax_nodebug';
		
		if ($this->request->is('post') and $this->request->isAjax())
		{
			if(!$management_report_id)
			{
				throw new NotFoundException(__('Unknown %s', __('Management Report')));
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
			
			$results = $this->ManagementReportsReportItem->assignChargeCodeReportItems($management_report_id, $charge_code_id, $items);
		}
		
		$this->set('results', $results);
	}
	
	public function manager_activity_assign($management_report_id = false)
	{
		$results = array();
		$this->set('results', $results);
		$this->layout = 'ajax_nodebug';
		
		if ($this->request->is('post') and $this->request->isAjax())
		{
			if(!$management_report_id)
			{
				throw new NotFoundException(__('Unknown %s', __('Management Report')));
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
			
			$results = $this->ManagementReportsReportItem->assignActivityReportItems($management_report_id, $activity_id, $items);
		}
		
		$this->set('results', $results);
	}
	
	public function manager_highlight_assign($management_report_id = false, $highlighted = true)
	{
		$results = array();
		$this->set('results', $results);
		$this->layout = 'ajax_nodebug';
		
		if ($this->request->is('post') and $this->request->isAjax())
		{
			if(!$management_report_id)
			{
				throw new NotFoundException(__('Unknown %s', __('Management Report')));
			}
			
			if(!isset($this->request->data['items']))
			{
				throw new NotFoundException(__('Unknown %s', __('Report Items')));
			}
			
			$items = $this->request->data['items'];
			
			$results = $this->ManagementReportsReportItem->assignHighlightReportItems($management_report_id, $highlighted, $items);
		}
		
		$this->set('results', $results);
	}
	
//
	public function manager_multiselect()
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
			$this->request->data['multiple'] = $this->ManagementReportsReportItem->find('list', array(
				'fields' => array('ManagementReportsReportItem.id', 'ManagementReportsReportItem.report_item_id'),
				'conditions' => array('ManagementReportsReportItem.id' => $ids),
				'recursive' => -1,
			));
		}
		
		if($this->request->data['ManagementReportsReportItem']['multiselect_option'] == 'charge_code')
		{
			$redirect = array('action' => 'multiselect_charge_code');
		}
		elseif($this->request->data['ManagementReportsReportItem']['multiselect_option'] == 'multicharge_code')
		{
			$redirect = array('action' => 'multiselect_multicharge_code');
		}
		elseif($this->request->data['ManagementReportsReportItem']['multiselect_option'] == 'activity')
		{
			$redirect = array('action' => 'multiselect_activity');
		}
		elseif($this->request->data['ManagementReportsReportItem']['multiselect_option'] == 'multiactivity')
		{
			$redirect = array('action' => 'multiselect_multiactivity');
		}
		elseif($this->request->data['ManagementReportsReportItem']['multiselect_option'] == 'item_date')
		{
			$redirect = array('action' => 'multiselect_item_date');
		}
		elseif($this->request->data['ManagementReportsReportItem']['multiselect_option'] == 'delete')
		{
			$this->ManagementReportsReportItem->deleteAll(array(
				$this->ManagementReportsReportItem->alias.'.id' => array_keys($this->request->data['multiple']),
			));
			
			$this->ManagementReportsReportItem->ReportItem->deleteAll(array('ReportItem.id' => $this->request->data['multiple']));	
			$this->bypassReferer = true;
			return $this->redirect($this->referer());
		}
		
		if($redirect)
		{
			Cache::write('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			$this->bypassReferer = true;
			return $this->redirect($redirect);
		}
		
		if($this->ManagementReportsReportItem->multiselect($this->request->data))
		{
			$this->Flash->success(__('The %s were updated for this %s.', __('Report Items'), __('Management Report')));
			return $this->redirect($this->referer());
		}
		
		$this->Flash->error(__('The %s were NOT updated for this %s.', __('Report Items'), __('Management Report')));
		$this->redirect($this->referer());
	}
	
//
	public function manager_multiselect_charge_code()
	{
		$sessionData = Cache::read('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['ManagementReportsReportItem'])?$this->request->data['ManagementReportsReportItem']:0);
			if($this->ManagementReportsReportItem->multiselect_items($sessionData, $this->request->data)) 
			{
				Cache::delete('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
				$this->Flash->success(__('The %s were updated for this %s.', __('Report Items'), __('Management Report')));
				return $this->redirect($this->ManagementReportsReportItem->multiselectReferer());
			}
			else
			{
				$this->Flash->error(__('The %s were NOT updated for this %s.', __('Report Items'), __('Management Report')));
			}
		}
		
		$selected_items = array();
		if(isset($sessionData['multiple']))
		{
			$selected_items = $this->ManagementReportsReportItem->ReportItem->find('list', array(
				'conditions' => array(
					'ReportItem.id' => $sessionData['multiple'],
				),
				'fields' => array('ReportItem.id', 'ReportItem.item'),
				'sort' => array('ReportItem.item' => 'asc'),
			));
		}
		
		$this->set('selected_items', $selected_items);
		
		// get the object types
		$this->set('charge_codes', $this->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable());
	}
	
	public function manager_multiselect_multicharge_code()
	{
		$sessionData = Cache::read('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->ManagementReportsReportItem->multiselect_items_multiple($sessionData, $this->request->data['ReportItem'])) 
			{
				Cache::delete('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
				$this->Flash->success(__('The %s were updated for this %s.', __('Report Items'), __('Management Report')));
				return $this->redirect($this->ManagementReportsReportItem->multiselectReferer());
			}
			else
			{
				$this->Flash->error(__('The %s were NOT updated for this %s.', __('Report Items'), __('Management Report')));
			}
		}

		$this->Prg->commonProcess();
		
		$ids = array();
		if(isset($sessionData['multiple']))
		{
			$ids = $sessionData['multiple'];
		}
		
		$conditions = array('ManagementReportsReportItem.id' => array_keys($ids));
		$this->ManagementReportsReportItem->recursive = 0;
		$this->paginate['contain'] = array('ReportItem');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('ManagementReportsReportItem.created' => 'desc');
		$this->paginate['conditions'] = $this->ManagementReportsReportItem->conditions($conditions, $this->passedArgs);
		$this->set('managementreports_reportitems', $this->paginate());
		
		// get the object types
		$this->set('charge_codes', $this->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable());
	}
	
	public function manager_multiselect_activity()
	{
		$sessionData = Cache::read('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			$multiselect_value = (isset($this->request->data['ManagementReportsReportItem'])?$this->request->data['ManagementReportsReportItem']:0);
			if($this->ManagementReportsReportItem->multiselect_items($sessionData, $this->request->data)) 
			{
				Cache::delete('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
				$this->Flash->success(__('The %s were updated for this %s.', __('Report Items'), __('Management Report')));
				return $this->redirect($this->ManagementReportsReportItem->multiselectReferer());
			}
			else
			{
				$this->Flash->error(__('The %s were NOT updated for this %s.', __('Report Items'), __('Management Report')));
			}
		}
		
		$selected_items = array();
		if(isset($sessionData['multiple']))
		{
			$selected_items = $this->ManagementReportsReportItem->ReportItem->find('list', array(
				'conditions' => array(
					'ReportItem.id' => $sessionData['multiple'],
				),
				'fields' => array('ReportItem.id', 'ReportItem.item'),
				'sort' => array('ReportItem.item' => 'asc'),
			));
		}
		
		$this->set('selected_items', $selected_items);
		
		// get the object types
		$this->set('activities', $this->ManagementReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function manager_multiselect_multiactivity()
	{
		$sessionData = Cache::read('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->ManagementReportsReportItem->multiselect_items_multiple($sessionData, $this->request->data['ReportItem'])) 
			{
				Cache::delete('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
				$this->Flash->success(__('The %s were updated for this %s.', __('Report Items'), __('Management Report')));
				return $this->redirect($this->ManagementReportsReportItem->multiselectReferer());
			}
			else
			{
				$this->Flash->error(__('The %s were NOT updated for this %s.', __('Report Items'), __('Management Report')));
			}
		}

		$this->Prg->commonProcess();
		
		$ids = array();
		if(isset($sessionData['multiple']))
		{
			$ids = $sessionData['multiple'];
		}
		
		$conditions = array('ManagementReportsReportItem.id' => array_keys($ids));
		$this->ManagementReportsReportItem->recursive = 0;
		$this->paginate['contain'] = array('ReportItem');
		$this->paginate['limit'] = count($ids);
		$this->paginate['order'] = array('ManagementReportsReportItem.created' => 'desc');
		$this->paginate['conditions'] = $this->ManagementReportsReportItem->conditions($conditions, $this->passedArgs);
		$this->set('managementreports_reportitems', $this->paginate());
		
		// get the object types
		$this->set('activities', $this->ManagementReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function multiselect_item_date()
	{
		$sessionData = Cache::read('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if(isset($this->request->data['ReportItem']['item_date']))
				$this->request->data['ReportItem']['item_date'] = date('Y-m-d H:i:s', strtotime($this->request->data['ReportItem']['item_date']));
			
			if($this->ManagementReportsReportItem->multiselect_items($sessionData, $this->request->data)) 
			{
				Cache::delete('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), 'sessions');
				$this->Flash->success(__('The %s were updated for this %s.', __('Report Items'), __('Daily Report')));
				return $this->redirect($this->ManagementReportsReportItem->multiselectReferer());
			}
			else
			{
				$this->Flash->error(__('The %s were NOT updated for this %s.', __('Report Items'), __('Daily Report')));
			}
		}
		
		$selected_items = array();
		if(isset($sessionData['multiple']))
		{
			$selected_items = $this->ManagementReportsReportItem->ReportItem->find('list', array(
				'conditions' => array(
					'ReportItem.id' => $sessionData['multiple'],
				),
				'fields' => array('ReportItem.id', 'ReportItem.item'),
				'sort' => array('ReportItem.item' => 'asc'),
			));
		}
		
		$this->set('selected_items', $selected_items);
	}
	
	public function manager_delete($management_report_id = false)
	{
		$results = array();
		$this->set('results', $results);
		$this->layout = 'ajax_nodebug';
		
		if ($this->request->is('post') and $this->request->isAjax())
		{
			if(!$management_report_id)
			{
				throw new NotFoundException(__('Unknown %s', __('Management Report')));
			}
			
			if(!isset($this->request->data['items']))
			{
				throw new NotFoundException(__('Unknown %s', __('Report Items')));
			}
			
			$items = $this->request->data['items'];
			
			/////////////////////// 
			/////// IF YOU CHANGE HOW YOU DELETE ITEMS FROM DELETEALL TO DELETE,
			/////// MAKE SURE YOU DON'T DELETE AN ITEM ASSOCIATED WITH OTHERS (WEEKLY, DAILY, MANAGEMENT)
			$results = $this->ManagementReportsReportItem->deleteReportItems($management_report_id, $items);
		}
		
		$this->set('results', $results);
	}
}