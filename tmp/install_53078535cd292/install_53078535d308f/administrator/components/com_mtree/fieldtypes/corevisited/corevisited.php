<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_corevisited extends mFieldType_number {
	var $name = 'link_visited';
	var $numOfInputFields = 0;
	function getJSValidation() {
		return null;
	}
}

?>