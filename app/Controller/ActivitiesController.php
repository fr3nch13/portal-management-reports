<?php
App::uses('AppController', 'Controller');
/**
 * Activities Controller
 *
 * @property Activities $Activity
 */
class ActivitiesController extends AppController 
{

	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		
		$this->Activity->recursive = -1;
		$this->paginate['order'] = array('Activity.name' => 'asc');
		$this->paginate['conditions'] = $this->Activity->conditions($conditions, $this->passedArgs); 
		$this->set('activities', $this->paginate());
	}

	public function admin_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		
		$this->Activity->recursive = -1;
		$this->paginate['order'] = array('Activity.name' => 'asc');
		$this->paginate['conditions'] = $this->Activity->conditions($conditions, $this->passedArgs); 
		$this->set('activities', $this->paginate());
	}
	
	public function admin_add() 
	{
		if ($this->request->is('post'))
		{
			$this->Activity->create();
			
			if ($this->Activity->saveAssociated($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been saved', __('Activity')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Activity')));
			}
		}
	}
	
	public function admin_edit($id = null) 
	{
		$this->Activity->id = $id;
		if (!$this->Activity->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Activity')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->Activity->saveAssociated($this->request->data)) 
			{
				$this->Session->setFlash(__('The %s has been saved', __('Activity')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Activity')));
			}
		}
		else
		{
			$this->request->data = $this->Activity->read(null, $id);
		}
	}

//
	public function admin_delete($id = null) 
	{
		$this->Activity->id = $id;
		if (!$this->Activity->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Activity')));
		}

		if ($this->Activity->delete()) 
		{
			$this->Session->setFlash(__('%s deleted', __('Activity')));
			return $this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('%s was not deleted', __('Activity')));
		return $this->redirect($this->referer());
	}
}
