<?php
App::uses('AppModel', 'Model');

class ReportItemFavorite extends AppModel 
{

	public $displayField = 'item';
	
	public $validate = array(
		'item' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
//				'required' => true,
			),
		),
	);
	
	public $hasMany = array(
		'ReportItem' => array(
			'className' => 'ReportItem',
			'foreignKey' => 'report_item_favorite_id',
			'dependent' => false,
		),
	);
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'ChargeCode' => array(
			'className' => 'ChargeCode',
			'foreignKey' => 'charge_code_id',
		),
		'Activity' => array(
			'className' => 'Activity',
			'foreignKey' => 'activity_id',
		),
	);
	
	public $actsAs = array(
		'Utilities.Extractor', 
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'ReportItemFavorite.item',
	);
	
	public $toggleFields = array('week1', 'week2', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
	
	// valid actions to take against multiselect items
	public $multiselectOptions = array('delete', 'charge_code', 'activity');
	
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
		
		return parent::beforeSave($options);
	}
	
	public function getItemStates($index = false)
	{
	// pull the the DailyReportsReportItem model, so the list stays in one place
		
		return $this->ReportItem->DailyReportsReportItem->getItemStates($index);
	}
	
	public function addToReport($data = array())
	{
		$this->modelError = false;
		if(!is_array($data))
		{
			$this->modelError = __('No data given.');
			return false;
		}
		
		if(!isset($data['model']))
		{
			$this->modelError = __('Unknown Report type.');
			return false;
		}
		
		$model = $data['model'];
		
		if(!is_object($this->ReportItem->{$model}))
		{
			$this->modelError = __('Unknown Object.');
			return false;
		}
		
		if(!isset($data['ids']))
		{
			$this->modelError = __('Unknown Item relationships. (%s)', 1);
			return false;
		}
		
		if(!is_array($data['ids']))
		{
			$this->modelError = __('Unknown Item relationships. (%s)', 2);
			return false;
		}
		
		if(!$data['ids'])
		{
			$this->modelError = __('Unknown Item relationships. (%s)', 3);
			return false;
		}
		
		$allowed_ids = array('user_id', 'daily_report_id', 'weekly_report_id', 'management_report_id');
		$xref_ids = $data['ids'];
		
		foreach($xref_ids as $k => $xref_id)
		{
			if(!$xref_id) unset($xref_ids[$k]);
			if(!in_array($k, $allowed_ids)) unset($xref_ids[$k]);
		}
		
		if(!$xref_ids)
		{
			$this->modelError = __('Unknown Item relationships. (%s)', 4);
			return false;
		}
		
		if(isset($data['selectItemdata']['ReportItemFavorite']['items']))
		{
			if(!isset($data['ReportItemFavorite']['items']))
			{
				$data['ReportItemFavorite']['items'] = $data['selectItemdata']['ReportItemFavorite']['items'];
			}
			else
			{
				if(!is_array($data['ReportItemFavorite']['items']))
				{
					$data['ReportItemFavorite']['items'] = array($data['ReportItemFavorite']['items']);
				}
				$data['ReportItemFavorite']['items'] = array_merge($data['ReportItemFavorite']['items'], $data['selectItemdata']['ReportItemFavorite']['items']);
			}
		}
		
		if(!isset($data['ReportItemFavorite']['items']))
		{
			$this->modelError = __('No Items selected.');
			return false;
		}
		
		$item_ids = $data['ReportItemFavorite']['items'];
		
		foreach($item_ids as $k => $item_id)
		{
			if(!$item_id) unset($item_ids[$k]);
		}
		
		if(!$item_ids)
		{
			$this->modelError = __('No Items selected.');
			return false;
		}
		
		$addData = array();
		
		$favorites = $this->find('all', array(
			'conditions' => array(
				$this->alias.'.id' => $item_ids,
			),
		));
		
		$keep_keys = array(
			'charge_code_id', 'activity_id', 'item_state', 'item', 'item_keys', 'report_item_favorite_id',
			'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun',
		);
		foreach($favorites as $favorite)
		{
			$favorite = $favorite[$this->alias];
			// track for later possible use
			$favorite_id = $favorite['id'];
			$favorite['report_item_favorite_id'] = $favorite_id;
			
			foreach($favorite as $k => $v)
			{
				if(!in_array($k, $keep_keys)) unset($favorite[$k]);
			}
			
			$favorite = array_merge($favorite, $xref_ids);
			$addData[] = $favorite;
		}
		
		sort($addData);
		
		if(method_exists($this->ReportItem->{$model}, 'favoritesAdd'))
		{
			if(!$this->ReportItem->{$model}->favoritesAdd($addData))
			{
				$this->modelError = __('Error occured when trying to add favoeites.');
				if($this->ReportItem->{$model}->modelError)
				{
					$this->modelError = $this->ReportItem->{$model}->modelError;
				}
				return false;
			}
		}
		return true;
	}
	
	public function getMatchedFavorites($user_id = false, $date = false, $finderType = 'all', $type = 'day')
	{
		if(!$user_id)
			return [];
		if(!$date)
			$date = date('Y-m-d');
		
		$time = strtotime($date);
		
		$woy = date('W', $time);
		$week = 'week'. ($woy % 2?'1':'2');
		$dow = strtolower(date('D', $time));
		
		$conditions = [
			$this->alias.'.user_id' => $user_id,
		];
		
		if(in_array($type, ['day', 'week']))
		{
			$conditions[$this->alias.'.'.$week] = true;
		}
		if(in_array($type, ['day']))
		{
			$conditions[$this->alias.'.'.$dow] = true;
		}
		
		return $this->find($finderType, ['conditions' => $conditions]);
	}
}