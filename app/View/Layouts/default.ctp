<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php 
		$site_title = Configure::read('AppConfig.Site.title');
		if(!$site_title)
		{
			$site_title = Configure::read('Site.title');
		}
		echo $site_title;
		?>:
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');
		
		echo $this->fetch('meta');
		echo $this->fetch('css');
		
		echo $this->Html->css('overall');
		echo $this->Html->css('generic');
		echo $this->Html->css('content');
		echo $this->Html->css('jquery-ui');
		echo $this->Html->css('tipsy');
		echo $this->Html->css('jquery.countdown');
		echo $this->Html->css('Tags.tags');
		echo $this->Html->css('superfish');
		echo $this->Html->css('superfish.style');
		
		if(Configure::read('debug') > 0)
		{
			echo $this->Html->css('sql_dump');
		}
		
		echo $this->Html->script('jquery');
		echo $this->Html->script('jquery-ui');
		echo $this->Html->script('jquery.tipsy');
		echo $this->Html->script('jquery.countdown');
//		echo $this->Html->script('jquery.cssparentselector');
		echo $this->Html->script('jquery.ui.widget');
		echo $this->Html->script('jquery.ui.position');
		echo $this->Html->script('jquery.ui.autocomplete');
		echo $this->Html->script('superfish');
		echo $this->fetch('script');
	?>
	<script type="text/javascript">
		//<![CDATA[
		$(document).ready(function ()
		{
			if ( $.browser.mozilla ) {
				$("#no_firefox").css( "display","hidden" );
			}
			
    		$('#no_java').hide();
    		$('#flash_wrapper').delay(6000).fadeOut('slow');
    		
    		// top menu
    		$("ul.sf-menu").superfish(); 
    		
    		//// add all links with a title in them to have a tipsy hook
    		// add a '?' after the object and move the title from the object to the new appended one
    		$.each($('.tipsy'), function(){
    			// get the title
    			var title = $(this).attr('title');
    			// unset the title
    			if(title) { $(this).removeAttr('title', ''); }
    			$(this).removeClass('tipsy');
    			$(this).append('<span class="tipsy" title="'+title+'">?</span>');
    		});
    		
    		$('.tipsy').tipsy();
    		
    		$('#sessionTimeout').countdown({until: +<?php echo (Configure::read('Session.timeout')*60); ?>, compact: true, format: 'HMS', description: 'Session Timeout'});
    		
    		// adds the ability to highlight a row in a table when the td is hovered over
//    		$("td").hover(function(){ $(this).parent().css('background-color', 'red'); }, function(){ $(this).parent().css('background-color', 'blue'); });
			
			$.post(  
            	'/<?php echo $this->params["controller"] ?>/proctime',  
            	{proc_time: $("#proc_time").text()},  
            	function(responseText)
            	{  
                	$("#proc_time").html(responseText);  
            	},  
            	"html"  
        	);
		});
		//]]>
	</script>
