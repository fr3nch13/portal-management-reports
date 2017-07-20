<?php

class UtilityShell extends AppShell
{
	// the models to use
	public $uses = array('Vector');
	
	public function startup() 
	{
		$this->clear();
		$this->out('Utility Shell');
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
		
		$parser->description(__d('cake_console', 'Used to run methods from the command line.'));
		
		$parser->addSubcommand('nslookup', array(
			'help' => __d('cake_console', 'Looks up a hostname for it\'s ip addresses'),
			'parser' => array(
				'arguments' => array(
					'hostname' => array('help' => __d('cake_console', 'The hostname to look up.'), 'required' => true)
				)
			)
		));
		
		return $parser;
	}
	
	public function nslookup()
	{
		$host = $this->args[0];
		$type = $this->Vector->discoverType($host);
		$results = false;
		
		// set the list of local hosts
		// see Config/app_config.php
		$this->Vector->Ipaddress->NS_setLocalsIps(explode(',', Configure::read('AppConfig.Nslookup.internal_ips')));
		$this->Vector->Hostname->NS_setLocalsHosts(explode(',', Configure::read('AppConfig.Nslookup.internal_hosts')));
		
		if($type == 'hostname')
		{
			$results = $this->Vector->Hostname->NS_getIps($host);
			$this->out($this->Vector->Hostname->shellOut());
		}
		elseif($type == 'ipaddress')
		{
			$results = $this->Vector->Ipaddress->NS_getHostnames($host);
			$this->out($this->Vector->Ipaddress->shellOut());
		}
print_r($results);
	}
}
?>