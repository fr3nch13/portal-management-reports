<?php
class LoginHistory extends AppModel 
{
	var $name = 'LoginHistory';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	// define the fields that can be searched
	public $searchFields = array(
		'LoginHistory.email',
		'LoginHistory.ipaddress',
		'LoginHistory.user_agent',
		'User.name',
	);
	
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		)
	);
	
	public function failedLogins($minutes = 5)
	{
	/*
	 * Gets a list of failed logins from a $minutes ago
	 */
		
		$minutes = '-'. $minutes. ' minutes';
		
		return $this->find('all', array(
			'recursive' => '0',
			'contain' => array('User'),
			'conditions' => array(
				'LoginHistory.success' => 0,
				'LoginHistory.timestamp >' => date('Y-m-d H:i:s', strtotime($minutes)),
			),
		));
	}
}