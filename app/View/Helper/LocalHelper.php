<?php

// app/View/Helper/WrapHelper.php
App::uses('AppHelper', 'View/Helper');

/*
 * A helper used specifically for this app
 */
class LocalHelper extends AppHelper 
{
	public $helpers = array('Wrap', 'Html', 'Form');
	
	public $sortable_options = array();
	
	public $sortable_options_defaults = array(
		'master_class' => 'sortable_columns_master',
		'master_id' => 'sortable_columns_master_',
		'select_all_id' => 'sortable_select_all_',
		'unselect_all_id' => 'sortable_unselect_all_',
		'delete_selected_id' => 'sortable_delete_selected_',
		'highlight_selected_id' => 'sortable_unhighlight_selected_',
		'unhighlight_selected_id' => 'sortable_highlight_selected_',
		'charge_code_list_class' => 'sortable_charge_code_list',
		'charge_code_list_id' => 'sortable_charge_code_list_',
		'activity_list_class' => 'sortable_activity_list',
		'activity_list_id' => 'sortable_activity_list_',
		'wrapper_holder_class' => 'sortable_column_holders',
		'wrapper_holder_id' => 'sortable_column_holders_',
		'wrapper_class' => 'sortable_columns',
		'wrapper_id' => 'sortable_columns_',
		'column_wrapper_class' => 'sortable_column_wrapper',
		'column_wrapper_id_prefix' => 'sortable_column_wrapper_',
		'column_title_class' => 'sortable_column_title',
		'column_title_id_prefix' => 'sortable_column_title_',
		'column_title_name_class' => 'sortable_column_title_name',
		'column_title_name_id_prefix' => 'sortable_column_title_name_',
		'column_title_options_class' => 'sortable_column_title_options',
		'column_title_options_id_prefix' => 'sortable_column_title_options_',
		'column_title_select_class' => 'sortable_column_title_select',
		'column_title_select_id_prefix' => 'sortable_column_title_select_',
		'column_title_addlink_class' => 'sortable_column_title_addlink',
		'column_title_addlink_id_prefix' => 'sortable_column_title_addlink_',
		'column_title_addlink_cancel_class' => 'sortable_column_title_addlink_cancel',
		'column_title_addlink_cancel_id_prefix' => 'sortable_column_title_addlink_cancel_',
		'column_title_add_class' => 'sortable_column_title_add',
		'column_title_add_id_prefix' => 'sortable_column_title_add_',
		'column_title_select_text' => 'Toggle Items',
		'column_class' => 'sortable_column',
		'column_id_prefix' => 'sortable_column_',
		'portlet_class' => 'sortable_portlet',
		'portlet_id_prefix' => 'sortable_portlet_',
		'portlet_class_hover' => 'sortable_portlet_hover',
		'portlet_class_checkbox' => 'sortable_portlet_checked',
		'portlet_class_highlighted' => 'sortable_portlet_highlighted',
		'portlet_header_class' => 'portlet_header',
		'portlet_header_id_prefix' => 'portlet_header_',
		'portlet_header_title_class' => 'portlet_header_title',
		'portlet_header_title_id_prefix' => 'portlet_header_title_',
		'portlet_header_date_class' => 'portlet_header_date',
		'portlet_header_date_id_prefix' => 'portlet_header_date_',
		'portlet_header_options_class' => 'portlet_header_options',
		'portlet_header_options_id_prefix' => 'portlet_header_options_',
		'portlet_header_select_class' => 'portlet_header_select',
		'portlet_header_select_id_prefix' => 'portlet_header_select_',
		'portlet_header_checkbox_class' => 'portlet_header_checkbox',
		'portlet_header_checkbox_id_prefix' => 'portlet_header_checkbox_',
		'portlet_header_activity_class' => 'portlet_header_activity',
		'portlet_header_activity_id_prefix' => 'portlet_header_activity_',
		'portlet_content_class' => 'portlet_content',
		'portlet_content_id_prefix' => 'portlet_content_',
		'ajax_sortable_url' => false,
		'ajax_delete_url' => false,
		'ajax_highlight_url' => false,
		'ajax_unhighlight_url' => false,
		'ajax_charge_code_assign_url' => false,
		'ajax_activity_assign_url' => false,
	);
	
	public $sortable_charge_codes = array();
	public $sortable_states = array();
	public $sortable_activities = array();
	public $sortable_review_reasons = array();
	
	public function emailOptions()
	{
		$options = array(
			0 => __('Never'),
		);
		foreach (range(9, 24) as $hour) 
		{
			$nice = date("g a T", strtotime("$hour:00"));
			if($hour == 12) $nice = 'Noon';
			if($hour == 24) $nice = 'Midnight';
		    $options[$hour] = $nice;
		}
		return $options;
	}
	
	public function niceDay($date = false, $test_date = false, $manually_set = false)
	{
		$date = date('Ymd', strtotime($date));
		$test_date = date('Ymd', strtotime($test_date));
		
		$prefix = false;
		if($date == $test_date and $manually_set == false)
		{
			$prefix = __('Week Of: ');
		}
		
		$date = $prefix. $this->Wrap->niceDay($date);
		return $date;
	}
	
