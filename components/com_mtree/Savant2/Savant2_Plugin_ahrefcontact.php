<?php
/**
* Mosets Tree 
*
* @package Mosets Tree 1.50
* @copyright (C) 2005 Mosets Consulting
* @url http://www.mosets.com/
* @author Lee Cher Yeong <mtree@mosets.com>
**/
defined('_JEXEC') or die('Restricted access');

//Base plugin class.
require_once JPATH_ROOT.'/components/com_mtree/Savant2/Plugin.php';

class Savant2_Plugin_ahrefcontact extends Savant2_Plugin {
	
	function plugin( $link, $attr=null )
	{
		global $Itemid, $mtconf;

		# Load Parameters
		$params = new JRegistry( $link->attribs );
		$params->def( 'show_contact', $mtconf->get('show_contact') );
		$params->def( 'use_owner_email', $mtconf->get('use_owner_email') );
		
		$html = '';
		
		if ( 
			(
				// Contact is enabled, link has e-mail
				($params->get( 'show_contact' ) == 1 && $link->email <> '') 
				OR 
				// Contact is enabled, use_owner_email is enabled
				( $params->get( 'show_contact' ) == 1 && $params->get( 'use_owner_email' ) == 1 && $link->user_id > 0 ) 
			)
			AND
			(
				$mtconf->get('user_contact') != -1
			)
			AND
			(
				$mtconf->get('contact_form_location') == 1
			)
		) {

			// $html = '<img src="images/M_images/indent1.png" width="9" height="9" />';

			$html .= '<a href="';

			$html .= JRoute::_( 'index.php?option=com_mtree&task=contact&link_id='.$link->link_id);
			
			$html .= '"';

			# Insert attributes
			if (is_array($attr)) {
				// from array
				foreach ($attr as $key => $val) {
					$key = htmlspecialchars($key);
					$val = htmlspecialchars($val);
					$html .= " $key=\"$val\"";
				}
			} elseif (! is_null($attr)) {
				// from scalar
				$html .= " $attr";
			}
			
			$html .= '>'.JText::_( 'COM_MTREE_CONTACT_OWNER' )."</a>";

		}
		return $html;

	}

}
?>