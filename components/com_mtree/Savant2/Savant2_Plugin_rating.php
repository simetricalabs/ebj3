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
* @copyright (C) 2004-2009 Lee Cher Yeong
* @url http://www.mosets.com/
* @author Lee Cher Yeong <mtree@mosets.com>
**/


class Savant2_Plugin_rating extends Savant2_Plugin {

	function plugin($rating, $votes)
	{
		global $Itemid, $my, $mtconf;

		$rating = ($rating>5) ? 5 : $rating;

		if ( $mtconf->get('show_rating') == 1 ) {

			if ( $votes >= $mtconf->get('min_votes_to_show_rating') ) {
				$star = floor($rating);
			} else {
				$star = 0;
			}
			$html = '';

			// Print stars
			for( $i=0; $i<$star; $i++) {
				
				$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_10.png" width="14" height="14" hspace="1" class="star" alt="★" />';
			}

			if( ($rating-$star) >= 0.5 && $star > 0 ) {
				$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_05.png" width="14" height="14" hspace="1" class="star" alt="½" />';
				$star += 1;
			}

			// Print blank stars
			for( $i=$star; $i<5; $i++) {
				$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_00.png" width="14" height="14" hspace="1" class="star" alt="" />';
			}

			# Return the listing link
			return $html;
		} else {
			return '';
		}

	}
}
?>