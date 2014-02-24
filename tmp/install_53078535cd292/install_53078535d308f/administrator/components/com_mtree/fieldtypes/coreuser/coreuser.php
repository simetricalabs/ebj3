<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_coreuser extends mFieldType {
	var $name = 'user_id';
	var $numOfSearchFields = 0;
	var $numOfInputFields = 0;
	
	function getOutput($view=1) {
		$html = '<a rel="author" href="' . JRoute::_('index.php?option=com_mtree&amp;task=viewowner&amp;user_id=' . $this->getValue(1)) . '">';
		$html .= $this->getValue(2);
		$html .= '</a>';
		return $html;
	}
}

?>