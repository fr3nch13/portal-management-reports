<?php
App::uses('AppController', 'Controller');
/**
 * ReviewReasons Controller
 *
 * @property ReviewReasons $ReviewReason
 */
class ReviewReasonsController extends AppController 
{

	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		
		$this->ReviewReason->recursive = -1;
		$this->paginate['order'] = array('ReviewReason.name' => 'asc');
		$this->paginate['conditions'] = $this->ReviewReason->conditions($conditions, $this->passedArgs); 
		$this->set('review_reasons', $this->paginate());
	}

	public function admin_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		
		$this->ReviewReason->recursive = -1;
		$this->paginate['order'] = array('ReviewReason.name' => 'asc');
		$this->paginate['conditions'] = $this->ReviewReason->conditions($conditions, $this->passedArgs); 
		$this->set('review_reasons', $this->paginate());
	}
	
	public function admin_add() 
	{
		if ($this->request->is('post'))
		{
			$this->ReviewReason->create();
			
			if ($this->ReviewReason->saveAssociated($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been saved', __('Review Reason')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Review Reason')));
			}
		}
	}
	
	public function admin_edit($id = null) 
	{
		$this->ReviewReason->id = $id;
		if (!$this->ReviewReason->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Review Reason')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ReviewReason->saveAssociated($this->request->data)) 
			{
				$this->Session->setFlash(__('The %s has been saved', __('Review Reason')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Review Reason')));
			}
		}
		else
		{
			$this->request->data = $this->ReviewReason->read(null, $id);
		}
	}

//
	public function admin_delete($id = null) 
	{
		$this->ReviewReason->id = $id;
		if (!$this->ReviewReason->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Review Reason')));
		}

		if ($this->ReviewReason->delete()) 
		{
			$this->Session->setFlash(__('%s deleted', __('Review Reason')));
			return $this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('%s was not deleted', __('Review Reason')));
		return $this->redirect($this->referer());
	}
}
