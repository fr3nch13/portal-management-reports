<?php ?>

				<div class="form">
				<?php echo $this->Form->create('User', array('type' => 'file'));?>
					<fieldset>
						<legend><?php echo __('Add/change Avatar'); ?></legend>
						<?php
							echo $this->Avatar->view($this->request->data, 'medium');
							echo $this->Form->input('id', array('type' => 'hidden'));
							echo $this->Form->input('photo', array('type' => 'file', 'label' => __('Upload Avatar Image')));
						?>
					</fieldset>
				<?php echo $this->Form->end(__('Upload Image'));?>
				</div>