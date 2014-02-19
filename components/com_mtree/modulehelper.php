<?php
/**
 * @version	$Id: modulehelper.php 1967 2013-07-16 05:04:58Z cy $
 * @copyright	Copyright (C) 2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class MTModuleHelper
{
	var $params = null;
	var $mtconf = null;
	var $task = null;
	
	public function setMtConf($mtconf)
	{
		$this->mtconf = $mtconf;
		return $this;
	}
	
	public function setParams($params)
	{
		$this->params = $params;
		return $this;
	}
	
	public function getTask()
	{
		if( !is_null($this->task) )
		{
			return $this->task;
		}
		else
		{
			$task		= JFactory::getApplication()->input->getCmd( 'task', '' );

			// Check 'view' instead of 'task' is used in links 
			// created through Joomla Menu Manager.
			if( empty($task) )
			{
				$task	= JFactory::getApplication()->input->getCmd( 'view', '' );
			}
			
			$this->task = $task;
			return $this->task;
		}
	}

	/**
	 * Returns true if the module is configure to show in current page
	 *
	 */
	public function isModuleShown()
	{
		$cat_id			= JFactory::getApplication()->input->getInt( 'cat_id' );
		$module_assignment	= $this->params->get( 'module_assignment' );
		$pages_assignment	= $this->params->get( 'pages_assignment' );
		$cats_assignment	= $this->params->get( 'categories_assignment', array() );

		switch( $module_assignment )
		{
			case '0':
				return true;
				break;
			case '-':
				return false;
				break;
		}

		if( is_null($pages_assignment) )
		{
			$pages_assignment = array();
		}

		if( $this->isCategoryPage() )
		{
			if( $module_assignment == '1' )
			{
				if( 
					$this->visibleInCurrentCategory() 
					&& 
					$this->visibleInCurrentPage()
				) {
					return true;
				} else {
					return false;
				}
			}
			else
			{
				if( 
					$this->visibleInCurrentCategory() 
					|| 
					$this->visibleInCurrentPage()
				) {
					return false;
				} else {
					return true;
				}
			}
		}
		elseif( $this->isListingPage() )
		{
			$tlcat_id	= $this->mtconf->getCategory();

			if( $module_assignment == '1' )
			{
				if(
					// Listings Pages are shown; and
					in_array('links',$pages_assignment)
					&&
					// the current top level category are checked to show
					in_array($tlcat_id,$cats_assignment) 
				)
				{
					return true;
				}
				else
				{
					return false;
				}				
			}
			else
			{
				if(
					// Listings Pages are shown; and
					in_array('links',$pages_assignment)
					||
					// the current top level category are checked to show
					in_array($tlcat_id,$cats_assignment) 
				)
				{
					return false;
				}
				else
				{
					return true;
				}
			}
		}
		else
		{
			if( in_array('others',$pages_assignment) )
			{
				return ($module_assignment == '1') ? true : false;
			}
			else
			{
				return ($module_assignment == '1') ? false : true;
			}
		}
	}
	
	/**
	 * Returns true if the current page is a Mosets Tree category page
	 *
	 */
	function isCategoryPage()
	{
		$option		= JFactory::getApplication()->input->getCmd( 'option', '' );
		$task		= $this->getTask();
		$cat_id		= JFactory::getApplication()->input->getInt( 'cat_id' );

		// 'view' instead of 'task' is used in links created through 
		// Joomla Menu Manager.
		if( empty($task) )
		{
			$task	= JFactory::getApplication()->input->getCmd( 'view', '' );
		}

		if( in_array($task,array('listcats','listall')) )
		{
			return true;
		}
		else if( $option == 'com_mtree' && empty($task) && is_null($cat_id) )
		{
			return true;
		}
		else if( $option == 'com_mtree' && empty($task) && is_numeric($cat_id) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function isListingPage()
	{
		$option		= JFactory::getApplication()->input->getCmd( 'option', '' );
		$task		= $this->getTask();
		$link_id	= JFactory::getApplication()->input->getInt( 'link_id' );
		
		// 'view' instead of 'task' is used in links created through 
		// Joomla Menu Manager.
		if( empty($task) )
		{
			$task	= JFactory::getApplication()->input->getCmd( 'view', '' );
		}

		if( $option == 'com_mtree' && is_int($link_id) && $link_id > 0 )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Returns true if the top level category is configured to be shown.
	 * 
	 */
	public function visibleInCurrentCategory()
	{
		// Categories Assignment
		$cats_assignment	= $this->params->get( 'categories_assignment' );

		if( !is_null($cats_assignment) )
		{
			$option		= JFactory::getApplication()->input->getCmd( 'option', '' );
			$task		= $this->getTask();
			$cat_id		= JFactory::getApplication()->input->getInt( 'cat_id', 0 );

			$this->mtconf->setCategory($cat_id);
			$tlcat_id	= $this->mtconf->getCategory();

			if( 
				$option != 'com_mtree'
				||
				!in_array($task,array('listcats','listall',''))
				|| 
				!in_array($tlcat_id,$cats_assignment) 
			)
			{
				return false;
			}
		}

		return true;
	}
	
	/**
	 * Pages in this context refers to either "Category", "Listing" or
	 * "Other" page. This function returns true if the page the user is
	 * currently on is enabled.
	 * 
	 */
	public function visibleInCurrentPage()
	{
		$option		= JFactory::getApplication()->input->getCmd( 'option', '' );
		
		// MT Pages Assignment
		$pages_assignment = $this->params->get( 'pages_assignment' );
		if( $option == 'com_mtree' && !is_null($pages_assignment) )
		{
			$tasks_map = array(
				'cats'		=> array(
					'listcats',
					'listall',
					''
				),
				'links'		=> array(
					'viewlink',
					'viewgallery',
					'writereview',
					'recommend',
					'print',
					'contact',
					'report',
					'claim',
					'deletelisting',
					'editlisting',
					'viewreviews'
				),
				'others'	=> array()
			);
			$task = $this->getTask();
			$shown_tasks = array();
			$hidden_tasks = array();

			foreach($tasks_map AS $key => $value)
			{
				if( in_array($key,$pages_assignment) ) {
					$shown_tasks = array_merge($tasks_map[$key],$shown_tasks);
				} else {
					$hidden_tasks = array_merge($tasks_map[$key],$hidden_tasks);
				}
			}

			// Take care of 'others'
			if( !in_array($task,array_merge($shown_tasks,$hidden_tasks)) ) {
				if( in_array('others',$pages_assignment) ) {
					array_push($shown_tasks,$task);
				} else {
					array_push($hidden_tasks,$task);
					return false;
				}
			}

			if( 
				!is_null($pages_assignment) 
				&& 
				!in_array($task,$shown_tasks) 
			)
			{
				return false;
			}
			else
			{
				return true;
			}
		}

		return true;
	}

	function getItemid() {
		$menu 	= JSite::getMenu();

		$items	= $menu->getItems('link', 'index.php?option=com_mtree&view=home');

		if( empty($items) )
		{
			$items	= $menu->getItems('link', 'index.php?option=com_mtree&view=listcats&cat_id=0');
		}

		return isset($items[0]) ? '&Itemid='.$items[0]->id : '';
	}
}
?>