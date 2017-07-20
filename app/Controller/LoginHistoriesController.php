<?php
App::uses('AppController', 'Controller');
/**
 * LoginHistories Controller
 *
 * @property LoginHistory $LoginHistory
 */
class LoginHistoriesController extends AppController 
{
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() 
	{
		$this->Prg->commonProcess();
		$this->LoginHistory->recursive = 0;
		$this->LoginHistory->contain('User');
		$this->paginate['order'] = array('LoginHistory.timestamp' => 'desc');
		$this->paginate['conditions'] = $this->LoginHistory->parseCriteria($this->passedArgs);
		$this->set('loginHistories', $this->paginate());
	}
	
	public function admin_user($user_id = false) 
	{
		$this->LoginHistory->User->id = $user_id;
		if (!$this->LoginHistory->User->exists()) 
		{
			throw new NotFoundException(__('Invalid user'));
		}
		$this->Prg->commonProcess();
		
		$this->paginate['order'] = array('LoginHistory.timestamp' => 'desc');
		$this->paginate['conditions'] = array_merge(array('LoginHistory.user_id' => $user_id), $this->LoginHistory->parseCriteria($this->passedArgs));
		$this->set('loginHistories', $this->paginate());
	}

/**
 * admin_delete method
 *
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) 
	{
		if (!$this->request->is('post')) 
		{
			throw new MethodNotAllowedException();
		}
		$this->LoginHistory->id = $id;
		if (!$this->LoginHistory->exists()) 
		{
			throw new NotFoundException(__('Invalid login history'));
		}
		if ($this->LoginHistory->delete()) 
		{
			$this->Session->setFlash(__('Login history deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Login history was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
