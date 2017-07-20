<?php
App::uses('AppModel', 'Model');

class ChargeCode extends AppModel 
{
	public $displayField = 'name';
	
	public $hasMany = array(
		'ReportItem' => array(
			'className' => 'ReportItem',
			'foreignKey' => 'charge_code_id',
			'dependent' => false,
		),
		'ReportItemFavorite' => array(
			'className' => 'ReportItemFavorite',
			'foreignKey' => 'charge_code_id',
			'dependent' => false,
		),
		'ManagementReportItem' => array(
			'className' => 'ManagementReportItem',
			'foreignKey' => 'charge_code_id',
			'dependent' => false,
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'charge_code_id',
			'dependent' => false,
		),
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'ChargeCode.name',
	);
}
