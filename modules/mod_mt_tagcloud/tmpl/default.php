<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php
if( empty($tags) ) return; 

echo JHtml::stylesheet('mod_mt_tagcloud/mod_mt_tagcloud.css',array(),true, false);

?><ol class="tagcloud">
<?php

	foreach( $tags AS $tag )
	{
		echo '<li>';
		echo '<a href="'.$tag->link.'">';
		echo $tag->value;
		// echo ' ('.$tag->items.')';
		echo '</a>';
		echo '</li>';
	}
	
?></ol>