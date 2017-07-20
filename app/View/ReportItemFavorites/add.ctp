<?php 
// File: app/View/ReportItemFavorites/add.ctp
?>
<div class="top">
	<h1><?php echo __('New %s', __('Favorite Report Item')); ?></h1>
</div>
<div class="center">
	<div class="form">
		<?php echo $this->Form->create('ReportItemFavorite');?>
		    <fieldset>
		    	<?php
					echo $this->Form->input('item', array(
						'label' => __('Item Content'),
						'type' => 'text',
					));
					echo $this->Form->input('charge_code_id', array(
						'options' => $charge_codes,
						'div' => array('class' => 'third'),
					));
					echo $this->Form->input('activity_id', array(
						'div' => array('class' => 'third'),
					));
					echo $this->Form->input('item_state', array(
						'options' => $item_states,
						'div' => array('class' => 'third'),
					));
				?>
		    </fieldset>
		<?php echo $this->Form->end(__('Save %s', __('Favorite Report Item'))); ?>
	</div>
</div>