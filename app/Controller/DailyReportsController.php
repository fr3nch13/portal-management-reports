<?php
App::uses('AppController', 'Controller');

class DailyReportsController extends AppController 
{

	public function isAuthorized($user = [])
	{
		// All registered users can add and view equipment
		if (in_array($this->action, ['add', 'view', 'edit'])) 
		{
			return true;
		}
		
		return parent::isAuthorized($user);
	}
	
	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = [
			'DailyReport.user_id' => AuthComponent::user('id'),
		];
		
		$this->DailyReport->recursive = 0;
		$this->paginate['order'] = ['DailyReport.report_date' => 'desc'];
		$this->paginate['conditions'] = $this->DailyReport->conditions($conditions, $this->passedArgs); 
		$this->set('daily_reports', $this->paginate());
	}
	
	public function tag($tag_id = null)  
	{ 
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->DailyReport->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$this->Prg->commonProcess();
		
		$conditions = [
			'DailyReport.user_id' => AuthComponent::user('id'),
		];
		$conditions[] = $this->DailyReport->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'DailyReport');
		
		$this->paginate['order'] = ['DailyReport.report_date' => 'desc'];
		$this->paginate['conditions'] = $this->DailyReport->conditions($conditions, $this->passedArgs); 
		$this->set('daily_reports', $this->paginate());
	}
	
	public function view($id = false)
	{
		if (!$daily_report = $this->DailyReport->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Daily Report')));
		}
		
		$this->set('daily_report', $daily_report);
		$this->set('item_states', $this->DailyReport->DailyReportsReportItem->getItemStates());
	}
	
	public function finalize($id = false, $finalized = false)
	{
		$this->DailyReport->id = $id;
		if (!$this->DailyReport->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('Daily Report')));
		}
		
		if ($this->request->is('post'))
		{
			if($finalized)
			{
				if ($this->DailyReport->finalize($id))
				{
					$this->Flash->success(__('The %s has been Finalized', __('Daily Report')));
					return $this->redirect(['action' => 'view', $this->DailyReport->id]);
				}
				else
				{
					$errMsg = __('The %s could not be Finalized. Please, try again. (1)', __('Daily Report'));
					if($this->DailyReport->modelError)
					{
						$errMsg = $this->DailyReport->modelError;
					}
					$this->Flash->error($errMsg);
				}
			}
			else
			{
				$this->Flash->error(__('The %s could not be Finalized. Please, try again. (2)', __('Daily Report')));
			}
		}
		
		// check items here
		if(!$this->DailyReport->checkItems($id))
		{
			$errMsg = __('The %s could not be Finalized. Please, try again. (1)', __('Daily Report'));
			if($this->DailyReport->modelError)
			{
				$errMsg = $this->DailyReport->modelError;
			}
			$this->Flash->error($errMsg);
			return $this->redirect(array('action' => 'view', $this->DailyReport->id));
		}
		
		$this->set($this->DailyReport->finalizedVars($id));
	}
	
	public function add() 
	{
		if ($this->request->is('post'))
		{
			$this->DailyReport->create();
			$this->request->data['DailyReport']['user_id'] = AuthComponent::user('id');
			
			if ($this->DailyReport->addReport($this->request->data))
			{
				$this->Flash->success(__('The %s has been saved', __('Daily Report')));
				return $this->redirect(array('action' => 'view', $this->DailyReport->id));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Daily Report')));
			}
		}
		else
		{
			$this->request->data['DailyReport']['name'] = __('Work Out - %s - %s', AuthComponent::user('name'), date('m-d-Y'));
		}
		
		$this->set('item_states', $this->DailyReport->DailyReportsReportItem->getItemStates());
	}
	
	public function edit($id = null) 
	{
		$this->DailyReport->id = $id;
		if (!$this->DailyReport->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Daily Report')));
		}
		if (!$this->DailyReport->isOwnedBy($id, AuthComponent::user('id'))) 
		{
			throw new ForbiddenException(__('You don\'t own this %s', __('Daily Report')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->DailyReport->save($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved', __('Daily Report')));
				return $this->redirect(array('action' => 'view', $id));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Daily Report')));
			}
		}
		else
		{
			$this->DailyReport->recursive = 1;
			$this->DailyReport->contain(array('Tag'));
			$this->request->data = $this->DailyReport->read(null, $id);
		}
	}

//
	public function delete($id = null) 
	{
		$this->DailyReport->id = $id;
		if (!$this->DailyReport->exists()) {
			throw new NotFoundException(__('Invalid %s', __('Daily Report')));
		}
		if ($this->DailyReport->delete()) {
			$this->Flash->success(__('%s deleted', __('Daily Report')));
			return $this->redirect(array('action' => 'index'));
		}
		$this->Flash->error(__('%s was not deleted', __('Daily Report')));
		return $this->redirect(array('action' => 'mine'));
	}
//
	public function admin_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		
		$this->DailyReport->recursive = 0;
		$this->paginate['order'] = array('DailyReport.report_date' => 'desc');
		$this->paginate['conditions'] = $this->DailyReport->conditions($conditions, $this->passedArgs); 
		$this->set('daily_reports', $this->paginate());
	}
}