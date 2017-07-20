<?php

class CronShell extends AppShell
{
	// the models to use
	public $uses = array('User', 'LoginHistory', 'Dblogger.Dblog', 'ReviewState');
	
	public function startup() 
	{
		$this->clear();
		$this->out('Cron Shell');
		$this->hr();
		return parent::startup();
	}
	
	public function getOptionParser()
	{
	/*
	 * Parses out the options/arguments.
	 * http://book.cakephp.org/2.0/en/console-and-shells.html#configuring-options-and-generating-help
	 */
	
		$parser = parent::getOptionParser();
		
		$parser->description(__d('cake_console', 'The Cron Shell runs all needed cron jobs'));
		
		$parser->addSubcommand('failed_logins', array(
			'help' => __d('cake_console', 'Emails a list of failed logins to the admins and users every 10 minutes'),
			'parser' => array(
				'options' => array(
					'minutes' => array(
						'help' => __d('cake_console', 'Change the time frame from 10 minutes ago.'),
						'short' => 'm',
						'default' => 10,
					),
				),
			),
		));
		
		$parser->addSubcommand('daily_reminder', array(
			'help' => __d('cake_console', 'Send an email to a user if they haven\'t %s their %s at their selected hour.', __('finalized'), __('Daily Report')),
		));
		
		$parser->addSubcommand('weekly_reminder', array(
			'help' => __d('cake_console', 'Send an email to a user if they haven\'t %s their %s at their selected hour.', __('finalized'), __('Weekly Report')),
		));
		
		return $parser;
	}
	
	public function failed_logins()
	{
	/*
	 * Emails a list of failed logins to the admins every 5 minutes
	 * Only sends an email if there was a failed login
	 * Everything is taken care of in the Task
	 */
		$FailedLogins = $this->Tasks->load('Utilities.FailedLogins')->execute($this);
	}
	
	public function daily_reminder()
	{
		$results = $this->User->cronDailyReminder();
	}
	
	public function weekly_reminder()
	{
		$results = $this->User->cronWeeklyReminder();
	}
	
