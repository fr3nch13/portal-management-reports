<?php

$sortable_columns = (isset($sortable_columns)?$sortable_columns:array());
$sortable_column_holders = (isset($sortable_column_holders)?$sortable_column_holders:array());

$sortable_options = (isset($sortable_options)?$sortable_options:array());
$this->Local->setSortableOptions($sortable_options);

$sortable_charge_codes = (isset($sortable_charge_codes)?$sortable_charge_codes:array());
$this->Local->setSortableChargeCodes($sortable_charge_codes);

$sortable_states = (isset($sortable_states)?$sortable_states:array());
$this->Local->setSortableStates($sortable_states);

$sortable_activities = (isset($sortable_activities)?$sortable_activities:array());
$this->Local->setSortableActivities($sortable_activities);

$show_highlight_buttons = (isset($sortable_options['show_highlight_buttons'])?$sortable_options['show_highlight_buttons']:false);

$html_column_holders = array();
foreach($sortable_column_holders as $column_id => $column)
{
	$html_column_holders[] = $this->Local->makeSortableColumn($column_id, $column);
}

$html_column_holders = $this->Html->tag('div', implode('', $html_column_holders), array(
	'class' => $this->Local->sortable_options['wrapper_holder_class'],
	'id' => $this->Local->sortable_options['wrapper_holder_id'],
));

$html_columns = array();
foreach($sortable_columns as $column_id => $column)
{
	$html_columns[] = $this->Html->tag('li', $this->Local->makeSortableColumn($column_id, $column));
}

$html_columns = $this->Html->tag('ul', implode('', $html_columns), array(
	'class' => $this->Local->sortable_options['wrapper_class'],
	'id' => $this->Local->sortable_options['wrapper_id'],
));

?>
<div class="sortable_options_table">
<table class="<?php echo $this->Local->sortable_options['master_class'] ?>" id="<?php echo $this->Local->sortable_options['master_id'] ?>" cellpadding="0" cellspacing="0">
	<tr>
		<th class="legend">
			<?php echo __('Legend'); ?>
		</th>
		<th class="multi_options">
			<?php echo __('Select Options'); ?>
		</th>
		<th class="multi_options">
			<?php echo __('With Selected:'); ?>
		</th>
	</tr>
	<tr>
		<td class="legend">
			<ul>
				<li><?php echo __('Click, then drag an item to move it.'); ?></li>
				<li><?php echo __('Double click an item, or check the checkbox to select it.'); ?></li>
				<li><?php echo __('Bold items are selected, normal aren\'t.'); ?></li>
				<li><?php echo __('Underlined items are highlighted, normal aren\'t.'); ?></li>
			</ul>
		</td>
		<td class="multi_options">
			<?php 
			echo $this->Local->sortableSelectAllButton(); 
			echo $this->Local->sortableUnSelectAllButton();
			?>
		</td>
		<td class="multi_options">
			<?php 
			echo $this->Local->sortableChargeCodeSelect(); 
			echo $this->Local->sortableActivitySelect(); 
			if($show_highlight_buttons) echo $this->Local->sortableHighlightButton(); 
			if($show_highlight_buttons) echo $this->Local->sortableHighlightButton(true); 
			echo $this->Local->sortableDeleteButton(); 
			?>
		</td>
	</tr>
</table>
</div>
<div class="html_columns">
<?php echo $html_columns ?>
</div>


<?php 
$this->Js->buffer($this->Local->sortableJquery()); 
?>