	public function setSortableOptions($sortable_options)
	{
		$sortable_options = array_merge($this->sortable_options_defaults, $sortable_options);
		
		if(is_array($sortable_options['ajax_sortable_url']))
		{
			$sortable_options['ajax_sortable_url'] = $this->Html->url($sortable_options['ajax_sortable_url']);
		}
		
		if(is_array($sortable_options['ajax_delete_url']))
		{
			$sortable_options['ajax_delete_url'] = $this->Html->url($sortable_options['ajax_delete_url']);
		}
		
		if(is_array($sortable_options['ajax_highlight_url']))
		{
			$sortable_options['ajax_highlight_url'] = $this->Html->url($sortable_options['ajax_highlight_url']);
		}
		
		if(is_array($sortable_options['ajax_unhighlight_url']))
		{
			$sortable_options['ajax_unhighlight_url'] = $this->Html->url($sortable_options['ajax_unhighlight_url']);
		}
		
		if(is_array($sortable_options['ajax_charge_code_assign_url']))
		{
			$sortable_options['ajax_charge_code_assign_url'] = $this->Html->url($sortable_options['ajax_charge_code_assign_url']);
		}
		
		if(is_array($sortable_options['ajax_activity_assign_url']))
		{
			$sortable_options['ajax_activity_assign_url'] = $this->Html->url($sortable_options['ajax_activity_assign_url']);
		}
		
		if(isset($sortable_options['wrapper_id']) and $sortable_options['wrapper_id'])
		{
			$sortable_options['wrapper_id'] .= rand(1, 200);
		}
		
		if(isset($sortable_options['wrapper_holder_id']) and $sortable_options['wrapper_holder_id'])
		{
			$sortable_options['wrapper_holder_id'] .= rand(1, 200);
		}
		
		if(isset($sortable_options['master_id']) and $sortable_options['master_id'])
		{
			$sortable_options['master_id'] .= rand(1, 200);
		}
		
		if(isset($sortable_options['charge_code_list_id']) and $sortable_options['charge_code_list_id'])
		{
			$sortable_options['charge_code_list_id'] .= rand(1, 200);
		}
		
		if(isset($sortable_options['activity_list_id']) and $sortable_options['activity_list_id'])
		{
			$sortable_options['activity_list_id'] .= rand(1, 200);
		}
		
		if(isset($sortable_options['select_all_id']) and $sortable_options['select_all_id'])
		{
			$sortable_options['select_all_id'] .= rand(1, 200);
		}
		
		if(isset($sortable_options['unselect_all_id']) and $sortable_options['unselect_all_id'])
		{
			$sortable_options['unselect_all_id'] .= rand(1, 200);
		}
		
		if(isset($sortable_options['delete_selected_id']) and $sortable_options['delete_selected_id'])
		{
			$sortable_options['delete_selected_id'] .= rand(1, 200);
		}
		
		if(isset($sortable_options['highlight_selected_id']) and $sortable_options['highlight_selected_id'])
		{
			$sortable_options['highlight_selected_id'] .= rand(1, 200);
		}
		
		if(isset($sortable_options['unhighlight_selected_id']) and $sortable_options['unhighlight_selected_id'])
		{
			$sortable_options['unhighlight_selected_id'] .= rand(1, 200);
		}
		
		$this->sortable_options = $sortable_options;
	}
	
	public function setSortableChargeCodes($charge_codes = array())
	{
		$this->sortable_charge_codes = $charge_codes;
	}
	
	public function getSortableChargeCodes($id = false, $reformatted = false)
	{
		$sortable_charge_codes = $this->sortable_charge_codes;
		
		if($id !== false)
		{
			foreach($sortable_charge_codes as $sortable_id => $sortable_value)
			{
				if(is_array($sortable_value))
				{
					if(isset($sortable_value['value']) and $sortable_value['value'] == $id)
						return $sortable_value['name'];
				}
			}
			
			return false;
		}
		
		if($reformatted)
		{
			$sortable_charge_codes_old = $sortable_charge_codes;
			$sortable_charge_codes = array();
			foreach($sortable_charge_codes_old as $sortable_id => $sortable_value)
			{
				if(is_array($sortable_value))
				{
					if(isset($sortable_value['value']) and isset($sortable_value['name']))
					{
						$sortable_charge_codes[$sortable_value['value']] = $sortable_value['name'];
					}
				}
			}
			
		}
		
		return $sortable_charge_codes;
	}
	
	public function setSortableStates($states = array())
	{
		$this->sortable_states = $states;
	}
	
	public function getSortableStates($id = false)
	{
		if($id !== false)
		{
			if(isset($this->sortable_states[$id]))
				return $this->sortable_states[$id];
			else
				return false;
		}
		return $this->sortable_states;
	}
	
	public function setSortableActivities($activities = array())
	{
		$this->sortable_activities = $activities;
	}
	
	public function getSortableActivities($id = false, $reformatted = false)
	{
		$sortable_activities = $this->sortable_activities;
		
		if($id !== false)
		{
			foreach($sortable_activities as $sortable_id => $sortable_value)
			{
				if(is_array($sortable_value))
				{
					if(isset($sortable_value['value']) and $sortable_value['value'] == $id)
						return $sortable_value['name'];
				}
			}
			
			return false;
		}
		
		if($reformatted)
		{
			$sortable_activities_old = $sortable_activities;
			$sortable_activities = array();
			foreach($sortable_activities_old as $sortable_id => $sortable_value)
			{
				if(is_array($sortable_value))
				{
					if(isset($sortable_value['value']) and isset($sortable_value['name']))
					{
						$sortable_activities[$sortable_value['value']] = $sortable_value['name'];
					}
				}
			}
			
		}
		
		return $sortable_activities;
	}
	
