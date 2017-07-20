<?php
/**
 * AppShell file
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Shell', 'Console');

/**
 * Application Shell
 *
 * Add your application-wide methods in the class below, your shells
 * will inherit them.
 *
 * @package       app.Console.Command
 */
class AppShell extends Shell 
{
	public $OutTask = false;
	public $WrapTask = false;
	
	public function __construct()
    {
        $args = func_get_args();
		parent::__construct($args);
		
		if(isset($this->params['verbose']) and $this->params['verbose'])
		{
			Configure::write('debug', 2);
		}
		elseif(Configure::read('debug') > 1)
		{
			Configure::write('debug', 1);
		}
    }
	
	public function startup() 
	{
		if(isset($this->params['verbose']) and $this->params['verbose'])
		{
			Configure::write('debug', 2);
		}
		elseif(Configure::read('debug') > 1)
		{
			Configure::write('debug', 1);
		}
		if(!$this->WrapTask)
		{
			$this->WrapTask = $this->Tasks->load('Utilities.Wrap');
		}
		return parent::startup();
	}
	
	public function __destruct() 
	{
		if(isset($this->params['verbose']) and $this->params['verbose'])
		{
			Configure::write('debug', 2);
		}
		elseif(Configure::read('debug') > 1)
		{
			Configure::write('debug', 1);
		}
		
		// reports warnings and errors to admins of this app
		if($IssueReporter = $this->Tasks->load('Utilities.IssueReporter'))
		{
			$IssueReporter->execute($this, GetCallingMethodName());
		}
		
		// prints out debug information
		if($Debug = $this->Tasks->load('Utilities.Debug'))
		{
			$Debug->execute($this);
		}
	}

	// overwrite the parent out() to add some information like a timestamp
	public function out($message = null, $newlines = 1, $level = Shell::NORMAL)
	{
		if(!$this->OutTask)
		{
			// wraps the information being written out 
			if(!$this->OutTask = $this->Tasks->load('Utilities.Out'))
			{
				return parent::out($message, $newlines, $level);
				
			}
		}
		
		$message = $this->OutTask->execute($this, $message, $newlines, $level);
		
		return parent::out($message, $newlines, $level);
	}
}
