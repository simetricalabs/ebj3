<?php
/**
 * @version	$Id: captcha.php 1270 2011-11-24 02:09:12Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2012-2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_captcha extends mFieldType
{
	var $numOfSearchFields = 0; 
	
	function getInputHTML()
	{
		if( $this->inBackEnd() || $this->getLinkId() > 0 )
		{
			return false;
		}
		else
		{
			$plugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));

			if (($captcha = JCaptcha::getInstance($plugin, array('namespace' => 'xnamespace'))) == null)
			{
				return '';
			}

			$html = '';
			$html .= $captcha->display($this->getInputFieldName(1), $this->getInputFieldId(1)); 
			$html .= '<input';
			$html .= ' type="hidden"';
			$html .= ($this->isRequired() ? ' required':'');
			$html .= ' name="'.$this->getInputFieldName(1).'"';
			$html .= ' id="'.$this->getInputFieldID(1).'"';
			$html .= ' value=""';
			$html .= ' />';

			return $html;
		}
	}

	function getJSPresubmitFunction()
	{
		$js = '';
		$js .= 'if(typeof document.mtForm.'.$this->getInputFieldName(1).' != \'undefined\'){document.mtForm.'.$this->getInputFieldName(1).'.value=document.mtForm.recaptcha_response_field.value;}';

		return 'function(){'.$js.'}';
	}

	/**
	 * Show Captcha input in front-end only. When viewing in back-end, 
	 * hasInputField returns 0 so that it is not shown in back-end.
	 */
	function hasInputField()
	{
		if( $this->inBackEnd() || $this->getLinkId() > 0 )
		{
			return false;
		}
		else
		{
			return ( $this->numOfInputFields <= 0 ) ? false:true;
		}
	}
	
	function validateValue( $value )
	{
		$plugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));
		
		$captcha = JCaptcha::getInstance($plugin);

		// Test the value.
		if (!$captcha->checkAnswer($value))
		{
			$error = $captcha->getError();
			if ($error instanceof Exception)
			{
				$this->setError($error);
				return false;
			}
			else
			{
				$this->setError(new JException($error));
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Returns an empty string since we do not need to store the value.
	 */
	function parseValue( $value )
	{ 
		return '';
	}
	
	function hasValue()
	{
		return false;
	}
	function getValue()
	{
		return '';
	}
}
?>