	public function setSortableReviewReasons($review_reasons = array())
	{
		$this->sortable_review_reasons = $review_reasons;
	}
	
	public function getSortableReviewReasons($id = false, $reformatted = false)
	{
		$sortable_review_reasons = $this->sortable_review_reasons;
		
		if($id !== false)
		{
			foreach($sortable_review_reasons as $sortable_id => $sortable_value)
			{
				if(is_array($sortable_value))
				{
					if(isset($sortable_value['value']) and $sortable_value['value'] == $id)
						return $sortable_value['name'];
				}
			}
			
			return false;
		}
		
		if($reformatted)
		{
			$sortable_review_reasons_old = $sortable_review_reasons;
			$sortable_review_reasons = array();
			foreach($sortable_review_reasons_old as $sortable_id => $sortable_value)
			{
				if(is_array($sortable_value))
				{
					if(isset($sortable_value['value']) and isset($sortable_value['name']))
					{
						$sortable_review_reasons[$sortable_value['value']] = $sortable_value['name'];
					}
				}
			}
			
		}
		
		return $sortable_review_reasons;
	}
	
	public function getSortableTitle($charge_code_id = false, $activity_id = false, $highlighted = false)
	{
		$charge_code = $activity = __('Unknown/Other');
		if($charge_code_id)
		{
			if($charge_code_id = $this->getSortableChargeCodes($charge_code_id))
				$charge_code = $charge_code_id;
		}
		if($activity_id)
		{
			if($activity_id = $this->getSortableChargeCodes($activity_id))
				$activity = $activity_id;
		}
		if($highlighted)
		{
			$highlighted = __("\n%s", __('Highlighted'));
		}
		return __("%s: %s\n%s: %s%s", __('Charge Code'), $charge_code, __('Activity'), $activity, $highlighted);
	}

	public function makeSortableColumn($column_id = false, $column = array())
	{
		$portlets = array();
		$column_title_div = $column_content_div = false;
		if(isset($column['title']))
		{
			$column_add = false;
			$column_add_div = false;
			if(isset($column['add_link']))
			{
				$column_add = $this->Html->link(__('Add'), $column['add_link'], array(
					'class' => $this->sortable_options['column_title_addlink_class'],
					'id' => $this->sortable_options['column_title_addlink_id_prefix']. $column_id,
				));
				$column_add .= $this->Html->link(__('Cancel Add'), '#', array(
					'class' => $this->sortable_options['column_title_addlink_cancel_class'],
					'id' => $this->sortable_options['column_title_addlink_cancel_id_prefix']. $column_id,
				));
			
				$column_add_div = $this->Html->tag('div','', array(
					'class' => $this->sortable_options['column_title_add_class'],
					'id' => $this->sortable_options['column_title_add_id_prefix']. $column_id,
				));
			}
			
			$column_select = $this->Form->input('column_select.'. $column_id, array(
				'type' => 'checkbox',
				'label' => false,
				'div' => false,
				'class' => $this->sortable_options['column_title_select_class'],
				'id' => $this->sortable_options['column_title_select_id_prefix']. $column_id,
			));
			
			$column_options = $this->Html->tag('span', $column_add. $column_select, array(
				'class' => $this->sortable_options['column_title_options_class'],
				'id' => $this->sortable_options['column_title_options_id_prefix']. $column_id,
				'title' => __('Select Items'),
			));
			
			$column_title = $this->Html->tag('span', $column['title'], array(
				'class' => $this->sortable_options['column_title_name_class'],
				'id' => $this->sortable_options['column_title_name_id_prefix']. $column_id,
			));
			
			$column_title_div = $this->Html->tag('div',$column_title. $column_options, array(
				'class' => $this->sortable_options['column_title_class'],
				'id' => $this->sortable_options['column_title_id_prefix']. $column_id,
			));
			$column_title_div .= $column_add_div;
		}
		if(isset($column['items']))
		{
			foreach($column['items'] as $item_id => $item)
			{
				$portlet_header = $portlet_content = false;
				if(isset($item['header']))
				{
					$select = $this->Form->input('report_item.'. $item_id, array(
						'type' => 'checkbox',
						'label' => false,
						'div' => false,
						'class' => $this->sortable_options['portlet_header_checkbox_class'],
						'id' => $this->sortable_options['portlet_header_checkbox_id_prefix']. $item_id,
					));
					$item_select = $this->Html->tag('span', $select, array(
						'class' => $this->sortable_options['portlet_header_select_class'],
						'id' => $this->sortable_options['portlet_header_select_id_prefix']. $item_id,
						'title' => __('Select Item'),
					));
					
					$item_date = false;
					if(isset($item['date']))
					{
						$item_date = ' '. $this->Html->tag('span', '('.$item['date'].')', array(
							'class' => $this->sortable_options['portlet_header_date_class'],
							'id' => $this->sortable_options['portlet_header_date_id_prefix']. $item_id,
						));
					}
					$item_header = $this->Html->tag('span', $item['header']. $item_date, array(
						'class' => $this->sortable_options['portlet_header_title_class'],
						'id' => $this->sortable_options['portlet_header_title_id_prefix']. $item_id,
					));
					$item_toggle = $this->Html->tag('span', '[-]', array(
						'class' => $this->sortable_options['portlet_header_options_class'],
						'id' => $this->sortable_options['portlet_header_options_id_prefix']. $item_id,
						'title' => __('Item Options'),
					));
					$item_activity = $this->Html->tag('span', '', array(
						'class' => $this->sortable_options['portlet_header_activity_class'],
						'id' => $this->sortable_options['portlet_header_activity_id_prefix']. $item_id,
					));
					
					$portlet_header = $this->Html->tag('div', $item_header. $item_activity. $item_select. $item_toggle, array(
						'class' => $this->sortable_options['portlet_header_class'],
						'id' => $this->sortable_options['portlet_header_id_prefix']. $item_id,
					));
				}
				
				if(isset($item['content']))
				{
					$portlet_content_arrow = $this->Html->tag('div', '', array(
						'class' => 'arrow',
					));
					$portlet_content = $this->Html->tag('span', $item['content']);
					$portlet_content = $this->Html->tag('div', $portlet_content_arrow. $portlet_content, array(
						'class' => $this->sortable_options['portlet_content_class'],
						'id' => $this->sortable_options['portlet_content_id_prefix']. $item_id,
					));
				}
				
				$portlet_class = $this->sortable_options['portlet_class'];
				if(isset($item['class']) and $item['class'])
				{
					$portlet_class .= ' '. $item['class'];
				}
				
				$portlet_attributes = array(
					'class' => $portlet_class,
					'id' => $this->sortable_options['portlet_id_prefix']. $item_id,
				);
				if(isset($item['attributes']) and $item['attributes'] and is_array($item['attributes']))
				{
					foreach($item['attributes'] as $attr_key => $attr_value)
					{
						$portlet_attributes[$attr_key] = $attr_value;
					}
				}
				
				$portlets[] = $this->Html->tag('div', $portlet_header. $portlet_content, $portlet_attributes);
			}
		}
		$column_content_div = $this->Html->tag('div', implode(' ', $portlets), array(
			'class' => $this->sortable_options['column_class'],
			'id' => $this->sortable_options['column_id_prefix']. $column_id,
		));
		
		return $this->Html->tag('div', $column_title_div. $column_content_div, array(
			'class' => $this->sortable_options['column_wrapper_class'],
			'id' => $this->sortable_options['column_wrapper_id_prefix']. $column_id,
		));
	}
	
