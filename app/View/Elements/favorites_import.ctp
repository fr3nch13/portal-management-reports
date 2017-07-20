<?php

$favorites_id = (isset($favorites_id)?$favorites_id:'favorites_import_'.rand(100, 500));
$model = (isset($model)?$model:false);
$ids = (isset($ids)?$ids:array());

// the path to submit the favorites to
$favorites_path = (isset($favorites_path)?$favorites_path:array(
	'controller' => 'report_item_favorites',
	'action' => 'favorite_index',
	'admin' => false,
	'manager' => false,
	'model' => $model,
	'ids' => $ids,
));

?>

<div class="favorites-import" id="<?php echo $favorites_id; ?>">
	<div class="favorites-off">
		<a href="#" class="favorites-show button"><?php echo  __('Add %s to this %s', __('Favorites'), __('Report')); ?></a>
	</div>
	<div class="favorites-on">
		<div class="favorites-cancel">
			<a href="#" class="favorites-hide button"><?php echo  __('Cancel'); ?></a>
		</div>
		<div class="favorites-list">
		</div>
		<div class="favorites-results">
		</div>
	</div>
</div>

<script type="text/javascript">
//<![CDATA[
$(document).ready(function ()
{
	$('div#<?php echo $favorites_id; ?> div.favorites-on').hide();
	$('div#<?php echo $favorites_id; ?> div.favorites-off').show();
	
	$('div#<?php echo $favorites_id; ?> a.favorites-show').bind("click", function (event) 
	{
		// use ajax to load the list of favorites here
		$.ajax({
			type: 'POST',
			url: "<?php echo $this->Html->url($favorites_path); ?>",
			success: function(data) {
				$('div#<?php echo $favorites_id; ?> div.favorites-list').html(data);
//				$('div#<?php echo $favorites_id; ?> div.favorites-list select').chosen();
				$('div#<?php echo $favorites_id; ?> div.favorites-on').show();
				$('div#<?php echo $favorites_id; ?> div.favorites-off').hide();
			},
		});
	return false;
	});
	
	$('div#<?php echo $favorites_id; ?> a.favorites-hide').bind("click", function (event) {
		
		$('div#<?php echo $favorites_id; ?> div.favorites-on').hide();
		$('div#<?php echo $favorites_id; ?> div.favorites-off').show();
		
		return false;
	});
	
});
//]]>
</script>