<?php
App::uses('AppModel', 'Model');
/**
 * ProjectFile Model
 *
 * @property Project $Project
 * @property User $User
 */
class ProjectFile extends AppModel 
{
	public $displayField = 'filename';
	public $order = array('ProjectFile.created' => 'desc');
	
	public $validate = array(
		'project_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);
	
	public $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
		),
		'ProjectUpdate' => array(
			'className' => 'ProjectUpdate',
			'foreignKey' => 'project_update_id',
		),
		'UserAddedProjectFile' => array(
			'className' => 'User',
			'foreignKey' => 'added_user_id',
		),
		'UserModifiedProjectFile' => array(
			'className' => 'User',
			'foreignKey' => 'modified_user_id',
		),
		'ProjectFileState' => array(
			'className' => 'ProjectFileState',
			'foreignKey' => 'project_file_state_id',
		),
	);
	
	public $actsAs = array(
		'Tags.Taggable',
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'Project.name',
		'ProjectFile.nicename',
		'ProjectFile.filename',
		'ProjectFileState.name',
	);
	
	public $manageUploads = true;
	
	public $saved = false;
	
	public function beforeSave($options = array()) 
	{
		if(isset($this->data[$this->alias]['nicename']) and !trim($this->data[$this->alias]['nicename']))
		{
			if(isset($this->data[$this->alias]['filename']))
			{
				$nicename = $this->data[$this->alias]['filename'];
				if(stripos($nicename, '.') !== false)
				{
					$fileparts = explode('.', $nicename);
					array_pop($fileparts);
					$nicename = implode('.', $fileparts);
				}
				$nicename = Inflector::underscore($nicename);
				$nicename = Inflector::humanize($nicename);
				
				$this->data[$this->alias]['nicename'] = $nicename;
			}
		}
		return parent::beforeSave($options);
	}
}
