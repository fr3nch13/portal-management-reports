<?php
App::uses('AppController', 'Controller');

class WeeklyReportsController extends AppController 
{

	public function isAuthorized($user = array())
	{
		// All registered users can add and view equipment
		if (in_array($this->action, array('add', 'view', 'edit'))) 
		{
			return true;
		}
		
		return parent::isAuthorized($user);
	}
//
	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'WeeklyReport.user_id' => AuthComponent::user('id'),
		);
		
		$this->WeeklyReport->recursive = 0;
		$this->paginate['conditions'] = $this->WeeklyReport->conditions($conditions, $this->passedArgs); 
		$this->set('weekly_reports', $this->paginate());
	}
	
	public function tag($tag_id = null)  
	{ 
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->WeeklyReport->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'WeeklyReport.user_id' => AuthComponent::user('id'),
		);
		$conditions[] = $this->WeeklyReport->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'WeeklyReport');
		
		$this->paginate['conditions'] = $this->WeeklyReport->conditions($conditions, $this->passedArgs); 
		$this->set('weekly_reports', $this->paginate());
	}
	
	public function view($id = false)
	{
		if (!$weekly_report = $this->WeeklyReport->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Weekly Report')));
		}
		
		$this->set('weekly_report', $weekly_report);
		$this->set('item_states', $this->WeeklyReport->WeeklyReportsReportItem->getItemStates());
	}
	
	public function view_excel($id)
	{
		if (!$weekly_report = $this->WeeklyReport->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Weekly Report')));
		}
		
		$this->set('weekly_report', $weekly_report);
		$this->set('excel_html', $this->WeeklyReport->viewExcelFile($id));
	}
	
	public function finalize($id = false, $finalized = false)
	{
		$this->WeeklyReport->id = $id;
		if (!$this->WeeklyReport->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('Weekly Report')));
		}
		
		if ($this->request->is('post'))
		{
			if($finalized)
			{
				if ($this->WeeklyReport->finalize($id))
				{
					$this->Session->setFlash(__('The %s has been Finalized', __('Weekly Report')));
					return $this->redirect(array('action' => 'view', $this->WeeklyReport->id));
				}
				else
				{
					$errMsg = __('The %s could not be Finalized. Please, try again. (1)', __('Weekly Report'));
					if($this->WeeklyReport->modelError)
					{
						$errMsg = $this->WeeklyReport->modelError;
					}
					$this->Session->setFlash($errMsg);
				}
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be Finalized. Please, try again. (2)', __('Weekly Report')));
			}
		}
		
		// check items here
		if(!$this->WeeklyReport->checkItems($id))
		{
			$errMsg = __('The %s could not be Finalized. Please, try again. (1)', __('Weekly Report'));
			if($this->WeeklyReport->modelError)
			{
				$errMsg = $this->WeeklyReport->modelError;
			}
			$this->Session->setFlash($errMsg);
			return $this->redirect(array('action' => 'view', $this->WeeklyReport->id));
		}
		
		// checks to make sure at least 2 items are highlighted
		if(!$finalizedVars = $this->WeeklyReport->finalizedVars($id))
		{
			$this->Session->setFlash($this->WeeklyReport->modelError);
			return $this->redirect(array('action' => 'view', $this->WeeklyReport->id));
		}
		
		$this->set($finalizedVars);
	}
	
	public function add() 
	{
		if ($this->request->is('post'))
		{
			$this->WeeklyReport->create();
			
			$this->request->data['WeeklyReport'] = array_merge(
				$this->request->data['WeeklyReport'],
				$this->WeeklyReport->formDefaults(AuthComponent::user('name'), $this->request->data['WeeklyReport']['report_date'])
			);
			
			$this->request->data['WeeklyReport']['user_id'] = AuthComponent::user('id');
			
			if ($this->WeeklyReport->addReport($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been saved', __('Weekly Report')));
				return $this->redirect(array('action' => 'view', $this->WeeklyReport->id));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Weekly Report')));
			}
		}
		else
		{
			$this->request->data['WeeklyReport'] = $this->WeeklyReport->formDefaults(AuthComponent::user('name'));
		}
		
		$this->set('item_states', $this->WeeklyReport->WeeklyReportsReportItem->getItemStates());
	}
	
	public function import() 
	{
		if ($this->request->is('post'))
		{
			$this->WeeklyReport->create();
			
			$this->request->data['WeeklyReport'] = array_merge(
				$this->request->data['WeeklyReport'],
				$this->WeeklyReport->formDefaults(AuthComponent::user('name'), $this->request->data['WeeklyReport']['report_date'])
			);
			
			$this->request->data['WeeklyReport']['user_id'] = AuthComponent::user('id');
			
			if ($this->WeeklyReport->importReport($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been saved', __('Weekly Report')));
				return $this->redirect(array('action' => 'view', $this->WeeklyReport->id));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Cause: %s', __('Weekly Report'), $this->WeeklyReport->modelError));
			}
		}
		else
		{
			$this->request->data['WeeklyReport'] = $this->WeeklyReport->formDefaults(AuthComponent::user('name'));
		}
	}
	
	public function edit($id = null) 
	{
		$this->WeeklyReport->id = $id;
		if (!$this->WeeklyReport->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Weekly Report')));
		}
		
		if (!$this->WeeklyReport->isOwnedBy($id, AuthComponent::user('id'))) 
		{
			throw new ForbiddenException(__('You don\'t own this %s', __('Weekly Report')));
		}
		
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$this->request->data['WeeklyReport'] = array_merge(
				$this->request->data['WeeklyReport'],
				$this->WeeklyReport->formDefaults(AuthComponent::user('name'), $this->request->data['WeeklyReport']['report_date'])
			);
			
			if ($this->WeeklyReport->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The %s has been saved', __('Weekly Report')));
				return $this->redirect(array('action' => 'view', $this->WeeklyReport->id));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Weekly Report')));
			}
		}
		else
		{
			$this->WeeklyReport->recursive = 1;
			$this->WeeklyReport->contain(array('Tag'));
			$this->request->data = $this->WeeklyReport->read(null, $id);
		}
	}
	
//
	public function delete($id = null) 
	{
		$this->WeeklyReport->id = $id;
		if (!$this->WeeklyReport->exists()) {
			throw new NotFoundException(__('Invalid %s', __('Weekly Report')));
		}
		if ($this->WeeklyReport->delete()) {
			$this->Session->setFlash(__('%s deleted', __('Weekly Report')));
			return $this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('%s was not deleted', __('Weekly Report')));
		return $this->redirect(array('action' => 'mine'));
	}

	public function manager_report_range($start = false, $end = false) 
	{
		$this->Prg->commonProcess();
		
		if(!$start)
		{
			$start = date('Y-m-01 00:00:00');
		}
		
		if(!$end)
		{
			$end = date('Y-m-t 23:59:59');
		}
		
		// shift the start date to the previous saturday to include some other possible weekly reports
		$start_dow = date('w', strtotime($start));
		$end_dow = date('w', strtotime($end));
		
		if($start_dow != 6)
		{
			$start = date('Y-m-d 00:00:00', strtotime('previous saturday', strtotime($start)) );
		}
		if($end_dow < 5)
		{
			$end = date('Y-m-d 23:59:59', strtotime('next friday', strtotime($end)) );
		}
		// shift the end date to the next closest friday to include some other possible weekly reports
		
		$start = date('Y-m-d 00:00:00', strtotime($start));
		$end = date('Y-m-d 23:59:59', strtotime($end));
		
		$conditions = array(
			'WeeklyReport.finalized' => true,
			'WeeklyReport.report_date BETWEEN ? AND ?' => array($start, $end),
		);
		
		$this->WeeklyReport->recursive = 0;
		
		$weekly_reports = $this->WeeklyReport->find('all', array(
			'order' => array('WeeklyReport.report_date' => 'asc'),
			'conditions' => $this->WeeklyReport->conditions($conditions, $this->passedArgs),
		));
		
		foreach($weekly_reports as $i => $weekly_report)
		{
			if(!isset($weekly_report['WeeklyReport']['id'])) continue;
			
			$weekly_reports[$i]['WeeklyReport']['report_item_count'] = $this->WeeklyReport->WeeklyReportsReportItem->find('count', array(
				'recursive' => -1,
				'conditions' => array(
					'WeeklyReportsReportItem.weekly_report_id' => $weekly_report['WeeklyReport']['id'],
				),
			));
		}
		
		$this->set('weekly_reports', $weekly_reports);
		$this->layout = 'ajax_nodebug';
	}
//
	public function manager_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		
		$this->WeeklyReport->recursive = 0;
		$this->paginate['conditions'] = $this->WeeklyReport->conditions($conditions, $this->passedArgs); 
		$this->set('weekly_reports', $this->paginate());
	}
	
	public function manager_tag($tag_id = null)  
	{ 
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->WeeklyReport->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		$conditions[] = $this->WeeklyReport->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'WeeklyReport');
		
		$this->paginate['conditions'] = $this->WeeklyReport->conditions($conditions, $this->passedArgs); 
		$this->set('weekly_reports', $this->paginate());
	}
	
	public function manager_view($id = false)
	{
		$this->WeeklyReport->recursive = 0;
		if (!$weekly_report = $this->WeeklyReport->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Weekly Report')));
		}
		
		$this->set('weekly_report', $weekly_report);
		$this->set('item_states', $this->WeeklyReport->WeeklyReportsReportItem->getItemStates());
	}
	
	public function manager_view_excel($id)
	{
		if (!$weekly_report = $this->WeeklyReport->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Weekly Report')));
		}
		
		$this->set('weekly_report', $weekly_report);
		$this->set('excel_html', $this->WeeklyReport->viewExcelFile($id));
	}
	
	public function manager_edit($id = null) 
	{
		$this->WeeklyReport->id = $id;
		$this->WeeklyReport->recursive = 1;
		$this->WeeklyReport->contain(array('Tag', 'User'));
		
		if (!$weekly_report = $this->WeeklyReport->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Weekly Report')));
		}
		
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$this->request->data['WeeklyReport'] = array_merge(
				$this->request->data['WeeklyReport'],
				$this->WeeklyReport->formDefaults($weekly_report['User']['name'], $this->request->data['WeeklyReport']['report_date'])
			);
			
			if ($this->WeeklyReport->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The %s has been saved', __('Weekly Report')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Weekly Report')));
			}
		}
		else
		{
			$this->request->data = $weekly_report;
		}
	}
	
	public function manager_finalize($id = false, $finalized = false)
	{
		$this->WeeklyReport->id = $id;
		if (!$this->WeeklyReport->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('Weekly Report')));
		}
		
