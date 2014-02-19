<?php
/**
 * @version	$Id: coreprice.php 1270 2011-11-24 02:09:12Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_coreprice extends mFieldType_number {
	var $name = 'price';
	var $dataValidator = 'validate-currency-dollar';
	
	function getOutput() {
		$price = $this->getValue();
		$displayFree = $this->getParam('displayFree',1);
		if($price == 0 && $displayFree == 1) {
			return JText::_( 'FLD_COREPRICE_FREE' );
		} else {
			return $price;
		}
	}
}
?>