<?php
App::uses('AppController', 'Controller');
/**
 * ReportItemFavorites Controller
 *
 * @property ReportItemFavorites $ReportItemFavorite
 */
class ReportItemFavoritesController extends AppController 
{
	public function index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array(
			'ReportItemFavorite.user_id' => AuthComponent::user('id'),
		);
		
		$this->paginate['order'] = array('ReportItemFavorite.name' => 'asc');
		$this->paginate['conditions'] = $this->ReportItemFavorite->conditions($conditions, $this->passedArgs); 
		$this->set('report_item_favorites', $this->paginate());
		
		$this->set('item_states', $this->ReportItemFavorite->getItemStates());
		$this->set('item_charge_codes', $this->ReportItemFavorite->ChargeCode->listForSortable());
		$this->set('item_activities', $this->ReportItemFavorite->Activity->listForSortable());
	}
	
	public function add() 
	{
		if ($this->request->is('post'))
		{
			$this->ReportItemFavorite->create();
			
			$this->request->data['ReportItemFavorite']['user_id'] = AuthComponent::user('id');
			if ($this->ReportItemFavorite->saveAssociated($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been saved', __('Favorite Report Item')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash(__('The %s could not be saved. Please, try again.', __('Favorite Report Item')));
			}
		}
		
		$this->set('item_states', $this->ReportItemFavorite->getItemStates());
		$this->set('charge_codes', $this->ReportItemFavorite->ChargeCode->listForSortable());
		$this->set('activities', $this->ReportItemFavorite->Activity->listForSortable());
	}

//
	public function delete($id = null) 
	{
		$this->ReportItemFavorite->id = $id;
		if (!$this->ReportItemFavorite->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Favorite Report Item')));
		}

		if ($this->ReportItemFavorite->delete()) 
		{
			$this->Session->setFlash(__('%s deleted', __('Favorite Report Item')));
			return $this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('%s was not deleted', __('Favorite Report Item')));
		return $this->redirect($this->referer());
	}
	
	public function favorite_index() 
	{
		if(!$this->request->is('ajax'))
		{
			throw new InternalErrorException(__('Request in not an Ajax request.'));
		}
		
		if(!isset($this->request->params['named']['model']))
		{
			throw new InternalErrorException(__('Unknown Object. (%s)', 1));
		}
		
		if(!$this->request->params['named']['model'])
		{
			throw new InternalErrorException(__('Unknown Object. (%s)', 2));
		}
		
		if(!isset($this->request->params['named']['ids']))
		{
			throw new InternalErrorException(__('Unknown IDs. (%s)', 1));
		}
		
		if(!$this->request->params['named']['ids'])
		{
			throw new InternalErrorException(__('Unknown IDs. (%s)', 2));
		}
		
		$this->set('report_item_favorites', $this->ReportItemFavorite->find('all', array(
			'conditions' => array(
				'ReportItemFavorite.user_id' => AuthComponent::user('id'),
			),
			'order' => array('ReportItemFavorite.item' => 'asc'),
		)));
		
		$this->set('model', $this->request->params['named']['model']);
		$this->set('ids', $this->request->params['named']['ids']);
		
		$this->set('item_states', $this->ReportItemFavorite->getItemStates());
		$this->set('item_charge_codes', $this->ReportItemFavorite->ChargeCode->listForSortable());
		$this->set('item_activities', $this->ReportItemFavorite->Activity->listForSortable());
		
		$this->layout = 'ajax_nodebug';
	}
	
	public function favorite_add()
	{
		if(!$this->request->is('ajax'))
		{
			throw new InternalErrorException(__('Request in not an Ajax request.'));
		}
		
		$results = false;
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if(!$results = $this->ReportItemFavorite->addToReport($this->request->data))
			{
				throw new InternalErrorException(__('Failed to Add Item. Reason: %s', $this->ReportItemFavorite->modelError));
			}
		}
		
		$this->set('results', $results);
		return $this->render('Utilities./Elements/gridedit', 'ajax_nodebug');
	}
}
