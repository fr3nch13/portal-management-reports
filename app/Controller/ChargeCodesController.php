<?php
App::uses('AppController', 'Controller');
/**
 * ChargeCodes Controller
 *
 * @property ChargeCodes $ChargeCode
 */
class ChargeCodesController extends AppController 
{

	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		
		$this->ChargeCode->recursive = -1;
		$this->paginate['order'] = array('Charge Code.name' => 'asc');
		$this->paginate['conditions'] = $this->ChargeCode->conditions($conditions, $this->passedArgs); 
		$this->set('charge_codes', $this->paginate());
	}

	public function admin_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		
		$this->ChargeCode->recursive = -1;
		$this->paginate['order'] = array('Charge Code.name' => 'asc');
		$this->paginate['conditions'] = $this->ChargeCode->conditions($conditions, $this->passedArgs); 
		$this->set('charge_codes', $this->paginate());
	}
	
	public function admin_add() 
	{
		if ($this->request->is('post'))
		{
			$this->ChargeCode->create();
			
			if ($this->ChargeCode->saveAssociated($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been saved', __('Charge Code')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Charge Code')));
			}
		}
	}
	
	public function admin_edit($id = null) 
	{
		$this->ChargeCode->id = $id;
		if (!$this->ChargeCode->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Charge Code')));
		}
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ChargeCode->saveAssociated($this->request->data)) 
			{
				$this->Session->setFlash(__('The %s has been saved', __('Charge Code')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Charge Code')));
			}
		}
		else
		{
			$this->request->data = $this->ChargeCode->read(null, $id);
		}
	}

//
	public function admin_delete($id = null) 
	{
		$this->ChargeCode->id = $id;
		if (!$this->ChargeCode->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Charge Code')));
		}

		if ($this->ChargeCode->delete()) 
		{
			$this->Session->setFlash(__('%s deleted', __('Charge Code')));
			return $this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('%s was not deleted', __('Charge Code')));
		return $this->redirect($this->referer());
	}
}
