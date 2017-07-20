<?php
App::uses('AppController', 'Controller');
/**
 * ProjectFiles Controller
 *
 * @property ProjectFile $ProjectFile
 * @property PaginatorComponent $Paginator
 */
class ProjectFilesController extends AppController 
{	
	public function project($project_id = null) 
	{
		$this->Prg->commonProcess();
		
		if (!$project = $this->ProjectFile->Project->read(null, $project_id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Project')));
		}
		$this->set('project', $project);
		
		$conditions = array(
			'ProjectFile.project_id' => $project_id,
		);
		
		$this->ProjectFile->recursive = 0;
		$this->paginate['contain'] = array('ProjectFileState');
		$this->paginate['conditions'] = $this->ProjectFile->conditions($conditions, $this->passedArgs); 
		$this->paginate['order'] = array('ProjectFile.created' => 'desc');
		$this->set('projectFiles', $this->paginate());
	}
	
	public function view($id = null) 
	{
		$this->ProjectFile->recursive = 0;
		if (!$projectFile = $this->ProjectFile->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s %s', __('Project'), __('File')));
		}
		
		$this->set('projectFile', $projectFile);
		$this->layout = 'Utilities.ajax_nodebug';
	}
	
	public function add($project_id = null) 
	{
		if (!$project = $this->ProjectFile->Project->read(null, $project_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Project')));
		}
		$this->set('project', $project);
		
		if ($this->request->is('post')) 
		{
			$this->ProjectFile->create();
			
			$this->request->data['ProjectFile']['project_id'] = $project_id;
			$this->request->data['ProjectFile']['added_user_id'] = AuthComponent::user('id');
			
			if ($this->ProjectFile->save($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been saved.', __('Project File')));
				return $this->redirect(array('controller' => 'projects', 'action' => 'view', $project_id));
			}
			else
			{
				$this->Session->setFlash(__('The %s file could not be saved. Please, try again.', __('Project File')));
			}
		}
		$this->set('projectFileStates', $this->ProjectFile->ProjectFileState->find('list') );
	}
	
	public function edit($id = null) 
	{
		if (!$projectFile = $this->ProjectFile->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Project %s', __('File'))));
		}
		$this->set('projectFile', $projectFile);
		
		if ($this->request->is(array('post', 'put'))) 
		{
			$this->request->data['ProjectFile']['modified_user_id'] = AuthComponent::user('id');
			
			if ($this->ProjectFile->save($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been saved.', __('Project File')));
				return $this->redirect(array('controller' => 'projects', 'action' => 'view', $projectFile['ProjectFile']['project_id'], '#' => 'ui-tabs-2'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Project File')));
			}
		}
		else
		{
			$this->request->data = $projectFile;
		}
		$this->set('projectFileStates', $this->ProjectFile->ProjectFileState->find('list') );
	}
	
	public function delete($id = null)
	{
		if (!$projectFile = $this->ProjectFile->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Project %s', __('File'))));
		}
		$this->set('projectFile', $projectFile);
		
		if ($this->ProjectFile->delete())
		{
			$this->Session->setFlash(__('The %s has been deleted.', __('Project File')));
		}
		else
		{
			$this->Session->setFlash(__('The %s could not be deleted. Please, try again.', __('Project File')));
		}
		
		return $this->redirect(array('controller' => 'projects', 'action' => 'view', $projectFile['ProjectFile']['project_id']));
	}
}
