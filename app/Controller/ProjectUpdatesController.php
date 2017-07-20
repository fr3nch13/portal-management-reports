<?php
App::uses('AppController', 'Controller');
/**
 * ProjectUpdates Controller
 *
 * @property ProjectUpdate $ProjectUpdate
 * @property PaginatorComponent $Paginator
 * @property FlashComponent $Flash
 * @property SessionComponent $Session
 */
class ProjectUpdatesController extends AppController 
{
	public function project($project_id = false) 
	{
		$this->Prg->commonProcess();
		
		if (!$project = $this->ProjectUpdate->Project->read(null, $project_id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Project')));
		}
		$this->set('project', $project);
		
		$conditions = array(
			'ProjectUpdate.project_id' => $project_id,
		);
		
		$this->ProjectUpdate->recursive = 0;
		$this->paginate['conditions'] = $this->ProjectUpdate->conditions($conditions, $this->passedArgs); 
		$this->set('projectUpdates', $this->paginate());
	}
	
	public function view($id = null) 
	{
		$this->ProjectUpdate->recursive = 0;
		if (!$projectUpdate = $this->ProjectUpdate->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s %s', __('Project'), __('Update')));
		}
		
		$this->set('projectUpdate', $projectUpdate);
		$this->layout = 'Utilities.ajax_nodebug';
	}
	
	public function add($project_id = false) 
	{
		if (!$project = $this->ProjectUpdate->Project->read(null, $project_id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Project')));
		}
		$this->set('project', $project);
		
		if ($this->request->is('post')) 
		{
			$this->ProjectUpdate->create();
			
			$this->request->data['ProjectUpdate']['project_id'] = $project_id;
			$this->request->data['ProjectUpdate']['added_user_id'] = AuthComponent::user('id');
			
			if ($this->ProjectUpdate->save($this->request->data)) 
			{
				$this->Flash->success(__('The %s %s has been saved.', __('Project'), __('Update')));
				if($project_id)
					return $this->redirect(array('controller' => 'projects', 'action' => 'view', $project_id));
				else
					return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Flash->error(__('The %s %s could not be saved. Please, try again.', __('Project'), __('Update')));
			}
		}
		
		$projectStatuses = $this->ProjectUpdate->ProjectStatus->find('list');
		$this->set(compact('projectStatuses'));
	}
	
	public function edit($id = null) 
	{
		if (!$projectUpdate = $this->ProjectUpdate->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s %s', __('Project'), __('Update')));
		}
		
		if ($this->request->is(array('post', 'put'))) 
		{
			$this->request->data['ProjectUpdate']['modified_user_id'] = AuthComponent::user('id');
			
			if ($this->ProjectUpdate->save($this->request->data)) 
			{
				$this->Flash->success(__('The %s %s has been saved.', __('Project'), __('Update')));
				return $this->redirect(array('controller' => 'projects', 'action' => 'view', $projectUpdate['ProjectUpdate']['project_id']));
			}
			else
			{
				$this->Flash->error(__('The %s %s could not be saved. Please, try again.', __('Project'), __('Update')));
			}
		}
		else
		{
			$this->request->data = $projectUpdate;
		}
	}
	
	public function delete($id = null) 
	{
		if (!$projectUpdate = $this->ProjectUpdate->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s %s', __('Project'), __('Update')));
		}
		
		$this->request->allowMethod('post', 'delete');
		
		if ($this->ProjectUpdate->delete()) 
		{
			$this->Flash->success(__('The %s %s has been deleted.', __('Project'), __('Update')));
		} 
		else 
		{
			$this->Flash->error(__('The %s %s could not be deleted. Please, try again.', __('Project'), __('Update')));
		}
		return $this->redirect(array('controller' => 'projects', 'action' => 'view', $projectUpdate['ProjectUpdate']['project_id']));
	}
}
