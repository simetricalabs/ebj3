<?php
/**
 * @version	$Id: coretelephone.php 2011 2013-08-02 11:10:35Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_coretelephone extends mFieldType
{
	var $name = 'telephone';
	
	function getInputHTML()
	{
		$html = '';
		$html .= '<input'.($this->isRequired() ? ' required':'');
		$html .= ' class="'.($this->isRequired() ? ' required':'').'"';
		$html .= ' type="tel" name="' . $this->getInputFieldName(1) . '"';
		$html .= ' id="' . $this->getInputFieldID(1) . '"';
		$html .= ' size="' . ($this->getSize()?$this->getSize():'30') . '"';
		$html .= ' value="' . htmlspecialchars($this->getInputValue()) . '"';
		$html .= ' />';
		return $html;
	}

	function getOutput()
	{
		if( $this->hasValue() )
		{
			$showLink = $this->getParam('showLink', 0);
			$html = '';

			if( $showLink )
			{
				$html .= '<a href="tel:'.$this->getValue().'">';
				$html .= $this->getValue();
				$html .= '</a>';
			} else {
				$html .= $this->getValue();
			}
			return $html;
		}
		else
		{
			return $this->getValue();
		}
	}
}

?>