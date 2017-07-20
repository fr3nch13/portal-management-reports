<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

// load for all models
App::uses('AuthComponent', 'Controller/Component');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model 
{
	public $actsAs = array(
		'Containable', 
		'Utilities.Common', 
		'Utilities.Extractor', 
		'Utilities.Foapi', 
		'Utilities.Rules', 
		'Utilities.Shell', 
		'Search.Searchable', 
		'Ssdeep.Ssdeep', 
		'OAuthClient.OAuthClient' => array(
			'redirectUrl' => array('plugin' => false, 'controller' => 'users', 'action' => 'login', 'admin' => false)
		),
		// used for avatar management
		'Upload.Upload' => array(
			'photo' => array(
				'deleteOnUpdate' => true,
				'thumbnailSizes' => array(
					'big' => '200x200',
					'medium' => '120x120',
					'thumb' => '80x80',
					'small' => '40x40',
					'tiny' => '16x16',
				),
			),
		),
    );
	
	public function afterFind($results = array(), $primary = false)
	{
	
		foreach($results as $i => $result)
		{
			/// fix the color stuff
			if(isset($results[$i][$this->alias]['color_code_hex']))
			{
				if(!$results[$i][$this->alias]['color_code_hex']) 
				{
					$results[$i][$this->alias]['color_code_hex']  = $this->makeColorCode($result[$this->alias][$this->displayField]);
				}
				
				if(isset($result[$this->alias]['color_code_hex']))
				{
					$results[$i][$this->alias]['color_code_rgb'] = $this->makeRGBfromHex($results[$i][$this->alias]['color_code_hex']);
				}
			}
		}
		
		return parent::afterFind($results, $primary);
	}
	
	public function listForSortable($first = false, $returnAsList = false, $specific_ids = false)
	{
		$attributes = array(
			'recursive' => -1,
			'order' => array($this->alias.'.'. $this->displayField => 'asc'),
		);
		
		if($specific_ids and is_array($specific_ids))
		{
			$attributes['conditions'] = array(
				$this->alias.'.'. $this->primaryKey => $specific_ids,
			);
		}
		
		$items = $this->find('all', $attributes);
		
		$new_items = array();
		
		if($first)
		{
			if(!$returnAsList)
			{
				$new_items[] = array(
					'value' => 0,
					'name' => $first,
				);
			}
			else
			{
				$new_items[0] = $first;
			}
		}
		
		foreach($items as $item)
		{
			if(!$returnAsList)
			{
				$new_items[] = array(
					'value' => $item[$this->alias][$this->primaryKey],
					'name' => $item[$this->alias][$this->displayField],
					'style' => 'background-color: '. ((isset($item[$this->alias]['color_code_rgb']) and $item[$this->alias]['color_code_rgb'])?$item[$this->alias]['color_code_rgb']: $this->makeRGBfromHex('ffffff')),
				);
			}
			else
			{
				$key = $item[$this->alias][$this->primaryKey];
				$new_items[$key] = $item[$this->alias][$this->displayField];
			}
		}
		
		return $new_items;
	}
	
	public function makeRGBfromHex($color = false, $opacity = '0.3')
	{
		if(!$color)
		{
			return 'rgb(255, 255, 255)';
		}
		
		$color = str_replace('#', '', $color);
		
		$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		$rgb =  array_map('hexdec', $hex);
		
		if($opacity)
		{
			if(abs($opacity) > 1)
        		$opacity = 1.0;
			
			$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
		}
		else
		{
			$output = 'rgb('.implode(",",$rgb).')';
		}
		
		return $output;
	}
	
	public function makeColorCode($string = false)
	{
		
		$string = md5($string);
		$color = strtolower(substr($string, 0, 6));
		
		return '#'. $color;
	}
	
	public function stats()
	{
	/*
	 * Default placeholder if no stats function is available for a Model
	 */
		return array();
	}
}
