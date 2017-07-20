<?php 

$page_options_title = __('Display Options');

$page_options = [];
$page_options[] = $this->Html->link(__('View %s', __('Management Report')), ['action' => 'view', $management_report['ManagementReport']['id']]);
$page_options[] = $this->Html->link(__('View %s Dashboard', __('Management Report')), ['action' => 'view_dashboard', $management_report['ManagementReport']['id']]);
$page_options[] = $this->Html->link(__('View %s %s by %s', __('User'), __('Report Items'), __('Charge Code')), ['action' => 'view_charge_code', $management_report['ManagementReport']['id']]);
$page_options[] = $this->Html->link(__('View %s %s by %s', __('User'), __('Report Items'), __('Activity')), ['action' => 'view_activity', $management_report['ManagementReport']['id']]);

$page_options_title2 = __('Actions');

$page_options2 = [];

$finalize_text = __('Review %s', __('Report'));
$page_options2[] = $this->Html->link(__('Edit %s', __('Report')), ['action' => 'edit', $management_report['ManagementReport']['id']]);
$page_options2[] = $this->Html->link($finalize_text, ['action' => 'finalize', $management_report['ManagementReport']['id']]);
$page_options2[] = $this->Html->link(__('Export Meeting Format to Excel'), ['action' => 'view', $management_report['ManagementReport']['id'], 'ext' => 'xls']);
$page_options2[] = $this->Html->link(__('Export Full Report to Excel'), ['action' => 'view', $management_report['ManagementReport']['id'], 1, 'ext' => 'xls']);

$this->set(compact(['page_options_title', 'page_options', 'page_options_title2', 'page_options2']));