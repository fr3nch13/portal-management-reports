<?php 
// File: app/View/ReportItemFavorites/index.ctp

$owner = true;

$page_options = array();
$page_options[] = $this->Html->link(__('Add %s', __('Item')), array('action' => 'add'));

$this->Local->setSortableChargeCodes($item_charge_codes);
$this->Local->setSortableStates($item_states);
$this->Local->setSortableActivities($item_activities);


// content
$th = array(
	'ReportItemFavorite.item' => array('content' => __('Item'), 'options' => array('sort' => 'ReportItemFavorite.item', 'editable' => array('type' => 'text', 'required' => true) )),
	'ReportItemFavorite.item_state' => array('content' => __('%s %s', __('Item'), __('State')), 'options' => array('sort' => 'ReportItemFavorite.item_state', 'editable' => array('type' => 'select', 'options' => $this->Local->getSortableStates()) )),
	'ReportItemFavorite.charge_code_id' => array('content' => __('Charge Code'), 'options' => array('sort' => 'ReportItemFavorite.charge_code_id', 'editable' => array('type' => 'select', 'required' => true, 'options' => $this->Local->getSortableChargeCodes(false, true)) )),
	'ReportItemFavorite.activity_id' => array('content' => __('Activity'), 'options' => array('sort' => 'ReportItemFavorite.activity_id', 'editable' => array('type' => 'select', 'required' => true, 'options' => $this->Local->getSortableActivities(false, true)) )),
	'ReportItemFavorite.week1' => array('content' => __('Week 1'), 'options' => array('sort' => 'ReportItemFavorite.week1')),
	'ReportItemFavorite.week2' => array('content' => __('Week 2'), 'options' => array('sort' => 'ReportItemFavorite.week2')),
	'ReportItemFavorite.mon' => array('content' => __('Mon'), 'options' => array('sort' => 'ReportItemFavorite.mon')),
	'ReportItemFavorite.tue' => array('content' => __('Tues'), 'options' => array('sort' => 'ReportItemFavorite.tue')),
	'ReportItemFavorite.wed' => array('content' => __('Wed'), 'options' => array('sort' => 'ReportItemFavorite.wed')),
	'ReportItemFavorite.thu' => array('content' => __('Thurs'), 'options' => array('sort' => 'ReportItemFavorite.thu')),
	'ReportItemFavorite.fri' => array('content' => __('Fri'), 'options' => array('sort' => 'ReportItemFavorite.fri')),
	'ReportItemFavorite.sat' => array('content' => __('Sat'), 'options' => array('sort' => 'ReportItemFavorite.sat')),
	'ReportItemFavorite.sun' => array('content' => __('Sun'), 'options' => array('sort' => 'ReportItemFavorite.sun')),
	'multiselect' => true,
);

$td = array();
foreach ($report_item_favorites as $i => $report_item_favorite)
{
	$edit_id = array(
		'ReportItemFavorite' => $report_item_favorite['ReportItemFavorite']['id'],
		'ReportItemFavorite.user_id' => AuthComponent::user('id'),
	);
	
	$td[$i] = array(
		$report_item_favorite['ReportItemFavorite']['item'],
		array($this->Local->getSortableStates($report_item_favorite['ReportItemFavorite']['item_state']), array('value' => $report_item_favorite['ReportItemFavorite']['item_state'])),
		array($this->Local->getSortableChargeCodes($report_item_favorite['ReportItemFavorite']['charge_code_id']), array('value' => $report_item_favorite['ReportItemFavorite']['charge_code_id'])),
		array($this->Local->getSortableActivities($report_item_favorite['ReportItemFavorite']['activity_id']), array('value' => $report_item_favorite['ReportItemFavorite']['activity_id'])),
		$this->Html->toggleLink($report_item_favorite['ReportItemFavorite'], 'week1'),
		$this->Html->toggleLink($report_item_favorite['ReportItemFavorite'], 'week2'),
		$this->Html->toggleLink($report_item_favorite['ReportItemFavorite'], 'mon'),
		$this->Html->toggleLink($report_item_favorite['ReportItemFavorite'], 'tue'),
		$this->Html->toggleLink($report_item_favorite['ReportItemFavorite'], 'wed'),
		$this->Html->toggleLink($report_item_favorite['ReportItemFavorite'], 'thu'),
		$this->Html->toggleLink($report_item_favorite['ReportItemFavorite'], 'fri'),
		$this->Html->toggleLink($report_item_favorite['ReportItemFavorite'], 'sat'),
		$this->Html->toggleLink($report_item_favorite['ReportItemFavorite'], 'sun'),
		'multiselect' => $report_item_favorite['ReportItemFavorite']['id'],
		'edit_id' => $edit_id,
	);
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Favorite Report Items'),
	'page_description' => __('These %s are used to COPY to a Report. Once in the Report, they are not linked together. Changing something here WILL NOT change the copied %s. The Week 1/2, and weekday options are for auto importing into %s.', __('Favorite Report Items'), __('Report Items'), __('Weekly Reports')),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	// grid/inline edit options
	'use_gridedit' => $owner,
	'use_gridadd' => $owner,
	'use_griddelete' => $owner,
	// multiselect options
	'use_multiselect' => false, // add this later if requested/needed
	'multiselect_options' => array(
		'charge_code' => __('Set all selected %s to one %s', __('Report Item Favorites'), __('Charge Code')),
		'multicharge_code' => __('Set each selected %s to a %s individually', __('Report Item Favorites'), __('Charge Code')),
		'activity' => __('Set all selected %s to one %s', __('Report Item Favorites'), __('Activity')),
		'multiactivity' => __('Set each selected %s to an %s individually', __('Report Item Favorites'), __('Activity')),
		'delete' => __('Delete selected %s', __('Report Item Favorites')),
	),
	'multiselect_referer' => array(
		'admin' => $this->params['admin'],
		'controller' => $this->params['controller'],
		'action' => $this->params['action'],
	),
));