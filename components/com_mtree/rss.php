<?php
/**
 * @version		$Id: rss.php 1973 2013-07-16 09:30:13Z cy $
 * @package		Mosets Tree
 * @copyright	(C) 2005-2009 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */


defined('_JEXEC') or die('Restricted access');

// load feed creator class
require_once( JPATH_COMPONENT.'/includes/feedcreator.php');

function rss( $option, $type, $cat_id=0 ) {
	global $mtconf;
	
	$database	=& JFactory::getDBO();
	
	$info	=	null;
	$rss	=	null;
	$jdate	= JFactory::getDate();
	$now	= $jdate->toSql();
	$nullDate	= $database->getNullDate();

	$rss = new MTRSSCreator20();
	
	if ($type == 'new') {
		$filename = $mtconf->getjconf('cachepath') . '/mtreeNew' . ($cat_id?'-'.$cat_id:'') . '.xml';
	} else {
		$filename = $mtconf->getjconf('cachepath') . '/mtreeUpdated' . ($cat_id?'-'.$cat_id:'') . '.xml';
	}
	$rss->useCached($filename);
	
	switch($type) {
		case 'updated':
			$rss->title = $mtconf->getjconf('sitename') . $mtconf->get('rss_title_separator') . JText::_( 'COM_MTREE_RECENTLY_UPDATED_LISTINGS' );
			break;
		case 'new':
		default:
			$rss->title = $mtconf->getjconf('sitename') . $mtconf->get('rss_title_separator') . JText::_( 'COM_MTREE_NEW_LISTINGS' );
			break;
	}
	if($cat_id>0) {
		$mtCats = new mtCats($database);
		$cat_name = $mtCats->getName($cat_id);
		$rss->title .= $mtconf->get('rss_title_separator') . $cat_name;
	}
	
	$rss->link = JUri::root();
	$rss->cssStyleSheet	= NULL;
	$rss->feedURL = $mtconf->getjconf('live_site').$_SERVER['PHP_SELF'];

	$database->setQuery("SELECT id FROM #__menu WHERE link='index.php?option=com_mtree' AND published='1' LIMIT 1");
	$Itemid = $database->loadResult();
	
	$sql = "SELECT l.*, u.username, u.name AS owner, c.cat_id, c.cat_name FROM (#__mt_links AS l, #__mt_cl AS cl, #__users AS u, #__mt_cats AS c) "
		. "WHERE link_published='1' && link_approved='1' "
		. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$now'  ) "
		. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$now' ) "
		. "\n AND l.link_id = cl.link_id "
		. "\n AND cl.main = 1 "
		. "\n AND cl.cat_id = c.cat_id "
		. "\n AND l.user_id = u.id ";
	if($cat_id > 0) {
		$subcats = getSubCats_Recursive($cat_id);
		if(count($subcats)>1) {
			$sql .= ' AND cl.cat_id IN (' . implode(',',$subcats) . ')';
		}
	}
	switch($type) {
		case 'updated':
			$sql .= "ORDER BY l.link_modified DESC ";
			break;
		case 'new':
		default:
			$sql .= "ORDER BY l.link_created DESC ";
			break;
	}
	
	if( $mtconf->get('rss_'.$type.'_limit') > 0 )
	{
		$sql .= "LIMIT " . intval($mtconf->get('rss_'.$type.'_limit'));
	}
	
	$database->setQuery( $sql );
	$links = $database->loadObjectList();

	# Get first image of each listings
	if( $mtconf->get('show_image_rss') && !empty($links) )
	{
		foreach( $links AS $link ) {
			$link_ids[] = $link->link_id;
		}
		$database->setQuery( 'SELECT link_id, filename FROM #__mt_images WHERE link_id IN ('.implode(', ',$link_ids).') AND ordering = 1 LIMIT ' . count($link_ids) );
		$link_images = $database->loadObjectList('link_id');
	}

	# Get arrays if link_ids
	foreach( $links AS $link ) {
		$link_ids[] = $link->link_id;
	}
	
	# Additional elements from core fields
	$core_fields = array( 'cat_name', 'cat_url', 'link_votes', 'link_rating', 'address', 'city', 'postcode', 'state', 'country', 'email', 'website', 'telephone', 'fax', 'metakey', 'metadesc', 'lat', 'lng', 'zoom' );
	$additional_elements = array();
	foreach( $core_fields AS $core_field ) {
		if($mtconf->get('rss_'.$core_field)) { $additional_elements[] = $core_field; }
	}
	
	# Additional elements from custom fields
	$custom_fields = trim($mtconf->get( 'rss_custom_fields' ));
	$custom_fields_values = array();
	if( !empty($custom_fields) && count($link_ids) > 0 ) {
		$array_custom_fields = explode(',',$custom_fields);
		foreach( $array_custom_fields AS $key => $value ) {
			if( intval($value) > 0 ) {
				$array_custom_fields[$key] = intval($value);
				$additional_elements[] = 'cust_' . $array_custom_fields[$key];
			} else {
				unset($array_custom_fields[$key]);
			}
		}
		if( count($array_custom_fields) > 0 ) {
			$database->setQuery( 'SELECT cf_id, link_id, value FROM #__mt_cfvalues WHERE cf_id IN (' . implode(',',$array_custom_fields) . ') AND link_id IN (' . implode(',',$link_ids) . ') LIMIT ' . (count($array_custom_fields) * count($link_ids)) );
			$array_custom_fields_values = $database->loadObjectList();
			foreach( $array_custom_fields_values AS $array_custom_fields_value ) {
				$custom_fields_values[$array_custom_fields_value->link_id][$array_custom_fields_value->cf_id] = $array_custom_fields_value->value;
			}
		}
	}
	
	$uri =& JUri::getInstance(JUri::base());
	$host = $uri->toString(array('scheme', 'host', 'port'));

	$thumbnail_path = $mtconf->get('relative_path_to_listing_small_image');

	foreach( $links AS $link ) {
		$item = new FeedItem();
		$item->title = $link->link_name;
		$item->link = $host . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=".$link->link_id."&Itemid=".$Itemid);
		$item->guid = $host . JRoute::_("index.php?option=com_mtree&task=viewlink&link_id=".$link->link_id."&Itemid=".$Itemid);

		$item->description = '';
		if( $mtconf->get('show_image_rss') && isset($link_images[$link->link_id]) && !empty($link_images[$link->link_id]->filename) )
		{
			$item->description .= '<img align="right" src="'.$mtconf->getjconf('live_site').$thumbnail_path.$link_images[$link->link_id]->filename.'" alt="'.$link->link_name.'" />';
		}
		$item->description .= $link->link_desc;
		
		//optional
		$item->descriptionHtmlSyndicated = true;

		switch($type) {
			case 'updated':
				$item->date = ($link->link_modified ? date('r', strtotime($link->link_modified)) : '');
				break;
			case 'new':
			default:
				$item->date = ($link->link_created ? date('r', strtotime($link->link_created)) : '');
				break;
		}
		$item->source = $mtconf->getjconf('live_site');
		$item->author = $link->username;
		if(count($additional_elements)>0) {
			$ae = array();
			foreach($additional_elements AS $additional_element) {
				if( in_array($additional_element,$core_fields) ) {
					if ($additional_element == 'cat_url') {
						$ae['mtree:'.$additional_element] = htmlspecialchars(JRoute::_('index.php?option=com_mtree&task=listcats&cat_id='.$link->cat_id.'&Itemid='.$Itemid));
					} else {
						$ae['mtree:'.$additional_element] = '<![CDATA[' . $link->$additional_element . ']]>';
					}
				} else {
					$cf_id = substr( $additional_element, 5 );
					if( array_key_exists($link->link_id,$custom_fields_values) && array_key_exists($cf_id,$custom_fields_values[$link->link_id]) ) {
						$ae['mtree:'.$additional_element] = '<![CDATA[' . str_replace('|',',',$custom_fields_values[$link->link_id][$cf_id]) . ']]>';
					}
				}
			}
			$item->additionalElements = $ae;
		}
		$rss->addItem($item);
	}
	echo $rss->saveFeed($filename);
}

