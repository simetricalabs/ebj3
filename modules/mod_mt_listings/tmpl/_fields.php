<?php
/* $Id: _fields.php 1974 2013-07-16 09:32:08Z cy $ */ defined('_JEXEC') or die('Restricted access');

$hide_caption = array('link_rating');

// Name
if( !$hide_name && !empty($l->trimmed_link_name) )
{
	echo '<a href="' . $l->link . '" class="name'.($listingclass?' '.$listingclass:'').'">';
	echo $l->trimmed_link_name;
	echo  '</a>';
}

// Website
if ( $show_website == 1 && !empty($l->website) && !empty($l->trimmed_website) ) {
	echo "<small><a href=\"".$l->website."\">";
	echo $l->trimmed_website;
	echo "</a></small>";
}

// Category
if ( $show_category == 1 ) {
	echo "<small>".JText::_( 'MOD_MT_LISTINGS_CATEGORY' ).": <a href=\"" . $l->cat_link . "\">" . $l->cat_name . "</a></small>";
}

// Related Data
if ( $show_rel_data == 1 && $type <> 2 ) {
	echo "<small>";
	switch( $type ) {
		case 1:
			echo JText::_( 'MOD_MT_LISTINGS_CREATED' ) . ": ".JHtml::_('date', strtotime($l->link_created), 'j M Y');
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
	echo "</small>";
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
		if( in_array($field->getId(),$displayfields) && $field->hasValue() )
		{
			echo '<small>';
			if($field->hasCaption() && !in_array($field->getName(),$hide_caption)) {
				echo $field->getCaption();
				echo ': ';
			}
			$value = $field->getOutput(2);
			echo $value;
			echo '</small>';
		}
		$fields[$l->link_id]->next();
	}
}
?>