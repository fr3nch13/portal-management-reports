<?php ?>
					<ul>
					<?php if (AuthComponent::user('id')): ?>
						<li class="no-print"><?php echo $this->Html->link(__('Documents and Forms'),  array('controller' => 'documents', 'action' => 'index', 'admin' => false, 'manager' => false, 'saa' => false, 'plugin' => 'docs'), array('class' => 'highlighted fa fa-file-text fa-icon-only fa-fw fa-lg')); ?></li>
						<li class="no-print"><?php echo $this->Html->link(__('Edit Settings'), array('controller' => 'users', 'action' => 'edit', 'admin' => false, 'plugin' => false), array('class' => 'fa fa-cog fa-icon-only fa-fw fa-lg')); ?></li>
						<li class="no-print"><?php echo $this->Html->link(__('Logout'), array('controller' => 'users', 'action' => 'logout', 'admin' => false, 'plugin' => false), array('class' => 'fa fa-sign-out fa-icon-only fa-fw fa-lg')); ?></li>
						<li class="user-name">
							Welcome: <?php echo (AuthComponent::user('name')?AuthComponent::user('name'):AuthComponent::user('email')); ?>
						</li>
					<?php else: ?>
						<li class="no-print"><?php echo $this->Html->link(__('Login'), array('controller' => 'users', 'action' => 'login', 'admin' => false, 'plugin' => false), array('class' => 'fa fa-sign-in fa-icon-only fa-fw fa-lg')); ?></i></li>

					<?php endif; ?>
					</ul>