<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2011-2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_coreprice extends mFieldType_number {
	var $name = 'price';
	var $dataValidator = 'validate-currency-dollar';
	
	function getOutput($view=1) {
		$price = $this->getValue();
		$whenPriceIs0 = $this->getParam('whenPriceIs0',3);

		if($price == 0 && $whenPriceIs0  == 2) {
			return JText::_( 'FLD_COREPRICE_FREE' );
		} else {
			return $price;
		}
	}

	function hasValue() {
		$whenPriceIs0 = $this->getParam('whenPriceIs0',3);
		$price = $this->getValue();

		if( $whenPriceIs0 == 3 && $price == 0 )
		{
			return false;
		} else {
			return true;
		}
	}

}
?>