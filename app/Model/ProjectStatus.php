<?php
App::uses('AppModel', 'Model');
/**
 * ProjectStatus Model
 *
 * @property ProjectUpdate $ProjectUpdate
 * @property Project $Project
 */
class ProjectStatus extends AppModel 
{
	public $displayField = 'name';

	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
	);

	public $hasMany = array(
		'ProjectUpdate' => array(
			'className' => 'ProjectUpdate',
			'foreignKey' => 'project_status_id',
			'dependent' => false,
		),
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_status_id',
			'dependent' => false,
		)
	);

}
