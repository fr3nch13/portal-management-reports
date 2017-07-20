<?php 
/**
 * File: /app/View/Elements/page_sortable.ctp
 * 
 * Use: provide a consistant layout for index pages.
 *
 * Usage: echo $this->element('page_index', array([options]));
 */

/////// Default settings.

// main title of the page
$this->set('trackReferer', true);

// main title of the page
$page_title = (isset($page_title)?$page_title:'');
$page_subtitle = (isset($page_subtitle)?$page_subtitle:'');
$page_subtitle2 = (isset($page_subtitle2)?$page_subtitle2:false);
$page_options_title = (isset($page_options_title)?$page_options_title:__('Options'));
$page_options = (isset($page_options)?$page_options:array());
$page_options_title2 = (isset($page_options_title2)?$page_options_title2:__('More Options'));
$page_options2 = (isset($page_options2)?$page_options2:array());
$page_options_html = (isset($page_options_html)?$page_options_html:array());
$page_description = (isset($page_description)?$page_description:false);
$use_search = (isset($use_search)?$use_search:true);
$use_filter = (isset($use_filter)?$use_filter:false);
$use_export = (isset($use_export)?$use_export:false);
$search_title_query = (isset($search_title_query)?$search_title_query:false);
$search_title_fields = (isset($search_title_fields)?$search_title_fields:false);

if(AuthComponent::user('role'))
{
	$page_options[] = $this->Html->roleLink(AuthComponent::user('role'), $this->_getViewFileName());
}

$no_records = (isset($no_records)?$no_records:__('No records were found.'));

$sortable_column_holders = (isset($sortable_column_holders)?$sortable_column_holders:array()); 
$sortable_columns = (isset($sortable_columns)?$sortable_columns:array()); 
$sortable_options = (isset($sortable_options)?$sortable_options:array());
$sortable_charge_codes = (isset($sortable_charge_codes)?$sortable_charge_codes:array());
$sortable_states = (isset($sortable_states)?$sortable_states:array());
$sortable_activities = (isset($sortable_activities)?$sortable_activities:array());
$new_column_row = (isset($new_column_row)?$new_column_row:3); 

////////////////////////////////////////

if($page_title) $this->set('title_for_layout', $page_title);

echo $this->element('Utilities.object_top', array(
	'page_title' => $page_title,
	'page_subtitle' => $page_subtitle,
	'page_subtitle2' => $page_subtitle2,
	'page_description' => $page_description,
	'page_options_title' => $page_options_title,
	'page_options' => $page_options,
	'page_options_title2' => $page_options_title2,
	'page_options2' => $page_options2,
	'page_options_html' => $page_options_html,
	'use_export' => $use_export,
	'use_search' => $use_search,
	'use_filter' => $use_filter,
	'search_title_query' => $search_title_query,
	'search_title_fields' => $search_title_fields,
));
?>

<div class="center">
	<?php 

echo $this->element('sortable', array(
	'sortable_column_holders' => $sortable_column_holders,
	'sortable_columns' => $sortable_columns,
	'sortable_options' => $sortable_options,
	'sortable_charge_codes' => $sortable_charge_codes,
	'sortable_states' => $sortable_states,
	'sortable_activities' => $sortable_activities,
	'new_column_row' => $new_column_row,
)); 

?>
</div>

<?php
// include any scripts that would be created for things like pagination
if(isset($this->Avatar))
{
	echo $this->Avatar->avatarPreview();
}
echo $this->Js->writeBuffer();

if(Configure::read('debug') > 0)
{
	echo $this->element('Utilities.sql_dump'); 
}