/* The manager can view the finalized version of the report, but not finalize it
		if ($this->request->is('post'))
		{
			if($finalized)
			{
				if ($this->WeeklyReport->finalize($id))
				{
					$this->Session->setFlash(__('The %s has been Finalized', __('Weekly Report')));
					return $this->redirect(array('action' => 'view', $this->WeeklyReport->id));
				}
				else
				{
					$errMsg = __('The %s could not be Finalized. Please, try again. (1)', __('Weekly Report'));
					if($this->WeeklyReport->modelError)
					{
						$errMsg = $this->WeeklyReport->modelError;
					}
					$this->Session->setFlash($errMsg);
				}
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be Finalized. Please, try again. (2)', __('Weekly Report')));
			}
		}
*/
		
		// check items here
		if(!$this->WeeklyReport->checkItems($id))
		{
			$errMsg = __('The %s could not be Finalized. Please, try again. (1)', __('Weekly Report'));
			if($this->WeeklyReport->modelError)
			{
				$errMsg = $this->WeeklyReport->modelError;
			}
			$this->Session->setFlash($errMsg);
			return $this->redirect(array('action' => 'view', $this->WeeklyReport->id));
		}
		
		// checks to make sure at least 2 items are highlighted
		if(!$finalizedVars = $this->WeeklyReport->finalizedVars($id))
		{
			$this->Session->setFlash($this->WeeklyReport->modelError);
			return $this->redirect(array('action' => 'view', $this->WeeklyReport->id));
		}
		
		$this->set($finalizedVars);
	}
}