	public function sortableSelectAllButton($label = false)
	{
		if(!$label)
		{
			$label = __('Select All');
		}
		
		$input = $this->Form->input($label, array(
			'type' => 'button',
			'label' => false,
			'class' => 'button sortable-multi-option sortable-button-select-all',
			'id' => $this->sortable_options['select_all_id'],
			'div' => false,
		));
		
		return $input;
	}
	
	public function sortableUnSelectAllButton($label = false)
	{
		if(!$label)
		{
			$label = __('Unselect All');
		}
		
		$input = $this->Form->input($label, array(
			'type' => 'button',
			'label' => false,
			'class' => 'button sortable-multi-option sortable-button-select-none',
			'id' => $this->sortable_options['unselect_all_id'],
			'div' => false,
		));
		
		return $input;
	}
	
	public function sortableDeleteButton($label = false)
	{
		if(!$label)
		{
			$label = __('Delete Selected %s', __('Items'));
		}
		
		$input = $this->Form->input($label, array(
			'type' => 'button',
			'label' => false,
			'class' => 'button sortable-multi-option sortable-button-selected-delete',
			'id' => $this->sortable_options['delete_selected_id'],
			'div' => false,
		));
		
		return $input;
	}
	
	public function sortableHighlightButton($unhighlight = false)
	{
		$label = __('Highlight Selected %s', __('Items'));
		$id = $this->sortable_options['highlight_selected_id'];
		if(!$unhighlight)
		{
			$label = __('Unhighlight Selected %s', __('Items'));
			$id = $this->sortable_options['unhighlight_selected_id'];
		}
		
		$input = $this->Form->input($label, array(
			'type' => 'button',
			'label' => false,
			'class' => 'button sortable-multi-option sortable-button-selected-highlight'.($unhighlight?'-on':'-off'),
			'id' => $id,
			'div' => false,
		));
		
		return $input;
	}
	
	public function sortableChargeCodeSelect($label = false)
	{
		if(!$label)
		{
			array_unshift($this->sortable_charge_codes, __('Assign %s to Selected %s:', __('Charge Code'), __('Items')));
		}
		
		$input = $this->Form->input('charge_code_list', array(
			'type' => 'select',
			'label' => false,
			'id' => $this->sortable_options['charge_code_list_id'],
			'class' => $this->sortable_options['charge_code_list_class']. ' sortable-multi-option ',
			'options' => $this->getSortableChargeCodes(),
			'div' => false,
		));
		
		return $input;
	}
	
	public function sortableActivitySelect($label = false)
	{
		if(!$label)
		{
			array_unshift($this->sortable_activities, __('Assign %s to Selected %s:', __('Activity'), __('Items')));
		}
		
		$input = $this->Form->input('activity_list', array(
			'type' => 'select',
			'label' => false,
			'id' => $this->sortable_options['activity_list_id'],
			'class' => $this->sortable_options['activity_list_class']. ' sortable-multi-option ',
			'options' => $this->getSortableActivities(),
			'div' => false,
		));
		
		return $input;
	}
	
