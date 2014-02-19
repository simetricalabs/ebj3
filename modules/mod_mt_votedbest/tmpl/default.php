<?php defined('_JEXEC') or die('Restricted access'); ?>
<style type="text/css">
.mod-mt-votedbest th {text-align:left;}
.mod-mt-votedbest .mod-mt-votedbest-votes{text-align:center;}
</style>
<div class="mod-mt-votedbest">
<table width="100%" border="0" cellpadding="1" cellspacing="0">
<?php 

if ($show_header) { 
?><tr><?php
	for( $i=1; $i<=count($order); $i++ ) {
		if ( $i == $order["rank"] )	{ echo '<th width="5%" class="mod-mt-votedbest-rank">'.$caption_rank.'</th>'; }
		if ( $i == $order["name"] )	{ echo '<th width="35%" class="mod-mt-votedbest-name">'.JText::_( 'MOD_MT_VOTEDBEST_COLUMN_NAME' ).'</th>'; }
		if ( $i == $order["category"] )	{ echo '<th width="35%" class="mod-mt-votedbest-category">'.JText::_( 'MOD_MT_VOTEDBEST_COLUMN_CATEGORY' ).'</th>'; }
		if ( $i == $order["rating"] )	{ echo '<th width="12%" class="mod-mt-votedbest-rating">'.JText::_( 'MOD_MT_VOTEDBEST_COLUMN_RATING' ).'</th>'; }
		if ( $i == $order["votes"] )	{ echo '<th width="12%" class="mod-mt-votedbest-votes">'.JText::_( 'MOD_MT_VOTEDBEST_COLUMN_VOTES' ).'</th>'; }
	}
?></tr><?php
}

$tabclass = array( 'sectiontableentry1', 'sectiontableentry2' );
$rank = 1;
$k=0;
foreach( $listings AS $l ) {

	if ( $use_alternating_bg ) {
		echo '<tr class="'.$tabclass[$k].'">';
	}	else {
		echo '<tr>';
	}

	for( $i=1; $i<=count($order); $i++ )
	{
		if ( $i == $order["rank"] )		{ echo '<td class="mod-mt-votedbest-rank">'.$rank.'</td>'; }
		if ( $i == $order["name"] ) 		{ echo '<td nowrap class="mod-mt-votedbest-name"><a href="' . $l->link . '">' . $l->trimmed_link_name . '</a></td>'; }
		if ( $i == $order["category"] ) 	{ echo '<td nowrap class="mod-mt-votedbest-category"><a href="' . $l->cat_link . '">'. $l->category . '</a></td>'; }
		if ( $i == $order["votes"] ) 		{ echo '<td class="mod-mt-votedbest-votes">'.$l->link_votes.'</td>'; }
		if ( $i == $order["rating"] )
		{
			echo '<td class="mod-mt-votedbest-rating">';
			if ( $l->link_rating >= $mtconf->get('min_votes_to_show_rating') ) {
				$star = floor($l->link_rating);
			} else {
				$star = 0;
			}
			$html = '';

			// Print stars
			for( $j=0; $j<$star; $j++) {

				$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_10.png" width="14" height="14" hspace="1" class="star" alt="★" />';
			}

			if( ($l->link_rating-$star) >= 0.5 && $star > 0 ) {
				$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_05.png" width="14" height="14" hspace="1" class="star" alt="½" />';
				$star += 1;
			}

			// Print blank stars
			for( $j=$star; $j<5; $j++) {
				$html .= '<img src="'.$mtconf->getjconf('live_site').$mtconf->get('relative_path_to_rating_image').'star_00.png" width="14" height="14" hspace="1" class="star" alt="" />';
			}

			echo $html;
			echo '</td>'; 
		}
	}
	echo '</tr>';	
	$rank++;
	$k = 1 - $k;
}

if ( $show_more ) {
	echo '<tr><td colspan="4"><a href="' . $show_more_link . '">' . $caption_showmore . '</a></td></tr>';	
}

?></table>
</div>