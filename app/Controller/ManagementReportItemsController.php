<?php
App::uses('AppController', 'Controller');
class ManagementReportItemsController extends AppController 
{
	public function manager_management_report($management_report_id = false, $item_section = false) 
	{
		$this->Prg->commonProcess();
		
		if (!$management_report = $this->ManagementReportItem->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$this->set('management_report', $management_report);
		$this->set('item_section', $item_section);
		
		$conditions = array(
			'ManagementReportItem.management_report_id' => $management_report_id,
		);
		
		if($item_section !== false)
		{
			$conditions['ManagementReportItem.item_section'] = $item_section;
		}
		
		$this->paginate['order'] = array(
			'ManagementReportItem.item_section' => 'desc', 
			'ManagementReportItem.item_order' => 'asc',
		);
		$this->paginate['conditions'] = $this->ManagementReportItem->conditions($conditions, $this->passedArgs);
		$management_report_items = $this->paginate();
		$this->set('management_report_items', $management_report_items);
		
		$item_sections = $this->ManagementReportItem->getItemSections();
		if(isset($item_sections['staff'])) unset($item_sections['staff']);
		if(isset($item_sections['completed'])) unset($item_sections['completed']);
		$this->set('item_sections', $item_sections);
	}
	
	public function manager_add($management_report_id = false)
	{
		if (!$management_report = $this->ManagementReportItem->ManagementReport->read(null, $management_report_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Management Report')));
		}
		
		$this->set('management_report', $management_report);
		
		if ($this->request->is('post'))
		{
			$this->ManagementReportItem->create();
			
			if ($this->ManagementReportItem->addToReport($management_report_id, $this->request->data))
			{
				$this->Session->setFlash(__('The %s %s have been saved', __('Management'), __('Report Items')));
				return $this->redirect(array('controller' => 'management_reports', 'action' => 'view', $management_report_id));
			}
			else
			{
				$this->Session->setFlash(__('The %s %s could not be saved. Please, try again.', __('Management'), __('Report Items')));
			}
		}
		$this->set('item_sections', $this->ManagementReportItem->getItemSections(false, true));
	}
	
	public function manager_edit($id = false)
	{
		$this->ManagementReportItem->recursive = 0;
		if (!$management_report_item = $this->ManagementReportItem->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s %s', __('Management'), __('Report Item')));
		}
		
		$this->set('management_report_item', $management_report_item);
		
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			if ($this->ManagementReportItem->save($this->request->data))
			{
				$this->Session->setFlash(__('The %s %s has been updated', __('Management'), __('Report Item')));
				return $this->redirect(array('controller' => 'management_reports', 'action' => 'view', $management_report_item['ManagementReport']['id']));
			}
			else
			{
				$this->Session->setFlash(__('The %s %s could not be saved. Please, try again.', __('Management'), __('Report Items')));
			}
		}
		else
		{
			$this->request->data = $management_report_item;
		}
		
		$this->set('item_sections', $this->ManagementReportItem->getItemSections(false, true));
	}
	
//
	public function manager_multiselect()
	{
		if(!$this->request->is('post'))
		{
			throw new MethodNotAllowedException();
		}
		
		// forward to a page where the user can choose a value
		$redirect = false;
		if(isset($this->request->data['multiple']))
		{
			$ids = array();
			foreach($this->request->data['multiple'] as $id => $selected) { if($selected) $ids[] = $id; }
			$this->request->data['multiple'] = $this->ManagementReportItem->find('list', array(
				'fields' => array('ManagementReportItem.id', 'ManagementReportItem.id'),
				'conditions' => array('ManagementReportItem.id' => $ids),
				'recursive' => -1,
			));
		}
		
		if($this->request->data['ManagementReportItem']['multiselect_option'] == 'charge_code')
		{
			$redirect = array('action' => 'multiselect_charge_code');
		}
		elseif($this->request->data['ManagementReportItem']['multiselect_option'] == 'multicharge_code')
		{
			$redirect = array('action' => 'multiselect_multicharge_code');
		}
		if($this->request->data['ManagementReportItem']['multiselect_option'] == 'activity')
		{
			$redirect = array('action' => 'multiselect_activity');
		}
		elseif($this->request->data['ManagementReportItem']['multiselect_option'] == 'multiactivity')
		{
			$redirect = array('action' => 'multiselect_multiactivity');
		}
		elseif($this->request->data['ManagementReportItem']['multiselect_option'] == 'delete')
		{
			$this->ManagementReportItem->deleteAll(array(
				$this->ManagementReportItem->alias.'.id' => array_keys($this->request->data['multiple']),
			));
			
			return $this->redirect($this->referer());
		}
		
		if($redirect)
		{
			Cache::write('Multiselect_'.$this->alias.'_'. AuthComponent::user('id'), $this->request->data, 'sessions');
			return $this->redirect($redirect);
		}
		
		if($this->ManagementReportItem->multiselect($this->request->data))
		{
			$this->Session->setFlash(__('The %s were updated for this %s.', __('Report Items'), __('Management Report')));
			return $this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('The %s were NOT updated for this %s.', __('Report Items'), __('Management Report')));
		$this->redirect($this->referer());
	}
}