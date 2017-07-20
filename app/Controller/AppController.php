<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
 * @link          http://cakephp.org CakePHP(tm) ChargeCode
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */


App::uses('CommonAppController', 'Utilities.Controller');
class AppController extends CommonAppController
{
	public $components = array(
		'Auth' => array(
			'loginRedirect' => array('controller' => 'daily_reports', 'action' => 'index', 'admin' => false, 'manager' => false, 'plugin' => false),
		),
	);
	
	public $helpers = array(
		'Local',
		'PhpExcel' => array('className' => 'PhpExcel.PhpExcel'),
	);
	
	public $bypassReferer = true;
	
	public function beforeFilter()
	{
		if($this->request->params['controller'] == 'management_reports_report_items' or $this->request->isAjax() or (isset($this->request->params['requested']) and $this->request->params['requested']))
		{
			return parent::beforeFilter();
		}
		
		// make sure there aren't any management report items that need to be reviewed
		$this->loadModel('ManagementReportsReportItem');
		
		$conditions = array(
			'ReportItem.user_id' => AuthComponent::user('id'),
			'ReportItem.review' => true,
			'ReportItem.reviewed' => false,
			'ManagementReport.id >' => 0,
		);
		
		$count = $this->ManagementReportsReportItem->find('count', array(
			'contain' => array('ReportItem', 'ManagementReport'),
			'conditions' => array(
				'ReportItem.user_id' => AuthComponent::user('id'),
				'ReportItem.review' => true,
				'ReportItem.reviewed' => false,
				'ManagementReport.id >' => 0,
			)
		));
		
		if($count)
		{
			$this->Flash->warning(__('You have %s %s that need to be reviewed, please take care of them first.', $count, __('Report Items')));
			return $this->redirect(array(
				'controller' => 'management_reports_report_items',
				'action' => 'review',
				'admin' => false,
				'plugin' => false,
			));
		}
		return parent::beforeFilter();
	}
	
	public function gridedit()
	{
		// to mark dates as manually set
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if(isset($this->request->data['ReportItem']['item_date']))
			{
				$this->request->data['ReportItem']['item_date_set'] = true;
			}
		}
		
		return parent::gridedit();
	}
	
	public function gridadd()
	{
		// to mark dates as manually set
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if(isset($this->request->data['ReportItem']['item_date']))
			{
				$this->request->data['ReportItem']['item_date_set'] = true;
			}
		}
		
		return parent::gridadd();
	}
}