class MTRSSCreator20 extends RSSCreator091 {
	
	function MTRSSCreator20() {
		$this->_setRSSVersion("2.0");
		$this->contentType = "application/rss+xml";
	}
	
	function createFeed() {
		$feed = "<?xml version=\"1.0\" encoding=\"".$this->encoding."\"?>\n";
		$feed.= $this->_createStylesheetReferences();
		$feed.= "<rss version=\"".$this->RSSVersion."\"  xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xmlns:mtree=\"http://www.mosets.com/tree/rss/\">\n";
		$feed.= "<channel>\n";
		$feed.= "<title>".FeedCreator::iTrunc(htmlspecialchars($this->title),100)."</title>\n";
		$this->descriptionTruncSize = 500;
		$feed.= "<description>".$this->getDescription()."</description>\n";
		$feed.= "<link>".$this->link."</link>\n";
		$now = new FeedDate();
		$feed.= "<lastBuildDate>".htmlspecialchars($now->rfc822())."</lastBuildDate>\n";
		$feed.= "<generator>".FEEDCREATOR_VERSION."</generator>\n";

		if ($this->image!=null) {
			$feed.= "<image>\n";
			$feed.= "	<url>".$this->image->url."</url>\n";
			$feed.= "	<title>".FeedCreator::iTrunc(htmlspecialchars($this->image->title),100)."</title>\n";
			$feed.= "	<link>".$this->image->link."</link>\n";
			if ($this->image->width!="") {
				$feed.= "	<width>".$this->image->width."</width>\n";
			}
			if ($this->image->height!="") {
				$feed.= "	<height>".$this->image->height."</height>\n";
			}
			if ($this->image->description!="") {
				$feed.= "	<description>".$this->image->getDescription()."</description>\n";
			}
			$feed.= "</image>\n";
		}
		if ($this->language!="") {
			$feed.= "<language>".$this->language."</language>\n";
		}
		if ($this->copyright!="") {
			$feed.= "<copyright>".FeedCreator::iTrunc(htmlspecialchars($this->copyright),100)."</copyright>\n";
		}
		if ($this->editor!="") {
			$feed.= "<managingEditor>".FeedCreator::iTrunc(htmlspecialchars($this->editor),100)."</managingEditor>\n";
		}
		if ($this->webmaster!="") {
			$feed.= "<webMaster>".FeedCreator::iTrunc(htmlspecialchars($this->webmaster),100)."</webMaster>\n";
		}
		if ($this->pubDate!="") {
			$pubDate = new FeedDate($this->pubDate);
			$feed.= "<pubDate>".htmlspecialchars($pubDate->rfc822())."</pubDate>\n";
		}
		if ($this->category!="") {
			$feed.= "<category>".htmlspecialchars($this->category)."</category>\n";
		}
		if ($this->docs!="") {
			$feed.= "<docs>".FeedCreator::iTrunc(htmlspecialchars($this->docs),500)."</docs>\n";
		}
		if ($this->ttl!="") {
			$feed.= "<ttl>".htmlspecialchars($this->ttl)."</ttl>\n";
		}
		if (isset( $this->rating_count ) && $this->rating_count > 0) {
			$rating = round( $this->rating_sum / $this->rating_count );
			$feed.= "<rating>".FeedCreator::iTrunc(htmlspecialchars($rating),500)."</rating>\n";
		}
		if ($this->skipHours!="") {
			$feed.= "<skipHours>".htmlspecialchars($this->skipHours)."</skipHours>\n";
		}
		if ($this->skipDays!="") {
			$feed.= "<skipDays>".htmlspecialchars($this->skipDays)."</skipDays>\n";
		}
		$feed.= $this->_createAdditionalElements($this->additionalElements, "	");

		for ($i=0;$i<count($this->items);$i++) {
			$feed.= "<item>\n";
			$feed.= "	<title>".FeedCreator::iTrunc(htmlspecialchars(strip_tags($this->items[$i]->title)),100)."</title>\n";
			$feed.= "	<link>".htmlspecialchars($this->items[$i]->link)."</link>\n";
			$feed.= "	<description>".$this->items[$i]->getDescription()."</description>\n";

			if ($this->items[$i]->author!="") {
				$feed.= "	<dc:creator>".htmlspecialchars($this->items[$i]->author)."</dc:creator>\n";
			}
			if ($this->items[$i]->category!="") {
				$feed.= "	<category>".htmlspecialchars($this->items[$i]->category)."</category>\n";
			}
			if ($this->items[$i]->comments!="") {
				$feed.= "	<comments>".htmlspecialchars($this->items[$i]->comments)."</comments>\n";
			}
			if ($this->items[$i]->date!="") {
				$itemDate = new FeedDate($this->items[$i]->date);
				$feed.= "	<pubDate>".htmlspecialchars($itemDate->rfc822())."</pubDate>\n";
			}
			if ($this->items[$i]->guid!="") {
				$feed.= "	<guid>".htmlspecialchars($this->items[$i]->guid)."</guid>\n";
			}
			$feed.= $this->_createAdditionalElements($this->items[$i]->additionalElements, "	");
			$feed.= "</item>\n";
		}
		$feed.= "</channel>\n";
		$feed.= "</rss>\n";
		return $feed;
	}
}

?>