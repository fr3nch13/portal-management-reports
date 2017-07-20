<?php
App::uses('AppController', 'Controller');

class ManagementReportsController extends AppController 
{
//
	public function manager_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		
		$this->ManagementReport->recursive = 0;
		$this->paginate['order'] = array('ManagementReport.created' => 'desc');
		$this->paginate['conditions'] = $this->ManagementReport->conditions($conditions, $this->passedArgs); 
		$this->set('management_reports', $this->paginate());
	}
	
	public function manager_tag($tag_id = null)  
	{ 
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->ManagementReport->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'ManagementReport.user_id' => AuthComponent::user('id'),
		);
		$conditions[] = $this->ManagementReport->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'ManagementReport');
		
		$this->paginate['order'] = array('ManagementReport.created' => 'desc');
		$this->paginate['conditions'] = $this->ManagementReport->conditions($conditions, $this->passedArgs); 
		$this->set('management_reports', $this->paginate());
	}
	
	public function manager_view($id = false, $full = false)
	{
		$this->ManagementReport->recursive = 0;
		if (!$management_report = $this->ManagementReport->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		$this->set('management_report', $management_report);
		
		if(isset($this->request->params['ext']))
		{
			$this->set($this->ManagementReport->finalizedVars($id));
			if($full)
			{
				return $this->render('manager_view_full');
			}
			return $this->render();
		}
		
		$this->set('item_sections', $this->ManagementReport->ManagementReportItem->getItemSections(false, true));
		$this->set('item_states', $this->ManagementReport->ManagementReportsReportItem->getItemStates());
		$this->set('item_charge_codes', $this->ManagementReport->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable());
		$this->set('item_activities', $this->ManagementReport->ManagementReportsReportItem->ReportItem->Activity->listForSortable());
	}
	
	public function manager_view_dashboard($management_report_id = false)
	{
		if (!$management_report = $this->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		// find each user that has a reported item
		
		$user_ids = $this->ManagementReport->ManagementReportsReportItem->find('list', [
			'conditions' => ['ManagementReportsReportItem.management_report_id' => $management_report_id],
			'fields' => ['ManagementReportsReportItem.user_id', 'ManagementReportsReportItem.user_id'],
		]);
		
		$users = $this->ManagementReport->User->find('all', [
			'conditions' => ['User.id' => $user_ids],
		]);
		
		$reportItem_ids = $this->ManagementReport->ManagementReportsReportItem->find('list', [
			'conditions' => ['ManagementReportsReportItem.management_report_id' => $management_report_id],
			'fields' => ['ManagementReportsReportItem.report_item_id', 'ManagementReportsReportItem.report_item_id'],
		]);
		
		$chargeCode_ids = $this->ManagementReport->ManagementReportsReportItem->ReportItem->find('list', [
			'recursive' => -1,
			'conditions' => ['ReportItem.id' => $reportItem_ids],
			'fields' => ['ReportItem.charge_code_id', 'ReportItem.charge_code_id'],
		]);
		
		$chargeCodes = $this->ManagementReport->User->ChargeCode->find('all', [
			'conditions' => ['ChargeCode.id' => $chargeCode_ids],
		]);
		
		$this->set(compact(['management_report_id', 'management_report', 'users', 'chargeCodes']));
	}
	
	public function manager_db_block_activity($management_report_id = false)
	{
		if (!$management_report = $this->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$reportItem_ids = $this->ManagementReport->ManagementReportsReportItem->find('list', [
			'conditions' => ['ManagementReportsReportItem.management_report_id' => $management_report_id],
			'fields' => ['ManagementReportsReportItem.report_item_id', 'ManagementReportsReportItem.report_item_id'],
		]);
		
		$reportItems = $this->ManagementReport->ManagementReportsReportItem->ReportItem->find('all', [
			'contain' => ['User', 'Activity'],
			'conditions' => ['ReportItem.id' => $reportItem_ids],
		]);
		
		$activities = $this->ManagementReport->ManagementReportsReportItem->ReportItem->Activity->find('all');
		
		$this->set(compact(['management_report_id', 'management_report', 'reportItems', 'activities']));
	}
	
	public function manager_db_tab_activity($management_report_id = false)
	{
		if (!$management_report = $this->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$reportItem_ids = $this->ManagementReport->ManagementReportsReportItem->find('list', [
			'conditions' => ['ManagementReportsReportItem.management_report_id' => $management_report_id],
			'fields' => ['ManagementReportsReportItem.report_item_id', 'ManagementReportsReportItem.user_id'],
		]);
		
		$reportItems = $this->ManagementReport->ManagementReportsReportItem->ReportItem->find('all', [
			'contain' => ['User', 'Activity'],
			'conditions' => ['ReportItem.id' => array_keys($reportItem_ids)],
		]);
		
		$activities = $this->ManagementReport->ManagementReportsReportItem->ReportItem->Activity->find('all');
		
		$users = $this->ManagementReport->User->find('all', [
			'contain' => ['ChargeCode'],
			'conditions' => ['User.id' => $reportItem_ids],
		]);
		
		$this->set(compact(['management_report_id', 'management_report', 'reportItems', 'activities', 'users']));
	}
	
	public function manager_db_block_charge_code($management_report_id = false, $user_id = false)
	{
		if (!$management_report = $this->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$user = [];
		$conditions = [
			'ManagementReportsReportItem.management_report_id' => $management_report_id,
		];
		if($user_id)
		{
			$user = $this->ManagementReport->User->find('first', [
				'contain' => ['ChargeCode'],
				'conditions' => ['User.id' => $user_id],
			]);
			$conditions['ManagementReportsReportItem.user_id'] = $user_id;
		}
		
		$reportItem_ids = $this->ManagementReport->ManagementReportsReportItem->find('list', [
			'conditions' => $conditions,
			'fields' => ['ManagementReportsReportItem.report_item_id', 'ManagementReportsReportItem.report_item_id'],
			'order' => ['ManagementReportsReportItem.report_item_id' => 'asc'],
		]);
		
		$reportItems = $this->ManagementReport->ManagementReportsReportItem->ReportItem->find('all', [
			'contain' => ['ChargeCode'],
			'conditions' => ['ReportItem.id' => $reportItem_ids],
		]);

		
		$chargeCodes = $this->ManagementReport->ManagementReportsReportItem->ReportItem->ChargeCode->find('all');
		
		$this->set(compact(['management_report_id', 'management_report', 'reportItems', 'chargeCodes', 'user_id', 'user']));
	}
	
	public function manager_db_tab_charge_code($management_report_id = false)
	{
		if (!$management_report = $this->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$reportItem_ids = $this->ManagementReport->ManagementReportsReportItem->find('list', [
			'conditions' => ['ManagementReportsReportItem.management_report_id' => $management_report_id],
			'fields' => ['ManagementReportsReportItem.report_item_id', 'ManagementReportsReportItem.user_id'],
		]);
		
		$reportItems = $this->ManagementReport->ManagementReportsReportItem->ReportItem->find('all', [
			'contain' => ['User', 'ChargeCode'],
			'conditions' => ['ReportItem.id' => array_keys($reportItem_ids)],
		]);
		
		$chargeCodes = $this->ManagementReport->ManagementReportsReportItem->ReportItem->ChargeCode->find('all');
		
		$users = $this->ManagementReport->User->find('all', [
			'contain' => ['ChargeCode'],
			'conditions' => ['User.id' => $reportItem_ids],
		]);
		
		$this->set(compact(['management_report_id', 'management_report', 'reportItems', 'chargeCodes', 'users']));
	}
	
	public function manager_db_block_charge_code_consolidated($management_report_id = false, $charge_code_id = false)
	{
		if (!$management_report = $this->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$charge_code = [];
		$conditions = [
			'ManagementReportsReportItem.management_report_id' => $management_report_id,
		];
		if($charge_code_id)
		{
			$chargeCode = $this->ManagementReport->User->ChargeCode->read(null, $charge_code_id);
			
			$user_ids = $this->ManagementReport->User->find('list', [
				'conditions' => ['User.charge_code_id' => $charge_code_id],
				'fields' => ['User.id', 'User.id'],
			]);

			$conditions['ManagementReportsReportItem.user_id'] = $user_ids;
		}
		
		$reportItem_ids = $this->ManagementReport->ManagementReportsReportItem->find('list', [
			'conditions' => $conditions,
			'fields' => ['ManagementReportsReportItem.report_item_id', 'ManagementReportsReportItem.report_item_id'],
		]);
		
		$reportItems = $this->ManagementReport->ManagementReportsReportItem->ReportItem->find('all', [
			'contain' => ['ChargeCode'],
			'conditions' => ['ReportItem.id' => $reportItem_ids],
		]);
		
		$chargeCodes = $this->ManagementReport->ManagementReportsReportItem->ReportItem->ChargeCode->find('all');
		
		$this->set(compact(['management_report_id', 'management_report', 'reportItems', 'chargeCodes', 'chargeCode', 'charge_code_id', 'user']));
	}
	
	public function manager_db_tab_charge_code_consolidated($management_report_id = false)
	{
		if (!$management_report = $this->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$reportItem_ids = $this->ManagementReport->ManagementReportsReportItem->find('list', [
			'conditions' => ['ManagementReportsReportItem.management_report_id' => $management_report_id],
			'fields' => ['ManagementReportsReportItem.report_item_id', 'ManagementReportsReportItem.user_id'],
		]);
		
		$reportItems = $this->ManagementReport->ManagementReportsReportItem->ReportItem->find('all', [
			'contain' => ['User', 'ChargeCode'],
			'conditions' => ['ReportItem.id' => array_keys($reportItem_ids)],
		]);
		
		$chargeCodes = $this->ManagementReport->ManagementReportsReportItem->ReportItem->ChargeCode->find('all');
		
		$users = $this->ManagementReport->User->find('all', [
			'contain' => ['ChargeCode'],
			'conditions' => ['User.id' => $reportItem_ids],
		]);
		
		$this->set(compact(['management_report_id', 'management_report', 'reportItems', 'chargeCodes', 'users']));
	}
	
	public function manager_view_charge_code($id = false)
	{
		if (!$management_report = $this->ManagementReport->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$charge_code_ids = $this->ManagementReport->ManagementReportsReportItem->find('list', array(
			'recursive' => 0,
			'contain' => array('ReportItem'),
			'conditions' => array(
				'ManagementReportsReportItem.management_report_id' => $id,
			),
			'fields' => array('ReportItem.charge_code_id', 'ReportItem.charge_code_id'),
		));
		
		$item_charge_codes = $this->ManagementReport->ManagementReportsReportItem->ReportItem->ChargeCode->listForSortable(false, false, $charge_code_ids);
		
		$this->set('management_report', $management_report);
		$this->set('item_charge_codes', $item_charge_codes);
	}
	
	public function manager_view_activity($id = false)
	{
		if (!$management_report = $this->ManagementReport->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$activity_ids = $this->ManagementReport->ManagementReportsReportItem->find('list', array(
			'recursive' => 0,
			'contain' => array('ReportItem'),
			'conditions' => array(
				'ManagementReportsReportItem.management_report_id' => $id,
			),
			'fields' => array('ReportItem.activity_id', 'ReportItem.activity_id'),
		));
		
		$item_activities = $this->ManagementReport->ManagementReportsReportItem->ReportItem->Activity->listForSortable(false, false, $activity_ids);
		
		$this->set('management_report', $management_report);
		$this->set('item_activities', $item_activities);
	}
	
	public function manager_finalize($id = false, $finalized = false)
	{
		$this->ManagementReport->id = $id;
		if (!$this->ManagementReport->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		if ($this->request->is('post'))
		{
			if($finalized)
			{
				if ($this->ManagementReport->finalize($id))
				{
					$this->Session->setFlash(__('The %s has been Finalized', __('Management Report')));
					return $this->redirect(array('action' => 'view', $this->ManagementReport->id));
				}
				else
				{
					$errMsg = __('The %s could not be Finalized. Please, try again. (1)', __('Management Report'));
					if($this->ManagementReport->modelError)
					{
						$errMsg = $this->ManagementReport->modelError;
					}
					$this->Session->setFlash($errMsg);
				}
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be Finalized. Please, try again. (2)', __('Management Report')));
			}
		}
		
		// check items here
		if(!$this->ManagementReport->checkItems($id))
		{
			$errMsg = __('The %s could not be Finalized. Please, try again. (1)', __('Management Report'));
			if($this->ManagementReport->modelError)
			{
				$errMsg = $this->ManagementReport->modelError;
			}
			$this->Session->setFlash($errMsg);
			return $this->redirect(array('action' => 'view', $this->ManagementReport->id));
		}
		
		// checks to make sure at least 2 items are highlighted
		if(!$finalizedVars = $this->ManagementReport->finalizedVars($id))
		{
			$this->Session->setFlash($this->ManagementReport->modelError);
			return $this->redirect(array('action' => 'view', $this->ManagementReport->id));
		}
		
		$this->set($finalizedVars);
	}
	
	public function manager_add() 
	{
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$this->ManagementReport->create();
			$this->request->data['ManagementReport']['user_id'] = AuthComponent::user('id');
			
			if ($this->ManagementReport->addReport($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been saved', __('Management Report')));
				return $this->redirect(array('action' => 'view', $this->ManagementReport->id));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved Error: %s', __('Management Report'), $this->ManagementReport->modelError));
			}
		}
		else
		{
			$this->request->data = $this->ManagementReport->User->ManagementReportDefault->getDefaults(AuthComponent::user('id'), 'ManagementReport', true);
			
			$now = time();
			$this->request->data['ManagementReport']['report_date'] = date('Y-m-d H:i:s', $now);
			$this->request->data['ManagementReport']['report_date_start'] = date('Y-m-01 H:i:s', strtotime('-30 days', $now));
			$this->request->data['ManagementReport']['report_date_end'] = date('Y-m-t H:i:s', $now);
			
		}
		
		$this->set('item_sections', $this->ManagementReport->ManagementReportItem->getItemSections());
	}
	
	public function manager_edit($id = null) 
	{
		$this->ManagementReport->id = $id;
		if (!$this->ManagementReport->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		if (!$this->ManagementReport->isOwnedBy($id, AuthComponent::user('id'))) 
		{
			throw new ForbiddenException(__('You don\'t own this %s', __('Management Report')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ManagementReport->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The %s has been saved', __('Management Report')));
				return $this->redirect(array('action' => 'view', $id));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Management Report')));
			}
		}
		else
		{
			$this->ManagementReport->recursive = 1;
			$this->ManagementReport->contain(array('Tag'));
			$this->request->data = $this->ManagementReport->read(null, $id);
		}
		
		$this->set('item_sections', $this->ManagementReport->ManagementReportItem->getItemSections());
	}
	
	public function manager_defaults() 
	{
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$this->request->data['ManagementReportDefault']['user_id'] = AuthComponent::user('id');
			
			if ($this->ManagementReport->User->ManagementReportDefault->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The %s has been saved', __('Management Report Defaults')));
				return $this->redirect(array('controller' => 'management_reports', 'action' => 'index', 'manager' => true));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Management Report Defaults')));
			}
		}
		else
		{
			$this->request->data = $this->ManagementReport->User->ManagementReportDefault->getDefaults(AuthComponent::user('id'));
		}
	}

	public function manager_delete($id = null) 
	{
		$this->ManagementReport->id = $id;
		if (!$this->ManagementReport->exists()) {
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		if ($this->ManagementReport->delete()) {
			$this->Session->setFlash(__('%s deleted', __('Management Report')));
			return $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('%s was not deleted', __('Management Report')));
		return $this->redirect(array('action' => 'index'));
	}
}