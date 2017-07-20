<?php 
// File: app/View/ReportItemFavorites/favorite_index.ctp

$this->Local->setSortableChargeCodes($item_charge_codes);
$this->Local->setSortableStates($item_states);
$this->Local->setSortableActivities($item_activities);

$results = array();
foreach ($report_item_favorites as $i => $report_item_favorite)
{
	$id = $report_item_favorite['ReportItemFavorite']['id'];
	
	$results[$id] = __('%s - [ %s - %s - %s ]', 
		$report_item_favorite['ReportItemFavorite']['item'],
		$this->Local->getSortableStates($report_item_favorite['ReportItemFavorite']['item_state']),
		$this->Local->getSortableChargeCodes($report_item_favorite['ReportItemFavorite']['charge_code_id']),
		$this->Local->getSortableActivities($report_item_favorite['ReportItemFavorite']['activity_id'])
	);
}

if(isset($this->request->params['named']))
{
	$named = $this->request->params['named'];
	$named = Set::flatten($named);
	foreach($named as $k => $v)
	{
		echo $this->Form->input($k, array('type' => 'hidden', 'value' => $v));
	}
}

echo $this->Form->input('ReportItemFavorite.items', array(
	'label' => __('Select multiple %s to add to this %s.', __('Favorite Report Items'), __('Report')),
	'type' => 'select',
	'multiple' => true,
	'options' => $results,
	'data-favorite-required' => true,
));

echo $this->Html->tag('div', '', array('class' => 'required_message'));
echo $this->Html->link(__('Add selected to %s', __('Report')), '#', array('class' => 'button favorites-submit'));
?>

<script type="text/javascript">
//<![CDATA[
$(document).ready(function ()
{
//	$('select.multipleSelect').chosen({});
	
	$('a.favorites-submit').bind("click", function (event) {
		
		var inline_button = $(this);
		
		var require_fail = false;
		
		inline_button.parent().find('select').each(function( index ) {
			if($(this).attr('data-favorite-required'))
			{
				var required_message = $(this).parents('div.favorites-list').find('div.required_message');
				if(!$.trim($(this).val()))
				{
					required_message.html('<?php echo __("Please select an option."); ?>');
					required_message.show();
					required_message.fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
					require_fail = true;
				}
				else
				{
					required_message.hide();
				}
			}
		});
		
		if(require_fail)
		{
			return false;
		}
		
		var inline_serialized = '';
		inline_button.parent().find('input').each(function( index ) {
			inline_serialized += $(this).serialize()+'&';
		});
		inline_button.parent().find('select').each(function( index ) {
			inline_serialized += $(this).serialize()+'&';
		});
		
		// use ajax to load the list of favorites here
		$.ajax({
			type: 'POST',
			url: "<?php echo $this->Html->url($this->Html->urlModify(array('action' => 'favorite_add'))); ?>",
			data: inline_serialized,
			success: function(data) {
				// we're in a tab, so refresh the tab
				var panel = inline_button.parents('section.panel');
				if(panel.length)
				{
					var tabId = panel.attr('aria-labelledby');
					if(tabId)
					{
						var tab = panel.parents('.nihfo-object-tabs').find('nav.tabs a#'+tabId);
						if(tab)
						{
							tab.trigger('click');
							return true;
						}
					}
				}
				
				location.reload(true);
			},
		});
		
		
		return false;
	});
});
//]]>
</script>