</head>
<body>
	
	<div id="no_firefox">
		Please enable javascript.
	</div>
	
	<div id="no_java">
		Please enable javascript.
	</div>
	
	<div id="loading" style="display:none;">
		<?php echo $this->Html->image('loading.gif', array('alt' => 'Loading...')); ?>
	</div>
	
	<div id="site">
		<div id="header">
			<div class="content">
				<div id="site_title">
					<?php echo $this->Html->link($site_title, '/'); ?>
				</div>
			
				<div id="menu_user">
					<ul>
					<?php if (AuthComponent::user('id')): ?>
						<li>Welcome: <?php echo (AuthComponent::user('name')?AuthComponent::user('name'):AuthComponent::user('email')); ?></li>
						<li><?php echo $this->Html->link(__('Edit Settings'), array('controller' => 'users', 'action' => 'edit', 'admin' => false, 'plugin' => false)); ?></li>
						<li><?php echo $this->Html->link(__('Logout'), array('controller' => 'users', 'action' => 'logout', 'admin' => false, 'plugin' => false)); ?></li>
					<?php else: ?>
						<li><?php echo $this->Html->link(__('Login'), array('controller' => 'users', 'action' => 'login', 'admin' => false, 'plugin' => false)); ?></li>
					<?php endif; ?>
					</ul>
				</div>
				
			</div>
		</div>
		
		<div id="menu_main">
			<div class="content">
				<?php if (AuthComponent::user('id')): ?>
				<ul class="sf-menu">
					<li>
						<?php echo $this->Html->link(__('Categories'), '#', array('class' => 'top')); ?>
						<ul>
							<li><?php echo $this->Html->link(__('Public Categories'), array('controller' => 'categories', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('My Categories'), array('controller' => 'categories', 'action' => 'mine', 'admin' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Temp Categories'), array('controller' => 'temp_categories', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Category Groups'), array('controller' => 'category_types', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
						</ul>
					</li>
					<li>
						<?php echo $this->Html->link(__('Reports'), '#', array('class' => 'top')); ?>
						<ul>
							<li><?php echo $this->Html->link(__('Public Reports'), array('controller' => 'reports', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('My Reports'), array('controller' => 'reports', 'action' => 'mine', 'admin' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Temp Reports'), array('controller' => 'temp_reports', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Report Groups'), array('controller' => 'report_types', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
						</ul>
					</li>
					<li>
						<?php echo $this->Html->link(__('Files'), '#', array('class' => 'top')); ?>
						<ul>
							<li><?php echo $this->Html->link(__('Public Files'), array('controller' => 'uploads', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('My Files'), array('controller' => 'uploads', 'action' => 'mine', 'admin' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Temp Files'), array('controller' => 'temp_uploads', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('File Groups'), array('controller' => 'upload_types', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
						</ul>
					</li>
					<li>
						<?php echo $this->Html->link(__('Vectors'), '#', array('class' => 'top')); ?>
						<ul>
							<li><?php echo $this->Html->link(__('Vectors'), array('controller' => 'vectors', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Hostnames'), array('controller' => 'vectors', 'action' => 'hostnames', 'admin' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Ip Addresses'), array('controller' => 'vectors', 'action' => 'ipaddresses', 'admin' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('DNS Records'), array('controller' => 'nslookups', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Vector Groups'), array('controller' => 'vector_types', 'action' => 'index', 'admin' => false, 'plugin' => false)); ?></li>
						</ul>
					</li>
					<li><?php echo $this->Html->link(__('Dumps'), array('controller' => 'dumps', 'action' => 'index', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?></li>
					<li><?php echo $this->Html->link(__('Tags'), array('controller' => 'tags', 'action' => 'index', 'admin' => false, 'plugin' => 'tags'), array('class' => 'top')); ?></li>
					<li><?php echo $this->Html->link(__('Users'), array('controller' => 'users', 'action' => 'index', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?></li>
					
					<?php if (AuthComponent::user('id') and AuthComponent::user('role') == 'admin'): ?>
					<li>
						<?php echo $this->Html->link(__('Admin'), '#', array('class' => 'top')); ?>
						<ul>
							<li><?php echo $this->Html->link(__('Categories'), array('controller' => 'categories', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Category Groups'), array('controller' => 'category_types', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Reports'), array('controller' => 'reports', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Report Groups'), array('controller' => 'report_types', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Files'), array('controller' => 'uploads', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('File Groups'), array('controller' => 'upload_types', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Vectors'), array('controller' => 'vectors', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Vector Groups'), array('controller' => 'vector_types', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Tags'), array('controller' => 'tags', 'action' => 'index', 'admin' => true, 'plugin' => 'tags')); ?></li>
							<li><?php echo $this->Html->link(__('Users'), array('controller' => 'users', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Login History'), array('controller' => 'login_histories', 'action' => 'index', 'admin' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Config'), array('controller' => 'users', 'action' => 'config', 'admin' => true, 'plugin' => false)); ?></li>
						</ul>
					</li>
					<?php endif; ?>
				</ul>
				<?php endif; ?>
			</div>
		</div>
		
		<div id="flash_wrapper" class="content">
				<?php echo $this->Session->flash(); ?>
		</div>
		
		<div id="body">
			<div id="body_content" class="content">
				<?php echo $this->fetch('content'); ?>
			</div>
		</div>
<!--
		<div id="object_menu">
object menu
		</div>
-->
		
		<div class="clearb"> </div>
		<div id="footer">
			<div class="content">

			</div>
				<div id="sessionTimeout"> </div>
		</div>
	<?php 
		if(Configure::read('debug') > 0)
		{
			echo $this->element('Utilities.sql_dump'); 
		}
	?>
	</div>
	<!--
	// hide the flash wrapper after 30 secounds
	-->
<?php
echo $this->Js->writeBuffer();
?>
<div id="proc_time"><?php echo (microtime(true) - PROC_START); ?></div>
</body>
</html>
