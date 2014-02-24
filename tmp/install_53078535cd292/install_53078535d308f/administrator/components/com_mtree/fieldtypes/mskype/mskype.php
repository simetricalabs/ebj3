<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2012-2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_mSkype extends mFieldType {

	// $view: 1=details, 2=summary
	function getOutput($view=1)
	{
		$params['detailsButton'] = $this->getParam('detailsButton','smallicon');
		$params['summaryButton'] = $this->getParam('summaryButton','smallicon');
		$params['action'] = $this->getParam('action','call');
		
		$html = '';
		$html .= '<script type="text/javascript" src="'.$this->getFieldTypeAttachmentURL('skypeCheck.js').'"></script>';
		
		if( $this->hasValue() )
		{
			$html .= '<a href="skype:'.$this->getValue().'?'.$params['action'].'"  onclick="return skypeCheck();">';
			$html .= '<img src="http://mystatus.skype.com/';
			switch($view)
			{
				case 1:
					$html .= $params['detailsButton'];
					break;
				case 2:
					$html .= $params['summaryButton'];
					break;
			}
			$html .= '/'.$this->getValue().'" alt="'.$this->getValue().'" />';
			$html .= '</a>';
		}
		return $html;
	}

}
?>