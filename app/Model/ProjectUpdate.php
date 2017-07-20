<?php
App::uses('AppModel', 'Model');
/**
 * ProjectUpdate Model
 *
 * @property Project $Project
 * @property User $User
 * @property ProjectStatus $ProjectStatus
 */
class ProjectUpdate extends AppModel 
{

	public $displayField = 'summary';
	public $order = array('ProjectUpdate.created' => 'desc');

	public $validate = array(
		'project_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'summary' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
	);

	public $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
		),
		'UserAddedProjectUpdate' => array(
			'className' => 'User',
			'foreignKey' => 'added_user_id',
		),
		'UserModifiedProjectUpdate' => array(
			'className' => 'User',
			'foreignKey' => 'modified_user_id',
		),
		'ProjectStatus' => array(
			'className' => 'ProjectStatus',
			'foreignKey' => 'project_status_id',
		)
	);

	public $hasOne = array(
		'ProjectFile' => array(
			'className' => 'ProjectFile',
			'foreignKey' => 'project_update_id',
			'dependent' => true,
		),
	);
	
	public $actsAs = array(
		'Tags.Taggable',
	);
	
	public $searchFields = array(
		'ProjectUpdate.summary',
	);
	
	public $saved = false;
	
	public function beforeSave($options = array()) 
	{
		if(isset($this->data[$this->alias]['project_status_id']) and !$this->data[$this->alias]['project_status_id'])
		{
			$this->data[$this->alias]['project_status_id'] = 0;
		}
		return parent::beforeSave($options);
	}
	
	public function afterSave($created = false, $options = array())
	{
		/// update the project's status if it is set
		if(isset($this->data[$this->alias]['project_status_id']) and 
		$this->data[$this->alias]['project_status_id'] and 
		isset($this->data[$this->alias]['project_id']) and 
		$this->data[$this->alias]['project_id'])
		{
			$this->Project->id = $this->data[$this->alias]['project_id'];
			$this->Project->set('project_status_id', $this->data[$this->alias]['project_status_id']);
			$this->Project->save();
		}
		
		// process any attached files.
		// the Project model also has a copy of this
		if($this->id and !$this->saved and isset($this->data['ProjectFile']['file']['error']) and $this->data['ProjectFile']['file']['error'] == 0)
		{
			$this->data['ProjectFile']['project_id'] = (isset($this->data[$this->alias]['project_id'])?$this->data[$this->alias]['project_id']:0);
			$this->data['ProjectFile']['project_update_id'] = $this->id;
			$this->data['ProjectFile']['added_user_id'] = (isset($this->data[$this->alias]['added_user_id'])?$this->data[$this->alias]['added_user_id']:0);
			$this->data['ProjectFile']['nicename'] = '';
			$this->ProjectFile->create();
			$this->ProjectFile->data = array(
				'ProjectFile' => $this->data['ProjectFile'],
			);
			if($this->ProjectFile->save($this->ProjectFile->data))
			{
				$this->ProjectFile->saved = true;
			}
		}
		
		return parent::afterSave($created, $options);
	}
}
