<?php ?>
<!-- File: app/View/Users/login.ctp -->

<div class="top">
	<h1><?php echo _('Login'); ?></h1>
</div>
<div class="center">
	<div class="left">
	<div class="users form">
	<?php echo $this->Session->flash('auth'); ?>
	<?php echo $this->Form->create('User');?>
	    <fieldset>
	        <legend><?php echo __('Please enter your email and password'); ?></legend>
	    <?php
	        echo $this->Form->input('email');
	        echo $this->Form->input('password');
	    ?>
	    </fieldset>
	<?php echo $this->Form->end(__('Login'));?>
	</div>
	</div>
	<div class="right">
		<?php echo $this->element('Utilities.login_banner'); ?>
	</div>
</div>