	public function sortableJquery()
	{
		// build the activity and charge code maps
		$set_charge_codes = array(
			__('Unknown'),
		);
		foreach($this->getSortableChargeCodes() as $i => $charge_code)
		{
			if(is_array($charge_code))
			{
				if(isset($charge_code['value']) and isset($charge_code['name']))
				$set_charge_codes[$charge_code['value']] = $charge_code['name'];
			}
			elseif(is_string($charge_code))
			{
				$set_charge_codes[] = $charge_code;
			}
		}
		
		$set_activities = array(
			__('Unknown'),
		);
		foreach($this->getSortableActivities() as $i => $activity)
		{
			if(is_array($activity))
			{
				if(isset($activity['value']) and isset($activity['name']))
				$set_activities[$activity['value']] = $activity['name'];
			}
			elseif(is_string($activity))
			{
				$set_activities[] = $activity;
			}
		}
	
		$jsScript = '
$(document).ready(function ()
{
	var charge_code_map = '.json_encode($set_charge_codes).';
	var activity_map = '.json_encode($set_activities).';
	
	// build the activity and charge code maps
	function createTitle(charge_code_id, activity_id, highlighted)
	{
		var title = [];
		
		charge_code_id = typeof charge_code_id !== "undefined" ? charge_code_id : false;
		activity_id = typeof activity_id !== "undefined" ? activity_id : false;
		highlighted = typeof highlighted !== "undefined" ? highlighted : false;
		
		if(charge_code_id)
		{
			if(charge_code_map.hasOwnProperty(charge_code_id))
				title.push("'.__('Charge Code').': "+charge_code_map[charge_code_id]);
		}
		if(activity_id)
		{
			if(activity_map.hasOwnProperty(activity_id))
				title.push("'.__('Activity').': "+activity_map[activity_id]);
		}
		if(highlighted)
		{
			title.push("'.__('Highlighted').'");
		}
		return title.join("\n");
	}
	
	/// make the parts sortable
	$( ".'.$this->sortable_options['column_class'].'" ).sortable({
		connectWith: ".'.$this->sortable_options['column_class'].'",
		handle: ".'.$this->sortable_options['portlet_header_class'].'",
		cancel: ".portlet-toggle",
		placeholder: "portlet_placeholder",
	});
	
		
		$( "#'.$this->sortable_options['wrapper_id'].'" ).shapeshift({
			minColumns: 3,
			handle: ".sort-handle",
			gutterX: 5,
			gutterY: 5,
			paddingX: 0,
			paddingY: 0
		});
	
	// ajax update when resort happens
	// set here so I can trigger it later
	
	$( ".'.$this->sortable_options['column_class'].'" ).on(\'sortupdate\',function() {
		
		// resort the blocks with mansonry
		$( "#'.$this->sortable_options['wrapper_id'].'" ).trigger("ss-rearrange");
		
		var data = $(this).sortable( "serialize", {key : "item[]" } );
		var state = $( this ).attr("id").split(/_/).pop();
		$.ajax({
			data: { state: state, items: data },
			type: \'POST\',
			url: \''.$this->sortable_options['ajax_sortable_url'].'\',
			success: function(data) {
				/// update the counts on the details page if they exist
				updateStatsValues( data );
			},
		});
	});
	
	
	
	// hide the extended options for items
	$( ".'.$this->sortable_options['portlet_class'].'" )
		.find( ".'.$this->sortable_options['portlet_content_class'].'" )
		.hide();
	
	// hide the link to the extended options for items
	$( ".'.$this->sortable_options['portlet_class'].'" )
		.find( ".'.$this->sortable_options['portlet_header_options_class'].'" )
		.hide();
	
	// hide the checkbox for an item
	$( ".'.$this->sortable_options['portlet_class'].'" )
		.find( ".'.$this->sortable_options['portlet_header_select_class'].'" )
		.hide();
	
	// hide the add div for the portlets
	$( ".'.$this->sortable_options['column_wrapper_class'].'" )
		.find( ".'.$this->sortable_options['column_title_add_class'].'" )
		.hide();
	
	// hide the add cancel link for the portlets
	$( ".'.$this->sortable_options['column_wrapper_class'].'" )
		.find( ".'.$this->sortable_options['column_title_addlink_cancel_class'].'" )
		.hide();
		
	// translate the attributes to the title
	$( ".'.$this->sortable_options['portlet_class'].'" )
		.each(function() {
			$( this ).attr("title", createTitle($( this ).attr("charge_code_id"), $( this ).attr("activity_id"), $( this ).attr("highlighted")) );
		});
	
	// add the checkbox class to each box that is checked
	$( ".'.$this->sortable_options['portlet_class'].'" )
		.find( ".'.$this->sortable_options['portlet_header_checkbox_class'].'" )
		.each(function() {
			if($( this ).is(":checked"))
				$( this ).parent().parent().parent()
					.addClass("'.$this->sortable_options['portlet_class_checkbox'].'");
			else
				$( this ).parent().parent().parent()
					.removeClass("'.$this->sortable_options['portlet_class_checkbox'].'");
		});
	
	// add hook to see if a checkbox is changed
	$( ".'.$this->sortable_options['portlet_class'].'" )
		.find( ".'.$this->sortable_options['portlet_header_checkbox_class'].'" )
		.change(function () {
			if($( this ).is(":checked"))
				$( this ).parent().parent().parent()
					.addClass("'.$this->sortable_options['portlet_class_checkbox'].'");
			else
				$( this ).parent().parent().parent()
					.removeClass("'.$this->sortable_options['portlet_class_checkbox'].'");			
		});
	
	// add hook to check the box when the actual words are clicked on
	$( ".'.$this->sortable_options['portlet_class'].'" )
		.find( ".'.$this->sortable_options['portlet_header_title_class'].'" )
		.dblclick(function(){ 
			var checkbox = $( this ).parent().parent()
				.find( ".'.$this->sortable_options['portlet_header_checkbox_class'].'" );
			
			checkbox.prop("checked", !checkbox.prop("checked"));
			if(checkbox.is(":checked"))
				checkbox.parent().parent().parent()
					.addClass("'.$this->sortable_options['portlet_class_checkbox'].'");
			else
				checkbox.parent().parent().parent()
					.removeClass("'.$this->sortable_options['portlet_class_checkbox'].'");			
		});
	
	// add hooks to show/hide the checkbox and options link when over the item
	$( ".'.$this->sortable_options['portlet_class'].'" ).hover(
		function()
		{
			$( this ).addClass("'.$this->sortable_options['portlet_class_hover'].'");
			$( this ).find( ".'.$this->sortable_options['portlet_header_options_class'].'" ).show();
			$( this ).find( ".'.$this->sortable_options['portlet_header_select_class'].'" ).show();
			
		},
		function()
		{
			$( this ).removeClass("'.$this->sortable_options['portlet_class_hover'].'");
			$( this ).find( ".'.$this->sortable_options['portlet_header_options_class'].'" ).hide();
			$( this ).find( ".'.$this->sortable_options['portlet_header_select_class'].'" ).hide();
		}
	);
	
	// add hooks to show/hide the extended options when the options link is clicked
	$( ".'.$this->sortable_options['portlet_class'].'" )
		.find( ".'.$this->sortable_options['portlet_header_options_class'].'" )
		.click(function(){ 
			$( this ).parent().parent()
			.find( ".'.$this->sortable_options['portlet_content_class'].'" )
			.toggle();
			$( "#'.$this->sortable_options['wrapper_id'].'" ).trigger("ss-rearrange");
		});
	
	// add hooks for select all, and unselect all
	$( "#'.$this->sortable_options['select_all_id'].'" )
		.click(function(){
			$( ".'.$this->sortable_options['column_title_select_class'].'" )
				.prop("checked", true);
			$( ".'.$this->sortable_options['portlet_class'].'" )
				.find( ".'.$this->sortable_options['portlet_header_checkbox_class'].'" )
				.each(function() {
					$( this ).prop("checked", true);
					$( this ).parent().parent().parent()
						.addClass("'.$this->sortable_options['portlet_class_checkbox'].'");
				});
				$( "#'.$this->sortable_options['wrapper_id'].'" ).trigger("ss-rearrange");
		});
	
	$( "#'.$this->sortable_options['unselect_all_id'].'" )
		.click(function(){
			$( ".'.$this->sortable_options['column_title_select_class'].'" )
				.prop("checked", false);
			$( ".'.$this->sortable_options['portlet_class'].'" )
				.find( ".'.$this->sortable_options['portlet_header_checkbox_class'].'" )
				.each(function() {
					$( this ).prop("checked", false);
					$( this ).parent().parent().parent()
						.removeClass("'.$this->sortable_options['portlet_class_checkbox'].'");
				});
			$( "#'.$this->sortable_options['wrapper_id'].'" ).trigger("ss-rearrange");
		});
		
	// watch the checkbox for each of the columns
	$( ".'.$this->sortable_options['column_title_select_class'].'" )
		.click(function(){
			var checked = false;
			if($( this ).is(":checked"))
			{
				checked = true;
			}
			$( this ).parent().parent().parent()
				.find( ".'.$this->sortable_options['portlet_header_checkbox_class'].'" )
				.each(function() {
					$( this ).prop("checked", checked);
					if(checked)
						$( this ).parent().parent().parent()
							.addClass("'.$this->sortable_options['portlet_class_checkbox'].'");
					else
						$( this ).parent().parent().parent()
							.removeClass("'.$this->sortable_options['portlet_class_checkbox'].'");
				});
			$( "#'.$this->sortable_options['wrapper_id'].'" ).trigger("ss-rearrange");
		});
	
	// watch the add link for each of the columns
	$( ".'.$this->sortable_options['column_title_addlink_class'].'" )
		.click(function(){
			var link = $( this );
			$.get( $( this ).attr("href"), function( data ) {
  				link.parents( ".'.$this->sortable_options['column_wrapper_class'].'" )
  					.find( ".'.$this->sortable_options['column_title_add_class'].'" )
  					.html( data )
  					.show();
  				// hide add link
  				link.parent( )
  					.find( ".'.$this->sortable_options['column_title_addlink_cancel_class'].'" )
  					.show();
  				link.hide();
  				$( "#'.$this->sortable_options['wrapper_id'].'" ).trigger("ss-rearrange");
  						
			});
			
			return false;
		});
	
	// watch the add link cancel for each of the columns
	$( ".'.$this->sortable_options['column_title_addlink_cancel_class'].'" )
		.click(function(){
			$( this ).parents( ".'.$this->sortable_options['column_wrapper_class'].'" )
  				.find( ".'.$this->sortable_options['column_title_add_class'].'" )
  				.hide();
			$( this ).parents( ".'.$this->sortable_options['column_wrapper_class'].'" )
  				.find( ".'.$this->sortable_options['column_title_addlink_class'].'" )
				.show();
			$( this ).hide();
			
			return false;
		});
	
	
	// take the styles from the charge code select options, and make them css rules
	$( "#'.$this->sortable_options['charge_code_list_id'].'" )
		.find( "option" )
		.each(function() {
			// create a css rule from the element style
			var styles = "";
			// check for a style, grab it, and remove it from the option
			if( $( this ).attr("style") )
			{
				styles = $( this ).attr( "style" );
				$( this ).removeAttr( "style" );
			}
			addCssRule(\'.charge_code_class_\'+ $( this ).val(), styles);
			
			// apply that rule to this option
			$( this ).addClass(\'charge_code_class_\'+ $( this ).val());
		});
	
	// take the styles from the activity select options, and make them css rules
	$( "#'.$this->sortable_options['activity_list_id'].'" )
		.find( "option" )
		.each(function() {
			// create a css rule from the elemt style
			var styles = "";
			// check for a style, grab it, and remove it from the option
			if( $( this ).attr("style") )
			{
				styles = $( this ).attr( "style" );
				$( this ).removeAttr( "style" );
			}
			
			addCssRule(\'option.activity_class_\'+ $( this ).val(), styles);
			
			var style_parts = $.parsecss.parseArguments( styles );
			if(style_parts.length)
			{
				var hex = $.fn.rgb2hex( style_parts[1] );
				styles += "; border: 1px solid "+hex+";"
			}
			
			addCssRule(\'.activity_class_\'+ $( this ).val()+ \' .'.$this->sortable_options['portlet_header_activity_class'].'\', styles);
			
			// apply that rule to this option
			$( this ).addClass(\'activity_class_\'+ $( this ).val());
		});
	
	// add hooks to watch the select charge code dropdown
	$( "#'.$this->sortable_options['charge_code_list_id'].'" )
		.on(\'change\', function() {
			// get the selected value
			var charge_code_id = $(this).val();
			var charge_code_name = $(this).find("option:selected").text();
			
			// reset the selected to the first item
			$(this)[0].selectedIndex = 0;
			
			// ids to send to the server via ajax
			var checked_ids = []; 
			
			// dom ids of the items
			var checked_items = []; 
			
			// find all of the selected items
			$( ".'.$this->sortable_options['portlet_class'].'" )
				.find( ".'.$this->sortable_options['portlet_header_checkbox_class'].'" )
				.each(function() {
					if($( this ).is(":checked"))
					{
						checked_ids.push( $( this ).attr("id").split(/_/).pop() );
						checked_items.push( $( this ).parent().parent().parent().attr("id") );
					}
				});
				
			// send the ids to be updated
			if(checked_ids.length)
			{
				$.ajax({
					data: {items: checked_ids, charge_code_id: charge_code_id },
					type: \'POST\',
					url: \''.$this->sortable_options['ajax_charge_code_assign_url'].'\',
					success: function(data) {
						
						// remove the items from the lists
						$.each(checked_items, function( index, value ) {
							$( "#"+value ).removePrefixedClasses( "charge_code_class_" );
							$( "#"+value ).addClass( "charge_code_class_"+charge_code_id );
							$( "#"+value ).attr("charge_code_id", charge_code_id);
							$( "#"+value ).attr("title", createTitle($( "#"+value ).attr("charge_code_id"), $( "#"+value ).attr("activity_id"), $( "#"+value ).attr("highlighted")) );
						});
						
						// make sure the server knows the new order
						// this will also auto updat the counts, if they exist
						$( ".'.$this->sortable_options['column_class'].'" ).trigger(\'sortupdate\');
					},
				});
			}
		});
	
	// add hooks to watch the select activity dropdown
	$( "#'.$this->sortable_options['activity_list_id'].'" )
		.on(\'change\', function() {
			// get the selected value
			var activity_id = $(this).val();
			var activity_name = $(this).find("option:selected").text();
			
			// reset the selected to the first item
			$(this)[0].selectedIndex = 0;
			
			// ids to send to the server via ajax
			var checked_ids = []; 
			
			// dom ids of the items
			var checked_items = []; 
			
			// find all of the selected items
			$( ".'.$this->sortable_options['portlet_class'].'" )
				.find( ".'.$this->sortable_options['portlet_header_checkbox_class'].'" )
				.each(function() {
					if($( this ).is(":checked"))
					{
						checked_ids.push( $( this ).attr("id").split(/_/).pop() );
						checked_items.push( $( this ).parent().parent().parent().attr("id") );
					}
				});
				
			// send the ids that have changed
			if(checked_ids.length)
			{
				$.ajax({
					data: {items: checked_ids, activity_id: activity_id },
					type: \'POST\',
					url: \''.$this->sortable_options['ajax_activity_assign_url'].'\',
					success: function(data) {
						
						// remove the items from the lists
						$.each(checked_items, function( index, value ) {
							$( "#"+value ).removePrefixedClasses( "activity_class_" );
							$( "#"+value ).addClass( "activity_class_"+activity_id );
							$( "#"+value ).attr("activity_id", activity_id);
							$( "#"+value ).attr("title", createTitle($( "#"+value ).attr("charge_code_id"), $( "#"+value ).attr("activity_id"), $( "#"+value ).attr("highlighted")) );
						});
						
						// make sure the server knows the new order
						// this will also auto updat the counts, if they exist
						$( ".'.$this->sortable_options['column_class'].'" ).trigger(\'sortupdate\');
					},
				});
			}
		});
	
	// add hooks to watch the delete button
	$( "#'.$this->sortable_options['delete_selected_id'].'" )
		.click(function(){ 
			if (confirm("'. __('Are you sure?').'")) 
			{
				var checked_ids = []; // ids to send to the server via ajax
				var portlet_ids = []; // ids of the portlets that need to be destroyed, after successful delete
				
				$( ".'.$this->sortable_options['portlet_class'].'" )
					.find( ".'.$this->sortable_options['portlet_header_checkbox_class'].'" )
					.each(function() {
						if($( this ).is(":checked"))
						{
							checked_id = $( this ).attr("id").split(/_/).pop(); 
							checked_ids.push( checked_id );
							portlet_ids.push( $( this ).parent().parent().parent().attr("id") );
							
						}
					});
				
				// send the ids to be deleted
				if(checked_ids.length)
				{
					$.ajax({
						data: {items: checked_ids},
						type: \'POST\',
						url: \''.$this->sortable_options['ajax_delete_url'].'\',
						success: function(data) {
							
							// remove the items from the lists
							$.each(portlet_ids, function( index, value ) {
								$( "#"+value ).remove();
							});
							
							// make sure the server knows the new order
							// this will also auto updat the counts, if they exist
							$( ".'.$this->sortable_options['column_class'].'" ).trigger(\'sortupdate\');
						},
					});
				}
			} 
			
			return false;
		});
	
	// add hooks to watch the highlight button
	$( "#'.$this->sortable_options['highlight_selected_id'].'" )
		.click(function(){ 
			if (confirm("'. __('Are you sure?').'")) 
			{
				var checked_ids = []; // ids to send to the server via ajax
				var portlet_ids = []; // ids of the portlets that need to be destroyed, after successful delete
				
				$( ".'.$this->sortable_options['portlet_class'].'" )
					.find( ".'.$this->sortable_options['portlet_header_checkbox_class'].'" )
					.each(function() {
						if($( this ).is(":checked"))
						{
							checked_id = $( this ).attr("id").split(/_/).pop(); 
							checked_ids.push( checked_id );
							portlet_ids.push( $( this ).parent().parent().parent().attr("id") );
							
						}
					});
				
				// send the ids to be deleted
				if(checked_ids.length)
				{
					$.ajax({
						data: {items: checked_ids},
						type: \'POST\',
						url: \''.$this->sortable_options['ajax_highlight_url'].'\',
						success: function(data) {
							
							// remove the items from the lists
							$.each(portlet_ids, function( index, value ) {
								$( "#"+value ).addClass( "'.$this->sortable_options['portlet_class_highlighted'].'" );
							});
							
							// make sure the server knows the new order
							// this will also auto updat the counts, if they exist
							$( ".'.$this->sortable_options['column_class'].'" ).trigger(\'sortupdate\');
						},
					});
				}
			} 
			
			return false;
		});
	
	// add hooks to watch the highlight button
	$( "#'.$this->sortable_options['unhighlight_selected_id'].'" )
		.click(function(){ 
			if (confirm("'. __('Are you sure?').'")) 
			{
				var checked_ids = []; // ids to send to the server via ajax
				var portlet_ids = []; // ids of the portlets that need to be destroyed, after successful delete
				
				$( ".'.$this->sortable_options['portlet_class'].'" )
					.find( ".'.$this->sortable_options['portlet_header_checkbox_class'].'" )
					.each(function() {
						if($( this ).is(":checked"))
						{
							checked_id = $( this ).attr("id").split(/_/).pop(); 
							checked_ids.push( checked_id );
							portlet_ids.push( $( this ).parent().parent().parent().attr("id") );
							
						}
					});
				
				// send the ids to be deleted
				if(checked_ids.length)
				{
					$.ajax({
						data: {items: checked_ids},
						type: \'POST\',
						url: \''.$this->sortable_options['ajax_unhighlight_url'].'\',
						success: function(data) {
							
							// remove the items from the lists
							$.each(portlet_ids, function( index, value ) {
								$( "#"+value ).removeClass( "'.$this->sortable_options['portlet_class_highlighted'].'" );
							});
							
							// make sure the server knows the new order
							// this will also auto updat the counts, if they exist
							$( ".'.$this->sortable_options['column_class'].'" ).trigger(\'sortupdate\');
						},
					});
				}
			} 
			
			return false;
		});
		
	// trigger manonry resize after all elements are shown/hidden
	$( "#'.$this->sortable_options['wrapper_id'].'" ).trigger("ss-rearrange");
});
';
	return $jsScript;
	}
	

	public function makeManagementSection($title = false, $description = false, $items_content = false)
	{
		$title = $this->Html->tag('h3', $title, array('class' => 'section_title'));
		
		$description = str_replace("\n", '<br />', $description);
		$description = $this->Html->tag('div', $description, array('class' => 'section_description'));
		
		$items_content = $this->Html->tag('div', $items_content, array('class' => 'section_description'));
		
		return $this->Html->tag('div', $title. $description. $items_content, array('class' => 'management_section'));
	}
}