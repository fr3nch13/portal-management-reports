<?php
App::uses('AppModel', 'Model');
/**
 * Project Model
 *
 * @property User $User
 * @property ProjectStatus $ProjectStatus
 * @property ProjectStatusUser $ProjectStatusUser
 * @property ProjectUpdate $ProjectUpdate
 */
class Project extends AppModel 
{
	public $displayField = 'name';
	public $order = array('Project.created' => 'desc');

	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
		'request_date' => array(
			'datetime' => array(
				'rule' => array('datetime'),
			),
		),
		'target_date' => array(
			'datetime' => array(
				'rule' => array('datetime'),
			),
		),
	);

	public $belongsTo = array(
		'UserAddedProject' => array(
			'className' => 'User',
			'foreignKey' => 'added_user_id',
		),
		'UserModifiedProject' => array(
			'className' => 'User',
			'foreignKey' => 'modified_user_id',
		),
		'ProjectStatus' => array(
			'className' => 'ProjectStatus',
			'foreignKey' => 'project_status_id',
		),
		'ProjectStatusUser' => array(
			'className' => 'User',
			'foreignKey' => 'project_status_user_id',
		)
	);

	public $hasMany = array(
		'ProjectUpdate' => array(
			'className' => 'ProjectUpdate',
			'foreignKey' => 'project_id',
			'dependent' => true,
		),
		'ProjectFile' => array(
			'className' => 'ProjectFile',
			'foreignKey' => 'project_id',
			'dependent' => true,
		),
	);
	
	public $actsAs = array(
		'Tags.Taggable',
	);
	
	public $searchFields = array(
		'Project.name',
	);
	
	public function beforeSave($options = array()) 
	{
		if(isset($this->data[$this->alias]['project_status_id']) and !$this->data[$this->alias]['project_status_id'])
		{
			$this->data[$this->alias]['project_status_id'] = 0;
		}
		
		// track the user
		if(isset($this->data[$this->alias]['project_status_id']) and $this->data[$this->alias]['project_status_id'])
		{
			if($this->id)
			{
				if(isset($this->data[$this->alias]['modified_user_id']))
					$this->data[$this->alias]['project_status_user_id'] = $this->data[$this->alias]['modified_user_id'];
			}
			else
			{
				if(isset($this->data[$this->alias]['added_user_id']))
					$this->data[$this->alias]['project_status_user_id'] = $this->data[$this->alias]['added_user_id'];
			}
		}
		
		return parent::beforeSave($options);
	}
	
	public function afterSave($created = false, $options = array())
	{
		// create an initial update.
		$project_update_id = 0;
		if($this->id and $created)
		{
			$updateData = array(
				'project_id' => $this->id,
				'added_user_id' => (isset($this->data[$this->alias]['added_user_id'])?$this->data[$this->alias]['added_user_id']:0),
				'project_status_id' => (isset($this->data[$this->alias]['project_status_id'])?$this->data[$this->alias]['project_status_id']:0),
				'summary' => __('Project Created'),
			);
			
			$this->ProjectUpdate->create();
			$this->ProjectUpdate->data = $updateData;
			if($this->ProjectUpdate->save($this->ProjectUpdate->data))
			{
				$project_update_id = $this->ProjectUpdate->id;
			}
		}
		
		// process any attached files.
		// the Project model also has a copy of this
		if($this->id and isset($this->data['ProjectFile']['file']['error']) and $this->data['ProjectFile']['file']['error'] == 0)
		{
			$this->data['ProjectFile']['project_id'] = $this->id;
			$this->data['ProjectFile']['project_update_id'] = $project_update_id;
			$this->data['ProjectFile']['added_user_id'] = (isset($this->data[$this->alias]['added_user_id'])?$this->data[$this->alias]['added_user_id']:0);
			$this->data['ProjectFile']['nicename'] = '';
			$this->ProjectFile->create();
			$this->ProjectFile->data = array(
				'ProjectFile' => $this->data['ProjectFile'],
			);
			$this->ProjectFile->save($this->ProjectFile->data);
		}
		
		return parent::afterSave($created, $options);
	}
}
