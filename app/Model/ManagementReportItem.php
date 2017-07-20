<?php
App::uses('AppModel', 'Model');

class ManagementReportItem extends AppModel 
{
	public $displayField = 'item';
	
	public $hasOne = array(
		'ManagementReportItemChild' => array(
			'className' => 'ManagementReportItem',
			'foreignKey' => 'management_report_item_id',
		)
	);
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'ManagementReport' => array(
			'className' => 'ManagementReport',
			'foreignKey' => 'management_report_id',
		),
		'ManagementReportItemParent' => array(
			'className' => 'ManagementReportItem',
			'foreignKey' => 'management_report_item_id',
		),
		'ChargeCode' => array(
			'className' => 'ChargeCode',
			'foreignKey' => 'charge_code_id',
		),
		'Activity' => array(
			'className' => 'Activity',
			'foreignKey' => 'activity_id',
		)
	);
	
	public $actsAs = array(
		'Tags.Taggable', 
		'Utilities.Extractor', 
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'ManagementReportItem.item',
	);
	
	// valid actions to take against multiselect items
	public $multiselectOptions = array('delete', 'charge_code', 'activity');
	
	public $saveManyIds = array();
	public $saveManyXref = array();
	public $saveManyXrefs = array();
	
	public $item_sections = array(
		'staff' => 'Staff Highlighted',
		'completed' => 'Staff Completed',
		'status' => 'Status',
		'planned' => 'Planned',
		'issues' => 'Issues',
		'impact' => 'Impact',
	);
	
	public function beforeSave($options = array())
	{
		// scan for keywords
		if(isset($this->data[$this->alias]['item']))
		{
			$this->data[$this->alias]['item'] = trim($this->data[$this->alias]['item'], '- ');
			
			$keywords = $this->extractKeywords($this->data[$this->alias]['item']);
			
			usort($keywords, function($a, $b) {
				return strlen($b) - strlen($a);
			});
			
			$this->data[$this->alias]['item_keys'] = implode(' ', $keywords);
		}
		
		if(isset($this->data[$this->alias]['item_date']))
		{
			$this->data[$this->alias]['item_date'] = date('Y-m-d H:i:s', strtotime($this->data[$this->alias]['item_date']));
		}
		
		// save/cache/pass this info for later reference
		$this->saveManyXref = $this->data[$this->alias];
		
		return parent::beforeSave($options);
	}
	
	public function afterSave($created = false, $options = array())
	{
		if($this->id)
		{
			$this->saveManyIds[$this->id] = $this->id;
			$this->saveManyXrefs[$this->id] = $this->saveManyXref;
		}
	}
	
	public function getItemSections($index = false, $exclude_staff = false)
	{
		$item_sections = $this->item_sections;
		if($index === false) 
		{
			if($exclude_staff)
			{
				if(isset($item_sections['staff'])) unset($item_sections['staff']);
				if(isset($item_sections['completed'])) unset($item_sections['completed']);
			}
			return $item_sections;
		}
		
		if(isset($item_sections[$index])) return $item_sections[$index];
		
		return false;
	}
	
	public function importFromLastReport($management_report_id = false, $user_id = false, $item_sections = array(), $management_report_data = array())
	{
		if(!$management_report_id) return false;
		if(!$user_id) return false;
		if(!$item_sections) return false;
		
		// find the last report
		$management_report_date = date('Y-m-d H:i:s');
		
		if(isset($management_report_data['report_date']))
		{
			$management_report_date = $management_report_data['report_date'];
		}
		
		if(!$last_items = $this->find('all', array(
			'recursive' => 0,
			'contain' => array('ManagementReport'),
			'conditions' => array(
					'ManagementReport.user_id' => $user_id,
					'ManagementReport.finalized' => true,
					'ManagementReport.report_date <' => $management_report_date,
					$this->alias. '.item_section' => $item_sections
			),
		)))
		{
			return false;
		}
		
		
		$management_report_item_schema = $this->schema();
		$management_report_item_fields = array_keys($management_report_item_schema);
		$remove_fields = array('id', 'created', 'modified');
		foreach($management_report_item_fields as $i => $management_report_item_field)
		{
			if(in_array($management_report_item_field, $remove_fields)) unset($management_report_item_fields[$i]);
		}
		
		$import_items = array();
		
		$item_orders = array();
		foreach($item_sections as $item_section_key => $item_section_name)
		{
			$item_order = 0;
			// find out how many are in there, and make the order that count, the order will be updated below
			if($item_order = $this->find('count', array(
				'conditions' => array(
					$this->alias. '.management_report_id' => $management_report_id,
					$this->alias. '.item_section' => $item_section_key,
				),
			)))
			{
				$item_order++;
			}
			else
			{
				$item_order = 0;
			}
			$item_orders[$item_section_key] = $item_order;
		}
		
		foreach($last_items as $last_item)
		{
			$item = array();
			foreach($last_item[$this->alias] as $field => $value)
			{
				if(!in_array($field, $management_report_item_fields)) continue;
				$item[$field] = $value;
			}
			
			$item['management_report_id'] = $management_report_id;
			$item_section = $item['item_section'];
			
			$item['item_order'] = $item_orders[$item_section];
			$item_orders[$item_section]++;
			
			if(isset($management_report_data['report_date']))
			{
				$item['item_date'] = $management_report_data['report_date'];
			}
			
			$item['management_report_item_id'] = $last_item[$this->alias]['id'];
			
			$import_items[] = $item;
		}
		
		if($import_items)
		{
			return $this->saveMany($import_items);
		}
		return true;
	}
	
	public function addToReport($management_report_id = false, $data = array())
	{
		$this->modelError = false;
		
		if(!$management_report_id) return false;
		if(!$data) return false;
		if(!isset($data[$this->alias])) return false;
		
		if(!$management_report = $this->ManagementReport->read(null, $management_report_id))
		{
			$this->modelError = __('Unknown %s', __('Management Report'));
			return false;
		}
		
		$management_report_item_schema = $this->schema();
		$management_report_item_fields = array_keys($management_report_item_schema);
		$remove_fields = array('id', 'created', 'modified');
		foreach($management_report_item_fields as $i => $management_report_item_field)
		{
			if(in_array($management_report_item_field, $remove_fields)) unset($management_report_item_fields[$i]);
		}
		
		$user_id = $management_report['ManagementReport']['user_id'];
		$item_date = $management_report['ManagementReport']['report_date'];
		
		$item_sections = $this->getItemSections();
		
		$all_items = array();
		// look for the different sections
		foreach($data[$this->alias] as $field => $items)
		{
			if(!preg_match('/_items$/i', $field)) continue;
			list($item_section,) = explode('_', $field);
			
			if(!isset($item_sections[$item_section])) continue;
			
			if(is_string($items))
			{
				// clean them up
				$items = trim($items);
				$items = explode("\n", $items);
				foreach($items as $i => $item)
				{
					$items[$i] = trim($items[$i]);
					if(!$items[$i]) unset($items[$i]);
				}
			}
			
			if(!is_array($items)) continue;
			
			
			$item_order = 0;
			// find out how many are in there, and make the order that count, the order will be updated below
			if($item_order = $this->find('count', array(
				'conditions' => array(
					$this->alias. '.management_report_id' => $management_report_id,
					$this->alias. '.item_section' => $item_section,
				),
			)))
			{
				$item_order++;
			}
			else
			{
				$item_order = 0;
			}
			
			foreach($items as $i => $item)
			{
				$this_item = $item;
				
				$item = array();
				foreach($management_report_item_fields as $management_report_item_field)
				{
					$item[$management_report_item_field] = false;
				}
				$item['item'] = $this_item;
				$item['user_id'] = $user_id;
				$item['item_date'] = $item_date;
				$item['item_order'] = $item_order;
				$item['item_section'] = $item_section;
				$item['management_report_id'] = $management_report_id;
				
				$all_items[] = $item;
				
				$item_order++;
			}
		}
		
		return $this->saveMany($all_items);
	}
}
