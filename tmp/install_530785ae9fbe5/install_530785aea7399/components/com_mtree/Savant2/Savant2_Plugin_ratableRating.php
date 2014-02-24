<?php
defined('_JEXEC') or die('Restricted access');

/**
* Base plugin class.
*/
require_once JPATH_ROOT.'/components/com_mtree/Savant2/Plugin.php';

/**
* Mosets Tree 
*
* @package Mosets Tree 3.0
* @copyright (C) 2007-2011 Lee Cher Yeong
* @url http://www.mosets.com/
* @author Lee Cher Yeong <mtree@mosets.com>
**/

class Savant2_Plugin_ratableRating extends Savant2_Plugin {

	function plugin()
	{
		global $mtree, $Itemid, $mtconf;

		list($link, $rating, $votes) = array_merge(func_get_args(), array(null));

		$database	= JFactory::getDBO();
		$my			= JFactory::getUser();
		
		$vote_ip = getenv( 'REMOTE_ADDR' );

		if ( $votes >= $mtconf->get('min_votes_to_show_rating') ) {
			$star = floor($rating);
		} else {
			$star = 0;
		}

		# Check if this user has voted before
		if ( $my->id == 0 ) {
			$database->setQuery( "SELECT log_date FROM #__mt_log WHERE link_id ='".$link->link_id."' AND log_ip = '".$vote_ip."' AND log_type = 'vote'" );
		} else {
			$database->setQuery( "SELECT log_date FROM #__mt_log WHERE link_id ='".$link->link_id."' AND user_id = '".$my->id."' AND log_type = 'vote'" );
		}
		$voted = false;
		$voted = ($database->loadResult() <> '') ? true : false;
		$html = '';

		$html .= '<div id="rating-msg">';
		# Allow rating?
		if( 
			($voted && $mtconf->get('rate_once') == '1') 
			|| 
			($mtconf->get('user_rating') == '-1') 
			|| 
			($mtconf->get('user_rating') == '1' && $my->id < 1) 
			|| 
			($mtconf->get('user_rating') == '2' && $my->id > 0 && $my->id == $link->user_id) 
			||
			($mtconf->get('user_rating') == '2' && $my->id == 0)
			|| 
			($mtconf->get('user_rating') == '2' && $link->user_id == $my->id) 
		) {
			$html .= JText::_( 'COM_MTREE_RATING' );
			$allowRating = false;
		} else {
			$html .= JText::_( 'COM_MTREE_RATE_THIS_LISTING' );
			$allowRating = true;
		}
		$html .= '</div>';
		
		// AggregateRating Schema
		$html .= '<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating" class="hidden">';
		$html .= ' <span itemprop="bestRating">5</span>';
		$html .= '<span itemprop="ratingValue">'.$star.'</span>';
		$html .= '<span itemprop="ratingCount">'.$votes.'</span>';
		// $html .= '<span itemprop="reviewCount">250</span>reviews';
		$html .= '</div>';
		
		// Print stars
		for( $i=0; $i<$star; $i++) {
			if($allowRating) {
				$html .= '<a href="javascript:rateListing('.$link->link_id.','.($i+1).');">';
				$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_10.png" width="16" height="16" hspace="1" vspace="3" border="0" id="rating'.($i+1).'" alt="★" />';
				$html .= '</a>';
			} else {
				$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_10.png" width="16" height="16" hspace="1" vspace="3" alt="★" />';
			}
		}
		
		if( ($rating-$star) >= 0.5 && $star > 0 ) {
			if($allowRating) {
				$html .= '<a href="javascript:rateListing('.$link->link_id.','.($i+1).');">';
				$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_05.png" width="16" height="16" hspace="1" vspace="3" border="0" id="rating'.($i+1).'" alt="½" />';
				$html .= '</a>';
			} else {
				$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_05.png" width="16" height="16" hspace="1" vspace="3" alt="½" />';
			}
			$star += 1;
		}

		// Print blank star
		for( $i=$star; $i<5; $i++) {
			if($allowRating) {
				$html .= '<a href="javascript:rateListing('.$link->link_id.','.($i+1).');">';
				$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_00.png" width="16" height="16" hspace="1" vspace="3" border="0" id="rating'.($i+1).'" alt="" />';
				$html .= '</a>';
			} else {
				$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_00.png" width="16" height="16" hspace="1" vspace="3" alt="" />';
			}
		}

		# Return the listing link
		return $html;

	}
}
?>