<?php
// app/Controller/UsersController.php

class UsersController extends AppController
{
	public $allowAdminDelete = true;
	
	public function beforeFilter()
	{
		$this->Auth->allow(
			'logout',
			'admin_login',
			'admin_logout',
			'proctime'
			);
		return parent::beforeFilter();
	}

	public function login()
	{
		// have the OAuthClient component handle everything for this action
		return $this->OAuthClient->OAC_Login();
	}
	
	public function admin_login() 
	{
		return 	$this->login();
	}

	public function logout()
	{
		$this->Session->setFlash(__('You have successfully logged out.'));
		return $this->redirect($this->Auth->logout());
	}
	
	public function admin_logout() 
	{
		return 	$this->logout();
	}

	public function index()
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
		);
		$this->paginate['order'] = array('User.name' => 'asc');
		$this->paginate['conditions'] = $this->User->conditions($conditions, $this->passedArgs);
		$this->set('users', $this->paginate());
	}

	public function view($id = null)
	{
		$this->User->id = $id;
		if (!$this->User->exists())
		{
			throw new NotFoundException(__('Invalid User'));
		}
		
		$this->set('user', $this->User->read(null, $id));
	}

	public function edit()
	{
		$this->User->id = AuthComponent::user('id');
		$this->User->recursive = 0;
		if (!$user = $this->User->read(null, $this->User->id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		unset($user['User']['password']);
		
		if(isset($this->request->query['flashmsg']))
		{
			$this->Session->setFlash($this->request->query['flashmsg']);
		}
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if ($this->User->saveAssociated($this->request->data))
			{
				// update the Auth session data to reflect the changes
				if (isset($this->request->data['User']))
				{
					foreach($this->request->data['User'] as $k => $v)
					{
						if ($this->Session->check('Auth.User.'. $k))
						{
							$this->Session->write('Auth.User.'. $k, $v);
						}
					}
				}
				if (isset($this->request->data['UsersSetting']))
				{
					foreach($this->request->data['UsersSetting'] as $k => $v)
					{
						$this->Session->write('Auth.User.UsersSetting.'. $k, $v);
					}
				}
				
				$this->Session->setFlash(__('Your settings have been updated.'));
				// go back to this form 
				return $this->redirect(array('action' => 'edit'));
			}
			else
			{
				$this->Session->setFlash(__('We could not update your settings. Please, try again.'));
			}
		}
		else
		{
			$this->request->data = $user;
		}
	}

//
	public function avatar()
	{
	/*
	 * Allow a User to edit their own information
	 */
		$this->User->id = AuthComponent::user('id');
		if (!$this->User->exists())
		{
			throw new NotFoundException(__('Invalid User'));
		}
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if ($this->User->saveAssociated($this->request->data))
			{
				// update the Auth session data to reflect the changes
				if (isset($this->User->afterdata['User']))
				{
					foreach($this->User->afterdata['User'] as $k => $v)
					{
						if (SessionComponent::check('Auth.User.'. $k))
						{
							SessionComponent::write('Auth.User.'. $k, $v);
						}
					}
					if(isset($this->User->afterdata['User']['photo']))
					{
						SessionComponent::write('Auth.User.photo', $this->User->afterdata['User']['photo']);
					}
				}
				
				$this->Session->setFlash(__('Your avatar has been updated.'));
				// go back to this form 
				return $this->redirect(array('action' => 'edit'));
			}
			else
			{
				$this->Session->setFlash(__('We could not update your avatar. Please, try again.'));
			}
		}
		else
		{
			$this->User->recursive = 0;
			$this->request->data = $this->User->read(null, $this->User->id);
			unset($this->request->data['User']['password']);
		}
	}

	public function admin_index()
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->set('chargeCodes', $this->User->ChargeCode->typeFormList());
		
		$this->User->recursive = 0;
		$this->paginate['order'] = array('User.name' => 'asc');
		$this->paginate['conditions'] = $this->User->conditions($conditions, $this->passedArgs);
		$this->set('users', $this->paginate());
	}

	public function admin_view($id = null)
	{
		$this->User->id = $id;
		if (!$this->User->exists())
		{
			throw new NotFoundException(__('Invalid User'));
		}
		
		$this->User->recursive = 1;
		$this->set('user', $this->User->read(null, $id));
	}

	public function admin_edit($id = null)
	{
		$this->User->id = $id;
		$this->User->recursive = 0;
		if (!$user = $this->User->read(null, $this->User->id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		unset($user['User']['password']);
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if ($this->User->saveAssociated($this->request->data))
			{
				if($this->User->id == AuthComponent::user('id'))
				{
					// update the Auth session data to reflect the changes
					if (isset($this->request->data['User']))
					{
						foreach($this->request->data['User'] as $k => $v)
						{
							if ($this->Session->check('Auth.User.'. $k))
							{
								$this->Session->write('Auth.User.'. $k, $v);
							}
						}
					}
				}
				$this->Session->setFlash(__('The %s has been saved', __('User')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('User')));
			}
		}
		else
		{
			$this->request->data = $user;
		}
		$this->set('chargeCodes', $this->User->ChargeCode->typeFormList());
		
	}

	public function admin_toggle($field = null, $id = null)
	{
		if ($this->User->toggleRecord($id, $field))
		{
			$this->Session->setFlash(__('The %s has been updated.', __('User')));
		}
		else
		{
			$this->Session->setFlash($this->User->modelError);
		}
		
		return $this->redirect($this->referer());
	}
}