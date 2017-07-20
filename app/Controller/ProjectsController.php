<?php
App::uses('AppController', 'Controller');
/**
 * Projects Controller
 *
 * @property Project $Project
 * @property PaginatorComponent $Paginator
 * @property FlashComponent $Flash
 * @property SessionComponent $Session
 */
class ProjectsController extends AppController 
{
	public $allowAdminDelete = true;

	public function index($project_status_id = false) 
	{
		$this->Prg->commonProcess();
		
		$projectStatus = false;
		if ($project_status_id and !$projectStatus = $this->Project->ProjectStatus->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s %s', __('Project'), __('Status')));
		}
		$this->set('projectStatus', $projectStatus);
		
		$conditions = array();
		
		if($project_status_id)
		{
			$coditions['Project.project_status_id'] = $project_status_id;
		}
		
		$this->Project->recursive = 0;
		$this->paginate['conditions'] = $this->Project->conditions($conditions, $this->passedArgs); 
		$this->set('projects', $this->paginate());
		
		$projectStatuses = $this->Project->ProjectStatus->find('list');
		$this->set(compact('projectStatuses'));
	}
	
	public function tag($tag_id = null)  
	{ 
		if (!$tag_id) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		
		$tag = $this->Project->Tag->read(null, $tag_id);
		if (!$tag) 
		{
			throw new NotFoundException(__('Invalid %s', __('Tag')));
		}
		$this->set('tag', $tag);
		
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$conditions[] = $this->Project->Tag->Tagged->taggedSql($tag['Tag']['keyname'], 'Project');
		
		$this->Project->recursive = 0;
		$this->paginate['conditions'] = $this->Project->conditions($conditions, $this->passedArgs); 
		$this->set('projects', $this->paginate());
	}
	
	public function view($id = null) 
	{
		$this->Project->recursive = 0;
		if (!$project = $this->Project->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Project')));
		}
		
		$this->set('project', $project);
	}
	
	public function add($project_status_id = false) 
	{
		$projectStatus = false;
		if ($project_status_id and !$projectStatus = $this->Project->ProjectStatus->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s %s', __('Project'), __('Status')));
		}
		$this->set('projectStatus', $projectStatus);
		
		if ($this->request->is('post')) 
		{
			$this->request->data['Project']['added_user_id'] = AuthComponent::user('id');
			
			$this->Project->create();
			if ($this->Project->save($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved.', __('Project')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Project')));
			}
		}
		
		$projectStatuses = $this->Project->ProjectStatus->find('list');
		$this->set(compact('projectStatuses'));
	}
	
	public function edit($id = null) 
	{
		if (!$project = $this->Project->read(null, $id)) 
		{
			throw new NotFoundException(__('Invalid %s', __('Project')));
		}
		
		if ($this->request->is(array('post', 'put'))) 
		{
			$this->request->data['Project']['modified_user_id'] = AuthComponent::user('id');
			
			if ($this->Project->save($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved.', __('Project')));
				return $this->redirect(array('action' => 'view', $this->Project->id));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Project')));
			}
		} 
		else 
		{
			$this->request->data = $project;
		}
		
		$projectStatuses = $this->Project->ProjectStatus->find('list');
		$this->set(compact('projectStatuses'));
	}
}
