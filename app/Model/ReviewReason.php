<?php
App::uses('AppModel', 'Model');

class ReviewReason extends AppModel 
{
	public $displayField = 'name';
	public $hasMany = array(
		'ReportItem' => array(
			'className' => 'ReportItem',
			'foreignKey' => 'review_reason_id',
			'dependent' => false,
		),
	);
}
