<?php
// app/Model/User.php

App::uses('AppModel', 'Model');

class User extends AppModel
{
	public $name = 'User';
	
	public $displayField = 'name';
	
	public $validate = array(
		'email' => array(
			'required' => array(
				'rule' => array('email'),
				'message' => 'A valid email adress is required',
			)
		),
		'role' => array(
			'valid' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter a valid role',
				'allowEmpty' => false,
			),
		),
	);
	
	public $hasOne = array(
		'ManagementReportDefault' => array(
			'className' => 'ManagementReportDefault',
			'foreignKey' => 'user_id',
		),
		'UserSetting' => array(
			'className' => 'UserSetting',
			'foreignKey' => 'user_id',
		),
	);
	
	public $hasMany = array(
		'LoginHistory' => array(
			'className' => 'LoginHistory',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'ReportItem' => array(
			'className' => 'ReportItem',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'ReportItemFavorite' => array(
			'className' => 'ReportItemFavorite',
			'foreignKey' => 'user_id',
			'dependent' => false,
		),
		'DailyReport' => array(
			'className' => 'DailyReport',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'DailyReportsReportItem' => array(
			'className' => 'DailyReportsReportItem',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'WeeklyReport' => array(
			'className' => 'WeeklyReport',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'WeeklyReportsReportItem' => array(
			'className' => 'WeeklyReportsReportItem',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'ManagementReport' => array(
			'className' => 'ManagementReport',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'ManagementReportsReportItem' => array(
			'className' => 'ManagementReportsReportItem',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'UserAddedProject' => array(
			'className' => 'Project',
			'foreignKey' => 'added_user_id',
		),
		'UserModifiedProject' => array(
			'className' => 'Project',
			'foreignKey' => 'modified_user_id',
		),
		'ProjectStatusUser' => array(
			'className' => 'Project',
			'foreignKey' => 'project_status_user_id',
		),
		'UserAddedProjectUpdate' => array(
			'className' => 'ProjectUpdate',
			'foreignKey' => 'added_user_id',
		),
		'UserModifiedProjectUpdate' => array(
			'className' => 'ProjectUpdate',
			'foreignKey' => 'modified_user_id',
		),
		'UserAddedProjectFile' => array(
			'className' => 'ProjectFile',
			'foreignKey' => 'added_user_id',
		),
		'UserModifiedProjectFile' => array(
			'className' => 'ProjectFile',
			'foreignKey' => 'modified_user_id',
		),
	);
	
	public $belongsTo = array(
		'ChargeCode' => array(
			'className' => 'ChargeCode',
			'foreignKey' => 'charge_code_id',
			'plugin_snapshot' => true,
		),
	);
	
	public $actsAs = array(
		'Utilities.Email',
		'Utilities.Shell',
		'Snapshot.Stat' => array(
			'entities' => array(
				'all' => array(),
				'created' => array(),
				'modified' => array(),
				'active' => array(
					'conditions' => array(
						'User.active' => true,
					),
				),
			),
		),
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'User.name',
		'User.email',
	);
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('active', 'manager');
	
	// the path to the config file.
	public $configPath = false;
	
	// Any error relating to the config
	public $configError = false;
	
	// used to store info, because the photo name is changed.
	public $afterdata = false;
	
	public function beforeSave($options = array())
	{
		// hash the password before saving it to the database
		if (isset($this->data[$this->alias]['password']))
		{
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
		return parent::beforeSave($options);
	}
	
	public function loginAttempt($input = false, $success = false, $user_id = false)
	{
	/*
	 * Once a user is logged in, tack it in the database
	 */
		$email = false;
		if(isset($input['User']['email'])) 
		{
			$email = $input['User']['email'];
			if(!$user_id)
			{
				$user_id = $this->field('id', array('email' => $email));
			}
		}
		
		$data = array(
			'email' => $email,
			'user_agent' => env('HTTP_USER_AGENT'),
			'ipaddress' => env('REMOTE_ADDR'),
			'success' => $success,
			'user_id' => $user_id,
			'timestamp' => date('Y-m-d H:i:s'),
		);
		
		$this->LoginHistory->create();
		return $this->LoginHistory->save($data);
	}
	
	public function lastLogin($user_id = null)
	{
		if($user_id)
		{
			$this->id = $user_id;
			return $this->saveField('lastlogin', date('Y-m-d H:i:s'));
		}
		return false;
	}
	
	public function adminEmails()
	{
	/*
	 * Returns a list of emails address for admin users
	 */
		
		return $this->find('list', array(
			'conditions' => array(
				'User.active' => true,
				'User.role' => 'admin',
			),
			'fields' => array(
				'User.email',
			),
		));
	}
	
	public function managerEmails()
	{
		return $this->find('list', array(
			'conditions' => array(
				'User.active' => true,
				'User.manager' => 'true',
			),
			'fields' => array(
				'User.email',
			),
		));
	}
	
	public function userList($user_ids = array(), $recursive = 0)
	{
	/*
	 * Lists users out with the keys being the user_id
	 */
		// fill the user cache
		$_users = $this->find('all', array(
			'recursive' => $recursive,
			'conditions' => array(
				'User.id' => $user_ids,
			),
		));
		
		$users = array();
		
		foreach($_users as $user)
		{
			$user_id = $user['User']['id'];
			$users[$user_id] = $user; 
		}
		unset($_users);
		return $users;
	}
	
	public function cronDailyReminder()
	{
		// the current hour
		$hour = date('G');
		
		// users that have their email setting to the hour
		$users = $this->find('all', array(
			'recursive' => 0,
			'contain' => array('UserSetting'),
			'conditions' => array(
				'User.active' => true,
				'UserSetting.email_daily' => $hour,
			),
		));
		
		// check if they have a daily finalized today
		foreach($users as $user)
		{
			$send = false;
			$subject = false;
			
			// check if one was created for today
			$daily_report = $this->DailyReport->find('first', array(
				'conditions' => array(
					'DailyReport.user_id' => $user['User']['id'],
					'DailyReport.report_date >' => date('Y-m-d 00:00:00'),
					'DailyReport.report_date <' => date('Y-m-d 23:59:59'),
				),
			));
			
			// one hasn't been created for today
			if(!$daily_report)
			{
				$subject = __('Create and %s a %s for today.', __('Finalize'), __('Daily Report'));
	 	
	 			$this->Email_reset();
				$this->Email_set('to', $user['User']['email']);
				$this->Email_set('subject', $subject);
				$this->Email_set('body', $subject);
				$this->Email_set('emailFormat', 'text');
				
				$this->Email_execute();
				
				$this->shellOut( __('Sent %s a Created %s email reminder.', $user['User']['email'], __('Daily Report')) );
				
				continue;
			}
			
			// If it's not finalized, send an email
			if(!$daily_report['DailyReport']['finalized'])
			{
				$subject = __('%s the %s for today.', __('Finalize'), __('Daily Report'));
	 	
	 			$this->Email_reset();
				$this->Email_set('to', $user['User']['email']);
				$this->Email_set('subject', $subject);
				$this->Email_set('body', $subject);
				$this->Email_set('emailFormat', 'text');
				
				$this->Email_execute();
				
				$this->shellOut( __('Sent %s a Finalize %s email reminder.', $user['User']['email'], __('Daily Report')) );
			}
		}
	}
	
	public function cronWeeklyReminder()
	{
		// the current friday
		$hour = date('G');
		$friday = date('Y-m-d 00:00:00', strtotime('Friday'));

		// users that have their email setting to the hour
		$users = $this->find('all', array(
			'recursive' => 0,
			'contain' => array('UserSetting'),
			'conditions' => array(
				'User.active' => true,
				'UserSetting.email_weekly' => $hour,
			),
		));
		
		// check if they have a weekly finalized today
		foreach($users as $user)
		{
			$send = false;
			$subject = false;
			
			// check if one was created for today
			$weekly_report = $this->WeeklyReport->find('first', array(
				'conditions' => array(
					'WeeklyReport.user_id' => $user['User']['id'],
					'WeeklyReport.report_date' => $friday,
				),
			));
			
			// one hasn't been created for today
			if(!$weekly_report)
			{
				$subject = __('Create and %s a %s for this week.', __('Finalize'), __('Weekly Report'));
	 	
	 			$this->Email_reset();
				$this->Email_set('to', $user['User']['email']);
				$this->Email_set('subject', $subject);
				$this->Email_set('body', $subject);
				$this->Email_set('emailFormat', 'text');
				
				$this->Email_execute();
				
				$this->shellOut( __('Sent %s a Created %s email reminder.', $user['User']['email'], __('Weekly Report')) );
				
				continue;
			}
			
			// If it's not finalized, send an email
			if(!$weekly_report['WeeklyReport']['finalized'])
			{
				$subject = __('%s the %s for this week.', __('Finalize'), __('Weekly Report'));
	 	
	 			$this->Email_reset();
				$this->Email_set('to', $user['User']['email']);
				$this->Email_set('subject', $subject);
				$this->Email_set('body', $subject);
				$this->Email_set('emailFormat', 'text');
				
				$this->Email_execute();
				
				$this->shellOut( __('Sent %s a Finalize %s email reminder.', $user['User']['email'], __('Weekly Report')) );
			}
		}
	}
}

