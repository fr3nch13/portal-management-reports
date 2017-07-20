<?php
App::uses('AppController', 'Controller');

class ProjectFileStatesController extends AppController 
{
	public $allowAdminDelete = true;

	public function admin_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		
		$this->ProjectFileState->recursive = -1;
		$this->paginate['order'] = array('ProjectFileState.name' => 'asc');
		$this->paginate['conditions'] = $this->ProjectFileState->conditions($conditions, $this->passedArgs); 
		$this->set('projectFileStates', $this->paginate());
	}
	
	public function admin_add() 
	{
		if ($this->request->is('post'))
		{
			$this->ProjectFileState->create();
			
			if ($this->ProjectFileState->save($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been saved', __('Project File')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Project File')));
			}
		}
	}
	
	public function admin_edit($id = null) 
	{
		if (!$projectFileState = $this->ProjectFileState->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s %s', __('Project'), __('File')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ProjectFileState->save($this->request->data)) 
			{
				$this->Session->setFlash(__('The %s has been saved', __('Project File')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Project File')));
			}
		}
		else
		{
			$this->request->data = $projectFileState;
		}
	}
}
