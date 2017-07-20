<?php
App::uses('AppModel', 'Model');

class ManagementReportDefault extends AppModel 
{
	
	public $displayField = 'title';
	
	public $validate = array(
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'status_title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'planned_title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'completed_title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'issues_title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'impact_title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'staff_title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
	);
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		)
	);
	
	public $stripFields = array(
		'created', 'modified'
	);
	
	public function getDefaults($user_id = false, $newalias = false, $strip = false)
	{
		$out = array();
		if($user_id)
		{
			$out = $this->find('first', array(
				'conditions' => array(
					$this->alias.'.user_id' => $user_id,
				),
			));
		}
		
		if(isset($out[$this->alias]))
		{
			if($strip)
			{
				foreach($out[$this->alias] as $field => $value)
				{
					if(in_array($field, $this->stripFields))
					{
						unset($out[$this->alias][$field]);
					}
				}
			}
			
			if($newalias)
			{
				$out[$newalias] = $out[$this->alias];
				unset($out[$this->alias]);
			}
		}
		
		return $out;
	}
}
