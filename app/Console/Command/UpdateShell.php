<?php

class UpdateShell extends AppShell
{
	// the models to use
	public $uses = array('ReportItem');
	
	public function startup() 
	{
		$this->clear();
		$this->out('Update Shell');
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
		
		$parser->description(__d('cake_console', 'The Update Shell runs all needed jobs to update production\'s database.'));
		
		$parser->addSubcommand('fix_user_issue', array(
			'help' => __d('cake_console', 'Fix issue with users not being tracked.'),
		));
		
		return $parser;
	}
	
	public function fix_user_issue()
	{
		$this->ReportItem->WeeklyReportsReportItem->fixUserIssue();
		$this->ReportItem->ManagementReportsReportItem->fixUserIssue();
	}
}