	public function change_log()
	{
	/*
	 * Sends an email when a change is made
	 * Send an email to whomever is involved with the change
	 */
		$minutes = '5';
		if(isset($this->args[0]))
		{
			$minutes = $this->args[0];
		}
		
		/////////// get the list of changes
		$logs = $this->Dblog->latest($minutes);
		if(!$logs)
		{
			$this->out(__('No logged changes'));
			return false;
		}
		
		$this->out(__('Found %s logged changes.', count($logs)), 1, Shell::QUIET);
		
		// list of models that is considered a user we can email
//		$userModels = array('EquipmentAddedUser', 'EquipmentVerifiedUser', 'ChainReceivedUser', 'ChainReleasedUser');
		
		// build a cache of users
		$user_cache = array();
		$user_ids = array();
		
		
		/////////// add the user_info to the user_cache
		foreach($logs as $log)
		{
			// only email changes when a media or custody chain is affected
			if(!in_array($log['Dblog']['model'], array('Equipment', 'EquipmentDetail', 'ExceptionUpdate'))) continue;
			if(isset($log['Equipment']))
			{
				$user_ids[$log['Equipment']['added_user_id']] = $log['Equipment']['added_user_id'];
				$user_ids[$log['Equipment']['modified_user_id']] = $log['Equipment']['modified_user_id'];
				$user_ids[$log['Equipment']['verified_user_id']] = $log['Equipment']['verified_user_id'];
				
			}
			if(isset($log['ExceptionUpdate']))
			{
				$user_ids[$log['ExceptionUpdate']['released_user_id']] = $log['ExceptionUpdate']['released_user_id'];
				$user_ids[$log['ExceptionUpdate']['verified_user_id']] = $log['ExceptionUpdate']['verified_user_id'];
				$user_ids[$log['ExceptionUpdate']['added_user_id']] = $log['ExceptionUpdate']['added_user_id'];
			}
		}
		
		$user_cache = $this->User->changeLogList($user_ids);
		
		/////////// sort the logs into 1 of 3 different types: created, updated, closed
		$logs_created = array();
		$logs_updated = array();
//		$logs_closed = array();
		foreach($logs as $log)
		{	
			// only email changes when a media or custody chain is affected
			if(!in_array($log['Dblog']['model'], array('Equipment', 'EquipmentDetail', 'ExceptionUpdate'))) continue;			
			
			// create a log key
			$log_key = $log['Dblog']['model']. '-'. $log['Dblog']['model_id']. '-'. $log['Dblog']['id'];
			
			// attach the EquipmentAddedUser
			if(!isset($log['EquipmentAddedUser']))
			{
				$log['EquipmentAddedUser'] = array();
				
				if(isset($log['Equipment']['added_user_id']) and $log['Equipment']['added_user_id'] > 0)
				{
					if(!isset($user_cache[$log['Equipment']['added_user_id']]))
					{
						$user_cache[$log['Equipment']['added_user_id']] = array();
						$user = $this->User->find('first', array(
							'recursive' => 0,
							'conditions' => array(
								'User.id' => $log['Equipment']['added_user_id'],
							),
						));
						if($user) $user_cache[$log['Equipment']['added_user_id']] = $user;
					}
					$log['EquipmentAddedUser'] = $user_cache[$log['Equipment']['added_user_id']]['User'];
				}
			}
			
			// attach the EquipmentVerifiedUser
			if(!isset($log['EquipmentVerifiedUser']))
			{
				$log['EquipmentVerifiedUser'] = array();
				
				if(isset($log['Equipment']['verified_user_id']) and $log['Equipment']['verified_user_id'] > 0)
				{
					if(!isset($user_cache[$log['Equipment']['verified_user_id']]))
					{
						$user_cache[$log['Equipment']['verified_user_id']] = array();
						$user = $this->User->find('first', array(
							'recursive' => 0,
							'conditions' => array(
								'User.id' => $log['Equipment']['verified_user_id'],
							),
						));
						if($user) $user_cache[$log['Equipment']['verified_user_id']] = $user;
					}
					$log['EquipmentVerifiedUser'] = $user_cache[$log['Equipment']['verified_user_id']]['User'];
				}
			}
			
			// attach the EquipmentModifiedUser
			if(!isset($log['EquipmentModifiedUser']))
			{
				$log['EquipmentModifiedUser'] = array();
				
				if(isset($log['Equipment']['modified_user_id']) and $log['Equipment']['modified_user_id'] > 0)
				{
					if(!isset($user_cache[$log['Equipment']['modified_user_id']]))
					{
						$user_cache[$log['Equipment']['modified_user_id']] = array();
						$user = $this->User->find('first', array(
							'recursive' => 0,
							'conditions' => array(
								'User.id' => $log['Equipment']['modified_user_id'],
							),
						));
						if($user) $user_cache[$log['Equipment']['modified_user_id']] = $user;
					}
					$log['EquipmentModifiedUser'] = $user_cache[$log['Equipment']['modified_user_id']]['User'];
				}
			}
			
			// track all user_ids for this entry
			$log['user_ids'] = array();
			foreach($log as $modelName => $modelValues)
			{
				// if email is set, and an id is set, it's a user
				if(isset($modelValues['email']) and isset($modelValues['id']))
				{
					$log['user_ids'][$modelValues['id']] = $modelValues['id'];
				}
			}
			
			// map the fields
			$log = $this->Dblog->mapFields($log);
			
			// new entries
			if($log['Dblog']['new'] == 1)
			{
				$logs_created[$log_key] = $log;
				continue;
			}
			
			// closed entries
			$changes = unserialize($log['Dblog']['changes']);
/*
			if(isset($changes['state']) and $changes['state'] == 0)
			{
				$logs_closed[$log_key] = $log;
				continue;
			}
*/
			
			// updated entries
			$logs_updated[$log_key] = $log;
		}
		
		/////////// seperate the users into groups based on their settings
		// list of users that want emails all of the time when created
		$users_created_all = array();
		// list of users that want emails only when mentioned
		$users_created_mentioned = array();
		// list of users that want emails all of the time when updated
		$users_updated_all = array();
		// list of users that want emails only when mentioned
		$users_updated_mentioned = array();
		// list of users that want emails all of the time when closed
//		$users_closed_all = array();
		// list of users that want emails only when mentioned
//		$users_closed_mentioned = array();
		
		foreach($user_cache as $user_id => $user)
		{
			if($user['UserSetting']['email_new'] == 2) $users_created_all[$user_id] = $user['User'];
			if($user['UserSetting']['email_new'] == 1) $users_created_mentioned[$user_id] = $user['User'];
			if($user['UserSetting']['email_updated'] == 2) $users_updated_all[$user_id] = $user['User'];
			if($user['UserSetting']['email_updated'] == 1) $users_updated_mentioned[$user_id] = $user['User'];
//			if($user['UserSetting']['email_closed'] == 2) $users_closed_all[$user_id] = $user['User'];
//			if($user['UserSetting']['email_closed'] == 1) $users_closed_mentioned[$user_id] = $user['User'];
		}
		
		// build the emails
		$emails = array();
		
		// map the created log to the users that want an email when one is created
		foreach($logs_created as $log_id => $log)
		{
			$equipment_id = 0;
			if(isset($log['Equipment']['id']))
			{
				$equipment_id = $log['Equipment']['id'];
			}
			else
			{
				$changes = unserialize($log['Dblog']['changes']);
				if(isset($changes['equipment_id'])) $equipment_id = $changes['equipment_id'];
			}
		
			// build one email for each user that want all
			foreach($users_created_all as $user_id => $user_created_all)
			{
				// make sure there is an entry into the email array
				$email_address = $user_created_all['email'];
				if(!isset($emails[$email_address]))
				{
					$emails[$email_address] = array(
						'email' => $email_address,
						'name' => $user_created_all['name'],
						'log_count_updated' => 0,
						'log_count_new' => 0,
//						'log_count_closed' => 0,
						'log_count_deleted' => 0,
						'logs' => array(),
					);
				}
				
				($log['Dblog']['deleted']?$emails[$email_address]['log_count_deleted']++:$emails[$email_address]['log_count_new']++);
				
				// add this log to the email
				$emails[$email_address]['logs'][$log_id] = array(
					'model' => $log['Dblog']['model'],
					'model_id' => $log['Dblog']['model_id'],
					'user_id' => $log['Dblog']['user_id'],
					'user' => (isset($log['DblogUser']['email'])?$log['DblogUser']['email']:''),
					'equipment_id' => $equipment_id,
					'status' => ($log['Dblog']['deleted']?4:1),
					'timestamp' => $log['Dblog']['created'],
					'message' => ($log['Dblog']['mapped_readable']?$log['Dblog']['mapped_readable']:str_replace(';;;', "\n", $log['Dblog']['readable'])),
				);
			}
			
			// build one email for each user that want all
			foreach($users_created_mentioned as $user_id => $user_created_mentioned)
			{
				if(!in_array($user_id, $log['user_ids'])) continue;
				
				// make sure there is an entry into the email array
				$email_address = $user_created_mentioned['email'];
				if(!isset($emails[$email_address]))
				{
					$emails[$email_address] = array(
						'email' => $email_address,
						'name' => $user_created_mentioned['name'],
						'log_count_updated' => 0,
						'log_count_new' => 0,
//						'log_count_closed' => 0,
						'log_count_deleted' => 0,
						'logs' => array(),
					);
				}
				
				($log['Dblog']['deleted']?$emails[$email_address]['log_count_deleted']++:$emails[$email_address]['log_count_new']++);
				
				// add this log to the email
				$emails[$email_address]['logs'][$log_id] = array(
					'model' => $log['Dblog']['model'],
					'model_id' => $log['Dblog']['model_id'],
					'user_id' => $log['Dblog']['user_id'],
					'user' => (isset($log['DblogUser']['email'])?$log['DblogUser']['email']:''),
					'equipment_id' => $equipment_id,
					'status' => ($log['Dblog']['deleted']?4:1),
					'timestamp' => $log['Dblog']['created'],
					'message' => ($log['Dblog']['mapped_readable']?$log['Dblog']['mapped_readable']:str_replace(';;;', "\n", $log['Dblog']['readable'])),
				);
			}
		}
		
		// map the updated log to the users that want an email when one is updated
		foreach($logs_updated as $log_id => $log)
		{
			$equipment_id = 0;
			if(isset($log['Equipment']['id']))
			{
				$equipment_id = $log['Equipment']['id'];
			}
			else
			{
				$changes = unserialize($log['Dblog']['changes']);
				if(isset($changes['equipment_id'])) $equipment_id = $changes['equipment_id'];
			}
			
			// build one email for each user that want all
			foreach($users_updated_all as $user_id => $user_updated_all)
			{
				// make sure there is an entry into the email array
				$email_address = $user_updated_all['email'];
				if(!isset($emails[$email_address]))
				{
					$emails[$email_address] = array(
						'email' => $email_address,
						'name' => $user_updated_all['name'],
						'log_count_updated' => 0,
						'log_count_new' => 0,
//						'log_count_closed' => 0,
						'log_count_deleted' => 0,
						'logs' => array(),
					);
				}
				
				($log['Dblog']['deleted']?$emails[$email_address]['log_count_deleted']++:$emails[$email_address]['log_count_updated']++);
				
				// add this log to the email
				$emails[$email_address]['logs'][$log_id] = array(
					'model' => $log['Dblog']['model'],
					'model_id' => $log['Dblog']['model_id'],
					'user_id' => $log['Dblog']['user_id'],
					'user' => (isset($log['DblogUser']['email'])?$log['DblogUser']['email']:''),
					'equipment_id' => $equipment_id,
					'status' => ($log['Dblog']['deleted']?4:2),
					'timestamp' => $log['Dblog']['created'],
					'message' => ($log['Dblog']['mapped_readable']?$log['Dblog']['mapped_readable']:str_replace(';;;', "\n", $log['Dblog']['readable'])),
				);
			}
			
			// build one email for each user that want all
			foreach($users_updated_mentioned as $user_id => $user_updated_mentioned)
			{
				if(!in_array($user_id, $log['user_ids'])) continue;
				
				// make sure there is an entry into the email array
				$email_address = $user_updated_mentioned['email'];
				if(!isset($emails[$email_address]))
				{
					$emails[$email_address] = array(
						'email' => $email_address,
						'name' => $user_updated_mentioned['name'],
						'log_count_updated' => 0,
						'log_count_new' => 0,
//						'log_count_closed' => 0,
						'log_count_deleted' => 0,
						'logs' => array(),
					);
				}
				
				($log['Dblog']['deleted']?$emails[$email_address]['log_count_deleted']++:$emails[$email_address]['log_count_updated']++);
				
				// add this log to the email
				$emails[$email_address]['logs'][$log_id] = array(
					'model' => $log['Dblog']['model'],
					'model_id' => $log['Dblog']['model_id'],
					'user_id' => $log['Dblog']['user_id'],
					'user' => (isset($log['DblogUser']['email'])?$log['DblogUser']['email']:''),
					'equipment_id' => $equipment_id,
					'status' => ($log['Dblog']['deleted']?4:2),
					'timestamp' => $log['Dblog']['created'],
					'message' => ($log['Dblog']['mapped_readable']?$log['Dblog']['mapped_readable']:str_replace(';;;', "\n", $log['Dblog']['readable'])),
				);
			}
		}
		
		// this keeps the logs in the proper order by object, then log order (uses key)
		foreach($emails as $email_address => $email_info)
		{
			if(isset($emails[$email_address]['logs']))
			{
				ksort($emails[$email_address]['logs']);
			}
		}
		
		// load ability to create an html link
		App::uses('View', 'View');
		$View = new View();
		App::uses('HtmlHelper', 'View/Helper');
		$HtmlHelper = new HtmlHelper($View);
		
		// load the email task
		$Email = $this->Tasks->load('Utilities.Email');
		
		$signature = array();
		
		$message_status_template = "%s - %s";
		$log_status_map = array(
			1 => __('Added'),
			2 => __('Updated'),
			3 => __('Closed'),
			4 => __('Deleted'),
		);
		
		foreach($emails as $email_address => $email_info)
		{
			$Email->set('to', array($email_info['email'] => $email_info['name']));
			$subject = __('Changes made. New: %s, Updated: %s, Deleted: %s',
				$email_info['log_count_new'],
				$email_info['log_count_updated'],
				$email_info['log_count_deleted']
			);
			$Email->set('subject', $subject); 
		
			$body = array();
			
			$message_items = array();
			foreach($email_info['logs'] as $log)
			{
				$link = Configure::read('Site.base_url'). $HtmlHelper->url(array('controller' => 'equipment', 'action' => 'view', $log['equipment_id']));
				$message = array(
					__($message_status_template, $log['model'], $log_status_map[$log['status']]),
				);
				$message[] = __('Timestamp: %s', $log['timestamp']);
				$message[] = __('Log Generated by: %s', $log['user']);
				$message[] = __('Details: %s', $log['message']);
				$message[] = __('Link: %s', $link);
				$message_items[] = implode("\n", $message);
			}
			
			$message_items = implode("\n------------------------------\n", $message_items);
			$body[] = $message_items;
			
			// signature
			$body[] = ' ';
			$body[] = '------------------------------';
			$body[] = __('To change your email settings for this notification, please visit the below url, and select the "Email Settings" tab.');
			$body[] = Configure::read('Site.base_url'). $HtmlHelper->url(array('controller' => 'users', 'action' => 'edit'));
			
			$body = implode("\n", $body);
			
			$Email->set('body', $body);
			$Email->execute();
			$this->out(__('Email sent to: %s - Subject: %s', $email_info['email'], $subject), 1, Shell::QUIET);
		}
	}
}