<?php
/**
 * @version	$Id: config.mtree.class.php 1973 2013-07-16 09:30:13Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */


defined('_JEXEC') or die('Restricted access');

if ( !class_exists('mtConfig') ) {
	class mtConfig {
		var $_db=null;
		var $mtconfig=null;
		var $jconfig=null;
		var $params=null;
		var $category=null;
		
		function mtConfig() {
			$this->_db = JFactory::getDBO();
			$this->_db->setQuery( 'SELECT `varname`, `value`, `default` FROM #__mt_config' );
			$this->mtconfig = $this->_db->loadObjectList('varname');

			$app = JFactory::getApplication();
			
			$this->jconfig['absolute_path'] = JPATH_SITE;
			if(substr(JUri::root(),-1) == '/') {
				$this->jconfig['live_site'] = substr(JUri::root(),0,-1);
			} else {
				$this->jconfig['live_site'] = JUri::root();
			}
			$this->jconfig['sitename'] = $app->getCfg('sitename');
			$this->jconfig['offset'] = $app->getCfg('offset');
			$this->jconfig['MetaTitle'] = $app->getCfg('MetaTitle');
			$this->jconfig['MetaAuthor'] = $app->getCfg('MetaAuthor');
			$this->jconfig['list_limit'] = $app->getCfg('list_limit');
			$this->jconfig['sef'] = $app->getCfg('sef');
			$this->jconfig['cachepath'] = JPATH_BASE.'/cache';
			$this->jconfig['mailfrom'] = $app->getCfg('mailfrom');
			$this->jconfig['fromname'] = $app->getCfg('fromname');
			$this->cat_params = new JRegistry();
		}

		function get($varname){
			if( !is_null($this->cat_params) && $this->cat_params->exists($varname) ) {
				$value = $this->cat_params->get($varname);
			} elseif( array_key_exists($varname,$this->mtconfig) ) {
				$value = $this->mtconfig[$varname]->value;
			} else {
				$value = '';
			}
		  	if (
				((is_null($value) || (is_string($value) && trim($value) == "")) && $value !== false) 
				||
				(is_array($value) && empty($value))
			) {
				return $this->getDefault($varname);
			} else {
				return $value;
			}
		}
		
		function set($varname,$value) {
			$this->mtconfig[$varname]->value = $value;
		}
		
		function setCategory($cat_id)
		{	
			if( $cat_id === null ) { $cat_id = 0; }
			
			$this->_db->setQuery('SELECT cat_id, cat_parent, metadata, lft, rgt FROM #__mt_cats WHERE cat_id = ' . $cat_id . ' LIMIT 1');
			$category = $this->_db->loadObject();

			if( !is_null($category) )
			{
				// This is a top level category. Proceed to load its metadata
				if( $category->cat_parent <= 0 ) {
					$this->cat_params->loadString($category->metadata,'JSON');
					$this->category = $category->cat_id;
				// This is not a top level category. Do another query to retrieve top level category's metadata
				} else {
					$this->_db->setQuery(
						"SELECT cat_id, metadata FROM #__mt_cats "
						. "\nWHERE lft < " . $category->lft . " && rgt > " . $category->rgt . " && cat_parent >= 0"
						. "\nORDER BY lft ASC LIMIT 1"
						);
					$result = $this->_db->loadObject();

					if( !is_null($result) )
					{
						$this->cat_params->loadString($result->metadata);
						$this->category = $result->cat_id;
					}
				}				
			}
		}
		
		function getCategory()
		{
			return $this->category;
		}
		
		function setTemplate($template){
			$this->mtconfig['template']->value = $template;

			$this->_db->setQuery('SELECT params FROM #__mt_templates WHERE tem_name = ' . $this->_db->quote($template) . ' LIMIT 1');
			$params = $this->_db->loadResult();
			$this->params = new JRegistry( $params );
		}

		function getjconf($varname){
			return $this->jconfig[$varname];
		}

		function getTemParam($key,$default='') {
			if(is_null($this->params)) {
				$this->_db->setQuery('SELECT params FROM #__mt_templates WHERE tem_name = ' . $this->_db->quote($this->get('template')) . ' LIMIT 1');
				$params = $this->_db->loadResult();
				$this->params = new JRegistry( $params );
			}
			return $this->params->get( $key, $default );
		}
	
		function getDefault($varname){
			if( isset($this->mtconfig[$varname]->default) ) {
				return $this->mtconfig[$varname]->default;
			} else {
				return null;
			}
		}

		function getVarArray() {
			foreach( $this->mtconfig AS $key=>$value) {
				if( !is_null($this->cat_params) && $this->cat_params->exists($key) )
				{
					$vars[$key] = $this->cat_params->get($key);

				} elseif (
					(
						(is_null($value->value) || trim($value->value) == "") 
						&& 
						$value->value !== false
					) 
					||
					(
						is_array($value->value) && empty($value->value)
					)
				) {
					$vars[$key] = $this->getDefault($key);
				} else {
					$vars[$key] = $value->value;
				}
			}
			return $vars;
		}
	
		function getJVarArray() {
			foreach( $this->jconfig AS $key=>$value) {
				$vars[$key] = $value;
			}
			return $vars;
		}
	}
}
?>