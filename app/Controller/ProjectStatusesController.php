<?php
App::uses('AppController', 'Controller');
/**
 * ProjectStatuses Controller
 *
 * @property ProjectStatus $ProjectStatus
 * @property PaginatorComponent $Paginator
 * @property FlashComponent $Flash
 * @property SessionComponent $Session
 */
class ProjectStatusesController extends AppController 
{
	public $allowAdminDelete = true;
	
	public function view($id = null) 
	{
		if (!$projectStatus = $this->ProjectStatus->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s %s', __('Project'), __('Status')));
		}
		
		$this->set('projectStatus', $projectStatus);
	}
	
	public function admin_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->ProjectStatus->recursive = 0;
		$this->paginate['conditions'] = $this->ProjectStatus->conditions($conditions, $this->passedArgs); 
		$this->set('projectStatuses', $this->paginate());
	}
	
	public function admin_add() 
	{
		if ($this->request->is('post')) 
		{
			$this->ProjectStatus->create();
			if ($this->ProjectStatus->save($this->request->data)) 
			{
				$this->Flash->success(__('The %s %s has been saved.', __('Project'), __('Status')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Flash->error(__('The %s %s could not be saved. Please, try again.', __('Project'), __('Status')));
			}
		}
	}
	
	public function admin_edit($id = null) 
	{
		if (!$projectStatus = $this->ProjectStatus->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s %s', __('Project'), __('Status')));
		}
		
		if ($this->request->is(array('post', 'put'))) 
		{
			if ($this->ProjectStatus->save($this->request->data)) 
			{
				$this->Flash->success(__('The %s %s has been saved.', __('Project'), __('Status')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Flash->error(__('The %s %s could not be saved. Please, try again.', __('Project'), __('Status')));
			}
		}
		else
		{
			$this->request->data = $projectStatus;
		}
	}
}
