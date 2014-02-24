<?php
defined('_JEXEC') or die('Restricted access');

/**
* Base plugin class.
*/
require_once JPATH_ROOT.'/components/com_mtree/Savant2/Plugin.php';

/**
* Mosets Tree 
*
* @package Mosets Tree 2.0
* @copyright (C) 2007 Lee Cher Yeong
* @url http://www.mosets.com/
* @author Lee Cher Yeong <mtree@mosets.com>
**/


class Savant2_Plugin_review_rating extends Savant2_Plugin {

	function plugin()
	{
		global $mtconf;

		list($rating) = func_get_args();

		if( $rating > 0 && $rating <= 5 ) {
			$star = round($rating, 0);
			$html = '';
			
			// Rating schema
			$html .= '<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="hidden">';
			$html .= '<meta itemprop="worstRating" content="1">';
			$html .= '<span itemprop="ratingValue">'.$star.'</span>/';
			$html .= '<span itemprop="bestRating">5</span>';
			$html .= '</div>';
			
			// Print starts
			for( $i=0; $i<$star; $i++) {
				$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_10.png" width="16" height="16" hspace="1" alt="★" />';
			}

			// Print blank star
			for( $i=$star; $i<5; $i++) {
				$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_00.png" width="16" height="16" hspace="1" alt="" />';
			}

			# Return the listing link
			return $html;
		} else {
			return '';
		}

	}
}
?>