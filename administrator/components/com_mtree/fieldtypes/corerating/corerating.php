<?php
/**
 * @version	$Id: corerating.php 1849 2013-03-28 11:12:26Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2011-2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mFieldType_corerating extends mFieldType_number
{
	var $name = 'link_rating';
	var $numOfSearchFields = 2;
	var $numOfInputFields = 0;

	function getOutput($view=1) {
		global $mtconf;

		$star = round($this->getValue(), 0);
		$html = '';

		// Print stars
		for( $i=0; $i<$star; $i++) {
			$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_10.png" width="16" height="16" hspace="1" alt="Star10" />';
		}
		// Print blank star
		for( $i=$star; $i<5; $i++) {
			$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_00.png" width="16" height="16" hspace="1" alt="Star00" />';
		}
		
		return $html;
	}
	
	function getJSValidation() {
		return null;
	}
}
?>