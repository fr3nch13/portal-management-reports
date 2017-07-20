<?php
App::uses('AppModel', 'Model');

class ProjectFileState extends AppModel 
{
	public $displayField = 'name';
	public $hasMany = array(
		'ProjectFile' => array(
			'className' => 'ProjectFile',
			'foreignKey' => 'project_file_state_id',
			'dependent' => false,
		),
	);
}
