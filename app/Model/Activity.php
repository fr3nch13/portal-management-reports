<?php
App::uses('AppModel', 'Model');

class Activity extends AppModel 
{
	public $displayField = 'name';
	
	public $hasMany = array(
		'ReportItem' => array(
			'className' => 'ReportItem',
			'foreignKey' => 'activity_id',
			'dependent' => false,
		),
		'ReportItemFavorite' => array(
			'className' => 'ReportItemFavorite',
			'foreignKey' => 'activity_id',
			'dependent' => false,
		),
		'ManagementReportItem' => array(
			'className' => 'ManagementReportItem',
			'foreignKey' => 'activity_id',
			'dependent' => false,
		),
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'Activity.name',
	);
}
