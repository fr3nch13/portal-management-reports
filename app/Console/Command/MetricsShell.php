<?php

class MetricsShell extends AppShell
{
	// the models to use
//	public $uses = array('Equipment', 'FismaInventory', 'VerifiedOrg', 'FismaSystem');
	public $uses = array('Equipment', 'FismaInventory');
	
	public function startup() 
	{
//		$this->clear();
		$this->Equipment->shellOut('Metrics Shell');
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
		
		$parser->description(__d('cake_console', 'The Metrics Shell runs metrics on the different objects.'));
		
		$parser->addSubcommand('yearly', array(
			'help' => __d('cake_console', 'Metrics Reports for object from the beginning of this year, to today.'),
		));
		
		return $parser;
	}
	
	public function yearly()
	{
		$created = date('Y'). '-01-01 00:00:00';
		//$created = '2013-01-01 00:00:00';
		
		
		$this->Equipment->shellOut(__('Metrics Counts since %s', $created), 'metrics');
		
		$counts = array();
		
		
		$review_states = $this->Equipment->ReviewState->find('list', array(
			'recursive' => -1,
			'fields' => array('ReviewState.id', 'ReviewState.name'),
			'order' => array('ReviewState.name'),
		));
		$this->Equipment->shellOut(__('Found %s Review States.', count($review_states)), 'metrics');
		
		$equipments = $this->Equipment->find('all', array(
			'recursive' => 0,
			'order' => array('Equipment.created DESC'),
			'conditions' => array(
				'Equipment.created >' => $created,
			),
		));
		
		$counts['Equipment Added'] = count($equipments);
		$this->Equipment->shellOut(__('Found %s Equipment.', $counts['Equipment Added']), 'metrics');
		
		$verified_orgs = array();
		$equipment_statuses = array();
		$discovery_methods = array();
		$op_divs = array();
		$_review_states = array();
		foreach($equipments as $equipment)
		{
			$equipment_status_name = $equipment['EquipmentStatus']['name'];
			if(!$equipment_status_name) $equipment_statuses['Unassigned'] = (isset($equipment_statuses['Unassigned'])?++$equipment_statuses['Unassigned']:1);
			else $equipment_statuses[$equipment_status_name] = (isset($equipment_statuses[$equipment_status_name])?++$equipment_statuses[$equipment_status_name]:1);
			
			$verified_org_name = $equipment['VerifiedOrg']['name'];
			if(!$verified_org_name) $verified_orgs['Unassigned'] = (isset($verified_orgs['Unassigned'])?++$verified_orgs['Unassigned']:1);
			else $verified_orgs[$verified_org_name] = (isset($verified_orgs[$verified_org_name])?++$verified_orgs[$verified_org_name]:1);
			
			$review_state_id = $equipment['Equipment']['review_state_id'];
			if(!$review_state_id) $_review_states['Unassigned'] = (isset($_review_states['Unassigned'])?++$_review_states['Unassigned']:1);
			else $_review_states[$review_state_id] = (isset($_review_states[$review_state_id])?++$_review_states[$review_state_id]:1);
			
			$discovery_method_name = $equipment['DiscoveryMethod']['name'];
			if(!$discovery_method_name) $discovery_methods['Unassigned'] = (isset($discovery_methods['Unassigned'])?++$discovery_methods['Unassigned']:1);
			else $discovery_methods[$discovery_method_name] = (isset($discovery_methods[$discovery_method_name])?++$discovery_methods[$discovery_method_name]:1);
			
			$op_div_name = $equipment['OpDiv']['name'];
			if(!$op_div_name) $op_divs['Unassigned'] = (isset($op_divs['Unassigned'])?++$op_divs['Unassigned']:1);
			else $op_divs[$op_div_name] = (isset($op_divs[$op_div_name])?++$op_divs[$op_div_name]:1);
		}
		
		arsort($equipment_statuses);
		$counts['Equipment Counts by Equipment Statuses'] = $equipment_statuses;
		
		arsort($verified_orgs);
		$counts['Equipment Counts by Verified Orgs'] = $verified_orgs;
		
		arsort($discovery_methods);
		$counts['Equipment Counts by Discovery Methods'] = $discovery_methods;
		
		arsort($op_divs);
		$counts['Equipment Counts by Op Divs'] = $op_divs;
		
		// replace the review_state_id with the review_state name
		$review_state_counts = array();
		foreach($_review_states as $review_state_id => $count)
		{
			$name = (isset($review_states[$review_state_id])?$review_states[$review_state_id]:$review_state_id);
			
			$review_state_counts[$name] = $count;
		}
		
		arsort($review_state_counts);
		$counts['Equipment Counts by Review States'] = $review_state_counts;
		
		unset($equipments);
		unset($verified_orgs);
		unset($equipment_statuses);
		unset($discovery_methods);
		unset($op_divs);
		unset($review_states);
		unset($_review_states);
		unset($review_state_counts);
		
pr($counts);
	}
}