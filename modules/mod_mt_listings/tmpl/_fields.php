<?php
/* $Id: _fields.php 1824 2013-03-05 09:52:34Z cy $ */ defined('_JEXEC') or die('Restricted access');

// Name

	echo '<div class="name">';


if( !$hide_name && !empty($l->trimmed_link_name) )
{
	echo '<a href="' . $l->link . '" >';
	echo $l->trimmed_link_name;
	echo  '</a>';
}
	echo '</div>';
	echo '</div>';

			
// Website
if ( $show_website == 1 && !empty($l->website) && !empty($l->trimmed_website) ) {
	echo "<a href=\"".$l->website."\">";
	echo $l->trimmed_website;
	echo "</a>";
}

// Category
if ( $show_category == 1 ) {
	echo "<small>".JText::_( 'MOD_MT_LISTINGS_CATEGORY' ).": <a href=\"" . $l->cat_link . "\">" . $l->cat_name . "</a></small>";
}

// Related Data
if ( $show_rel_data == 1 && $type <> 2 ) {

	switch( $type ) {
		case 1:
			echo JText::_( 'MOD_MT_LISTINGS_CREATED' ) . ": ".JHTML::_('date', strtotime($l->link_created), 'j M Y');
			break;
		case 3:
			echo JText::_( 'MOD_MT_LISTINGS_HITS' ) . ": ".$l->link_hits;
			break;
		case 4:
			echo JText::_( 'MOD_MT_LISTINGS_VOTES' ) . ": ".$l->link_votes;
			break;
		case 5:
			$star = round($l->link_rating, 0);
			// Print stars
			for( $i=0; $i<$star; $i++) {
				echo '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_10.png" width="16" height="16" hspace="1" alt="Star10" />';
			}
			// Print blank star
			for( $i=$star; $i<5; $i++) {
				echo '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_00.png" width="16" height="16" hspace="1" alt="Star00" />';
			}
			break;
		case 6:
			echo JText::_( 'MOD_MT_LISTINGS_REVIEWS' ) . ": ".$l->reviews;
			break;
	}

}

// Custom fields
$displayfields = $params->get( 'fields', array() );
if( !is_array($displayfields) ) {
	$displayfields = array($displayfields);
}

if( !empty($displayfields) && isset($fields[$l->link_id]) )
{

	$fields[$l->link_id]->resetPointer();
	while( $fields[$l->link_id]->hasNext() ) {
		$field = $fields[$l->link_id]->getField();
		
		switch( $field->getID() ) {
			case 29:
				$short_desc = $field->getOutput(2);
			break;
			case 30:
				$serv_type = $field->getOutput(2);
			break;
			case 31:
				$segment = $field->getOutput(2);
			break;			
			case 12:
				$homepage = $field->getOutput(2);
			break;
			case 33:
				$twitter = $field->getOutput(2);
			break;
			case 32:
				$facebook = $field->getOutput(2);
			break;
			case 34:
				$google = $field->getOutput(2);
			break;
			case 24:
				$logo = $field->getOutput(2);
			break;
			case 7:
				$country = $field->getOutput(2);
			break;
			case 5;
				$city = $field->getOutput(2);
			break;																					
		}	

		
		$fields[$l->link_id]->next();
	}

}
//Short Description
	echo '<div class="pitch tiptip_bottom" data-hasqtip="0" >';
	echo '	<div style="margin: 0px; padding: 0px; border: 0px;">';
	echo $short_desc;
	echo '	</div>';
	echo '</div>';

	echo '</div>';
	
	echo '<div class="mini">';
//Location
	echo '	<div class="tag">';
	echo '		<div class="type">';
	echo $country.', '.$city;	
	echo '		</div>';
	echo '	</div>';
//Service Type
	echo '	<div class="tag">';
	echo '		<div class="type">';
	echo $serv_type;	
	echo '		</div>';
	echo '	</div>';	
//Segment
	echo '	<div class="tag">';
	echo '		<div class="type">';
	echo $segment;	
	echo '		</div>';
	echo '	</div>';
					
	echo '</div>';
	
	$facebook = strstr($facebook, '"');
	$facebook =substr($facebook,1,strpos($facebook,"target")-3);

	$google = strstr($google, '"');
	$google =substr($google,1,strpos($google,"target")-3);

	echo '<div class="links">';
	echo '<a class="tag" target="_blank" href="'.$l->website.'">Visitar Sitio Web</a>';
	echo '<div class="social_links">';
	if(!empty($facebook))	
		echo '<a class="linkopacity" target="_blank" href="'.$facebook.'">	<img src="/images/facebook.png" alt="Facebook '.$l->trimmed_link_name.'" ></a>';
		
	if(!empty($google))	
		echo '<a class="linkopacity" target="_blank" href="'.$google.'">	<img src="/images/google_follow.png" alt="Google+ '.$l->trimmed_link_name.'" ></a>';
	
	if(!empty($twitter))
		echo '<a class="linkopacity" target="_blank" href="'.'https://twitter.com/'.$twitter.'">	<img src="/images/twitter.png" alt="Twitter '.$l->trimmed_link_name.'" ></a>';
	
	echo '</div>';						
	echo '</div>';	
?>
