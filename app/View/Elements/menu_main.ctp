<?php ?>
				<?php if (AuthComponent::user('id')): ?>
				<ul class="sf-menu">
					
					<?php if (AuthComponent::user('id') and AuthComponent::user('role') == 'admin'): ?>
					<li>
						<?php echo $this->Html->link(__('Admin'), '#', array('class' => 'top')); ?>
						<ul>
							<li><?php echo $this->Html->link(__('%s %s', __('Project'), __('Statuses')), array('controller' => 'project_statuses', 'action' => 'index', 'admin' => true, 'manager' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Charge Codes'), array('controller' => 'charge_codes', 'action' => 'index', 'admin' => true, 'manager' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Activities'), array('controller' => 'activities', 'action' => 'index', 'admin' => true, 'manager' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Review Reasons'), array('controller' => 'review_reasons', 'action' => 'index', 'admin' => true, 'manager' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Project File States'), array('controller' => 'project_file_states', 'action' => 'index', 'admin' => true, 'manager' => false, 'plugin' => false)); ?></li>
							
							<?php echo $this->Common->loadPluginMenuItems('admin'); ?>
							<li><?php echo $this->Html->link(__('Users'), '#', array('class' => 'sub')); ?>
								<ul>
									<li><?php echo $this->Html->link(__('All %s', __('Users')), array('controller' => 'users', 'action' => 'index', 'admin' => true, 'manager' => false, 'plugin' => false)); ?></li>
									<li><?php echo $this->Html->link(__('Login History'), array('controller' => 'login_histories', 'action' => 'index', 'admin' => true, 'manager' => false, 'plugin' => false)); ?></li>
								</ul>
							</li>
							<li><?php echo $this->Html->link(__('App Admin'), '#', array('class' => 'sub')); ?>
								<ul>
									<li><?php echo $this->Html->link(__('Config'), array('controller' => 'users', 'action' => 'config', 'admin' => true, 'manager' => false, 'plugin' => false)); ?></li>
<!--									<li><?php echo $this->Html->link(__('Statistics'), array('controller' => 'users', 'action' => 'stats', 'admin' => true, 'plugin' => false)); ?></li> -->
									<li><?php echo $this->Html->link(__('Process Times'), array('controller' => 'proctimes', 'action' => 'index', 'admin' => true, 'manager' => false, 'plugin' => 'utilities')); ?></li> 
								</ul>
							</li>
						</ul>
					</li>
					<?php endif; ?>
					<li>
						<?php echo $this->Html->link(__('Daily Reports'), '#', array('class' => 'top')); ?>
						<ul>
							<li><?php echo $this->Html->link(__('New %s', __('Daily Report')), array('controller' => 'daily_reports', 'action' => 'add', 'admin' => false, 'manager' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('My %s', __('Daily Reports')), array('controller' => 'daily_reports', 'action' => 'index', 'admin' => false, 'manager' => false, 'plugin' => false)); ?></li>
						</ul>
					</li>
					<li>
						<?php echo $this->Html->link(__('Weekly Reports'), '#', array('class' => 'top')); ?>
						<ul>
							<li><?php echo $this->Html->link(__('New %s', __('Weekly Report')), array('controller' => 'weekly_reports', 'action' => 'add', 'admin' => false, 'manager' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Import %s', __('Weekly Report')), array('controller' => 'weekly_reports', 'action' => 'import', 'admin' => false, 'manager' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('My %s', __('Weekly Reports')), array('controller' => 'weekly_reports', 'action' => 'index', 'admin' => false, 'manager' => false, 'plugin' => false)); ?></li>
						</ul>
					</li>
					<li>
						<?php echo $this->Html->link(__('Report Items'), '#', array('class' => 'top')); ?>
						<ul>
							<li><?php echo $this->Html->link(__('Favorite %s', __('Report Items')), array('controller' => 'report_item_favorites', 'action' => 'index', 'admin' => false, 'manager' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Review %s', __('Report Items')), array('controller' => 'management_reports_report_items', 'action' => 'review', 'admin' => false, 'manager' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Reviewed %s', __('Report Items')), array('controller' => 'management_reports_report_items', 'action' => 'reviewed', 'admin' => false, 'manager' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('All My %s %s', __('Daily'), __('Report Items')), array('controller' => 'daily_reports_report_items', 'action' => 'index', 'admin' => false, 'manager' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('All My %s %s', __('Weekly'), __('Report Items')), array('controller' => 'weekly_reports_report_items', 'action' => 'index', 'admin' => false, 'manager' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('My %s %s %s', __('Highlighted'), __('Weekly'), __('Report Items')), array('controller' => 'weekly_reports_report_items', 'action' => 'highlighted', 'admin' => false, 'manager' => false, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('All My %s %s', __('Management'), __('Report Items')), array('controller' => 'management_reports_report_items', 'action' => 'index', 'admin' => false, 'manager' => false, 'plugin' => false)); ?></li>
						</ul>
					</li>
					
					<?php if (AuthComponent::user('id') and AuthComponent::user('manager')): ?>
					
					<li>
						<?php echo $this->Html->link(__('Management'), '#', array('class' => 'top')); ?>
						<ul>
							<li><?php echo $this->Html->link(__('New %s', __('Management Report')), array('controller' => 'management_reports', 'action' => 'add', 'admin' => false, 'manager' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Management Reports'), array('controller' => 'management_reports', 'action' => 'index', 'admin' => false, 'manager' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('Management Report Defaults'), array('controller' => 'management_reports', 'action' => 'defaults', 'admin' => false, 'manager' => true, 'plugin' => false)); ?></li>
							<li><?php echo $this->Html->link(__('All %s', __('Weekly Reports')), array('controller' => 'weekly_reports', 'action' => 'index', 'admin' => false, 'manager' => true, 'plugin' => false)); ?></li>
						</ul>
					</li>
					<?php endif; ?>
					<li><?php echo $this->Html->link(__('Projects'), array('controller' => 'projects', 'action' => 'index', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?></li>
					<li><?php echo $this->Html->link(__('Charge Codes'), array('controller' => 'charge_codes', 'action' => 'index', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?></li>
					<li><?php echo $this->Html->link(__('Activity List'), array('controller' => 'activities', 'action' => 'index', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?></li>
					<li><?php echo $this->Html->link(__('View Users'), array('controller' => 'users', 'action' => 'index', 'admin' => false, 'plugin' => false), array('class' => 'top')); ?></li>
					
					<?php echo $this->Common->loadPluginMenuItems(); ?>
				</ul>
				<?php endif; ?>