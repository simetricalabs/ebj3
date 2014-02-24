<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2005-2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

class mtListListing {

	var $task=null;
	var $list=null;
	var $subcats=null;
	var $now=null;
	var $limit=null;
	var $limitstart=0;
	var $link_id=null;
	var $link_ids=null;
	var $link_ids_results=null;
	var $sort=null;
	var $sql_order_by=null;
	var $allowed_sort = array('link_featured','link_created','link_modified','link_hits','link_visited','link_rating','link_votes','link_name','price');
	
	function mtListListing( $task ) {
		global $mtconf;
		$this->task = $task;
		$jdate		= JFactory::getDate();
		$this->now	= $jdate->toSql();
		$this->list = array(
			'listassociated' => array (
				'title_lang_key'	=> 'PAGE_TITLE_ASSOCIATED_LISTINGS',
				'header_lang_key'	=> 'ASSOCIATED_LISTINGS',
				'viewmore_lang_key'	=> 'VIEW_MORE_ASSOCIATED_LISTINGS',
				'title'			=> JText::_( 'COM_MTREE_PAGE_TITLE_ASSOCIATED_LISTINGS' ),
				'header'		=> JText::_( 'COM_MTREE_ASSOCIATED_LISTINGS' ),
				'viewmore'		=> JText::_( 'COM_MTREE_VIEW_MORE_ASSOCIATED_LISTINGS' ),
				'name'			=> 'associated'
				),
			'listall' => array(
				'title_lang_key'	=> 'PAGE_TITLE_ALL_LISTINGS',
				'header_lang_key'	=> 'ALL_LISTINGS',
				'viewmore_lang_key'	=> 'VIEW_MORE_ALL_LISTINGS',
				'title'			=> JText::_( 'COM_MTREE_PAGE_TITLE_ALL_LISTINGS' ),
				'header'		=> JText::_( 'COM_MTREE_ALL_LISTINGS' ),
				'viewmore'		=> JText::_( 'COM_MTREE_VIEW_MORE_ALL_LISTINGS' ),
				'name'			=> 'all'
				),
			'listpopular' => array(
				'title_lang_key'	=> 'PAGE_TITLE_POPULAR_LISTINGS',
				'header_lang_key'	=> 'POPULAR_LISTINGS',
				'viewmore_lang_key'	=> 'VIEW_MORE_POPULAR_LISTINGS',
				'title'			=> JText::_( 'COM_MTREE_PAGE_TITLE_POPULAR_LISTINGS' ),
				'header'		=> JText::_( 'COM_MTREE_POPULAR_LISTINGS' ),
				'viewmore'		=> JText::_( 'COM_MTREE_VIEW_MORE_POPULAR_LISTINGS' ),
				'name'			=> 'popular'
				),
			'listmostrated' => array(
				'title_lang_key'	=> 'PAGE_TITLE_MOST_RATED_LISTINGS',
				'header_lang_key'	=> 'MOST_RATED_LISTINGS',
				'viewmore_lang_key'	=> 'VIEW_MORE_MOST_RATED_LISTINGS',
				'title'			=> JText::_( 'COM_MTREE_PAGE_TITLE_MOST_RATED_LISTINGS' ),
				'header'		=> JText::_( 'COM_MTREE_MOST_RATED_LISTINGS' ),
				'viewmore'		=> JText::_( 'COM_MTREE_VIEW_MORE_MOST_RATED_LISTINGS' ),
				'name'			=> 'mostrated'
				),
			'listtoprated' => array(
				'title_lang_key'	=> 'PAGE_TITLE_TOP_RATED_LISTINGS',
				'header_lang_key'	=> 'TOP_RATED_LISTINGS',
				'viewmore_lang_key'	=> 'VIEW_MORE_TOP_RATED_LISTINGS',
				'title'			=> JText::_( 'COM_MTREE_PAGE_TITLE_TOP_RATED_LISTINGS' ),
				'header'		=> JText::_( 'COM_MTREE_TOP_RATED_LISTINGS' ),
				'viewmore'		=> JText::_( 'COM_MTREE_VIEW_MORE_TOP_RATED_LISTINGS' ),
				'name'			=> 'toprated'
				),
			'listmostreview' => array(
				'title_lang_key'	=> 'PAGE_TITLE_MOST_REVIEWED_LISTINGS',
				'header_lang_key'	=> 'MOST_REVIEWED_LISTINGS',
				'viewmore_lang_key'	=> 'VIEW_MORE_MOST_REVIEWED_LISTINGS',
				'title'			=> JText::_( 'COM_MTREE_PAGE_TITLE_MOST_REVIEWED_LISTINGS' ),
				'header'		=> JText::_( 'COM_MTREE_MOST_REVIEWED_LISTINGS' ),
				'viewmore'		=> JText::_( 'COM_MTREE_VIEW_MORE_MOST_REVIEWED_LISTINGS' ),
				'name'			=> 'mostreview'
				),
			'listnew' => array(
				'title_lang_key'	=> 'PAGE_TITLE_NEW_LISTINGS',
				'header_lang_key'	=> 'NEW_LISTINGS',
				'viewmore_lang_key'	=> 'VIEW_MORE_NEW_LISTINGS',
				'title'			=> JText::_( 'COM_MTREE_PAGE_TITLE_NEW_LISTINGS' ),
				'header'		=> JText::_( 'COM_MTREE_NEW_LISTINGS' ),
				'viewmore'		=> JText::_( 'COM_MTREE_VIEW_MORE_NEW_LISTINGS' ),
				'name'			=> 'new'
				),
			'listupdated' => array(
				'title_lang_key'	=> 'PAGE_TITLE_RECENTLY_UPDATED_LISTINGS',
				'header_lang_key'	=> 'RECENTLY_UPDATED_LISTINGS',
				'viewmore_lang_key'	=> 'VIEW_MORE_RECENTLY_UPDATED_LISTINGS',
				'title'			=> JText::_( 'COM_MTREE_PAGE_TITLE_RECENTLY_UPDATED_LISTINGS' ),
				'header'		=> JText::_( 'COM_MTREE_RECENTLY_UPDATED_LISTINGS' ),
				'viewmore'		=> JText::_( 'COM_MTREE_VIEW_MORE_RECENTLY_UPDATED_LISTINGS' ),
				'name'			=> 'updated'
				),
			'listfavourite' => array(
				'title_lang_key'	=> 'PAGE_TITLE_MOST_FAVOURED_LISTINGS',
				'header_lang_key'	=> 'MOST_FAVOURED_LISTINGS',
				'viewmore_lang_key'	=> 'VIEW_MORE_MOST_FAVOURED_LISTINGS',
				'title'			=> JText::_( 'COM_MTREE_PAGE_TITLE_MOST_FAVOURED_LISTINGS' ),
				'header'		=> JText::_( 'COM_MTREE_MOST_FAVOURED_LISTINGS' ),
				'viewmore'		=> JText::_( 'COM_MTREE_VIEW_MORE_MOST_FAVOURED_LISTINGS' ),
				'name'			=> 'favourite'
				),
			'listfeatured' => array(
				'title_lang_key'	=> 'PAGE_TITLE_FEATURED_LISTINGS',
				'header_lang_key'	=> 'FEATURED_LISTINGS',
				'viewmore_lang_key'	=> 'VIEW_MORE_FEATURED_LISTINGS',
				'title'			=> JText::_( 'COM_MTREE_PAGE_TITLE_FEATURED_LISTINGS' ),
				'header'		=> JText::_( 'COM_MTREE_FEATURED_LISTINGS' ),
				'viewmore'		=> JText::_( 'COM_MTREE_VIEW_MORE_FEATURED_LISTINGS' ),
				'name'			=> 'featured'
				),
			'listalpha' => array(
				'title_lang_key'	=> 'PAGE_TITLE_ALPHA_LISTINGS',
				'header_lang_key'	=> 'ALPHA_LISTINGS',
				'viewmore_lang_key'	=> 'VIEW_MORE_ALPHA_LISTINGS',
				'title'			=> JText::_( 'COM_MTREE_PAGE_TITLE_ALPHA_LISTINGS' ),
				'header'		=> JText::_( 'COM_MTREE_ALPHA_LISTINGS' ),
				'viewmore'		=> JText::_( 'COM_MTREE_VIEW_MORE_ALPHA_LISTINGS' ),
				'name'			=> 'alpha'
				)
			);
	}

	function setSubcats( $cat_ids ) {
		$this->subcats = $cat_ids;
	}

	function setLinkId( $link_id ) {
		$this->link_id = $link_id;
	}

	function setLimitStart( $limitstart ) {
		if( !is_numeric($limitstart) || $limitstart < 0 ) {
			$this->limitstart = 0;
		} else {
			$this->limitstart = $limitstart;
		}
	}
	
	function setLimit( $limit )
	{
		$this->limit = intval($limit);
	}
	
	function getLimit()
	{
		global $mtconf;
		
		if( isset($this->limit) )
		{
			return $this->limit;
		}
		else
		{
			return $mtconf->get('fe_num_of_'.$this->getName());
		}
	}
	
	function setSort( $sort ) {
		$this->sort = $sort;
		if( substr($sort,0,1) == '-' ) {
			$order_by_part_2 = 'DESC';
		} else {
			$order_by_part_2 = 'ASC';
		}
		$order_by_part_1 = str_replace(array('-','+'),'',$sort);

		if( in_array($order_by_part_1,$this->allowed_sort) ) {
			$this->sql_order_by = $order_by_part_1 . ' ' . $order_by_part_2;
		}
	}
	
	function getSort() {
		if( !empty($this->sort) ) {
			return $this->sort;
		} else {
			return '';
		}
	}
	
	function getOrderBy() {
		if( !is_null($this->sql_order_by) ) {
			return $this->sql_order_by;
		} else {
			return '';
		}
	}
	
	function getSortHTML() {
		global $mtconf;
		
		$sort_options = $mtconf->get('all_listings_sort_by_options');
		if( !is_array($sort_options) ) {
			$sort_options = explode('|',$sort_options);
		}
		
		if( !in_array($this->sort,$sort_options) ) {
			$options[] = JHtml::_('select.option',  $this->sort, JTEXT::_('COM_MTREE_ALL_LISTINGS_SORT_OPTION_'.strtoupper($this->sort)) );
		}
		
		foreach( $sort_options AS $sort_option ) {
			$options[] = JHtml::_('select.option',  $sort_option, JTEXT::_('COM_MTREE_ALL_LISTINGS_SORT_OPTION_'.strtoupper($sort_option)) );
		}
		
		$javascript = 'onchange="this.form.submit();"';

		return JHtml::_(
			'select.genericlist',
			$options,
			'sort',
			array('list.attr' => 'id="sort" size="1" '. $javascript, 'list.select' => $this->sort)
		);
	}
	
	/*
	 * This return a precise limitstart value to SQL query to always return the last page's limitstart to prevent
	 * unexpected results.
	 */
	function getLimitStart() {
		global $mtconf;
		
		$limitstart = 0;
		$limit = $this->getLimit();

		if( ($this->limitstart % $limit) != 0 ) {
			$limitstart = floor($this->limitstart/$limit) * $limit;
		} else {
			$limitstart = $this->limitstart;
		}

		if(
			$this->getName() == 'featured' 
			&&
			($limitstart + $limit) > $this->getTotalFeatured()
		) {
			$limitstart = floor( $this->getTotalFeatured() / $limit ) * $limit;
		}
		
		 if( 
			$mtconf->get('fe_total_'.$this->getName()) > 0 && $limitstart >= $mtconf->get('fe_total_'.$this->getName())
		) {
			$limitstart = $mtconf->get('fe_total_'.$this->getName()) - $mtconf->get('fe_num_of_'.$this->getName());
		}
		$this->setLimitStart( $limitstart );
		return $limitstart;
	}

	function getImplodedSubcats() {
		if( count($this->subcats) == 1 && $this->subcats[0] == 0 ) {
			return 0;
		} else {
			return implode( ", ", $this->subcats );
		}	
	}

	function getTitle() {
		return $this->list[$this->task]['title'];
	}

	function getTitleLangKey() {
		return $this->list[$this->task]['title_lang_key'];
	}

	function getHeader() {
		return $this->list[$this->task]['header'];
	}

	function getHeaderLangKey() {
		return $this->list[$this->task]['header_lang_key'];
	}

	function getViewMoreText() {
		return $this->list[$this->task]['viewmore'];
	}
	
	function getName() {
		return $this->list[$this->task]['name'];
	}

	function getListNewLinkCount()
	{
		global $mtconf;

		if ( ($this->limitstart + $this->getLimit()) > $mtconf->get('fe_total_new') )
		{
			return $mtconf->get('fe_total_new') - $this->limitstart;
		}
		else
		{
			return $this->getLimit();
		}
	}

	function prepareQuery() {
		global $mtconf;
		
		$database	= JFactory::getDBO();

		$nullDate	= $database->getNullDate();

		switch( $this->task ) {
			case 'listassociated':
			$database->setQuery(
				"SELECT link_id2 AS link_id 
				  FROM #__mt_links_associations AS lmap
				  LEFT JOIN #__mt_links AS l ON l.link_id = lmap.link_id2 
				  LEFT JOIN #__mt_cl AS cl ON cl.link_id = lmap.link_id2 AND cl.main = 1 
				  LEFT JOIN #__mt_cats AS cat ON cat.cat_id = cl.cat_id 
				  WHERE link_id1 = " . $this->link_id . "
				  AND link_published='1' && link_approved='1' 
				  AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= ".$database->Quote($this->now)." ) 
				  AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= ".$database->Quote($this->now)." ) 
				  AND l.link_id = cl.link_id
				  AND cl.main = 1
				  LIMIT $this->limitstart, " . $this->getLimit()
				);
				/*
				// This query limits associated listing to one associated category only.
				$database->setQuery(
					"SELECT link_id2 AS link_id 
					  FROM #__mt_links_associations AS lmap
					  LEFT JOIN #__mt_links AS l ON l.link_id = lmap.link_id2 
					  LEFT JOIN #__mt_cl AS cl ON cl.link_id = lmap.link_id2 AND cl.main = 1 
					  LEFT JOIN #__mt_cats AS cat ON cat.cat_id = cl.cat_id 
					  WHERE link_id1 = " . $this->link_id . "
					  AND link_published='1' && link_approved='1' 
					  AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= ".$database->Quote($this->now)." ) 
					  AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= ".$database->Quote($this->now)." ) 
					  AND l.link_id = cl.link_id
					  AND cl.main = 1
					  AND cl.cat_id = cat.cat_id
					  " . ( ($this->getImplodedSubcats()) ? "\n AND cl.cat_id IN (" . $this->getImplodedSubcats() . ") " : '') . "
					  LIMIT $this->limitstart, " . $mtconf->get('fe_num_of_associated')
					);
				*/
				$this->link_ids_results = $database->loadObjectList('link_id');
				break;
			case 'listfavourite':
				$database->setQuery(
					"SELECT f.link_id, COUNT(f.fav_id) AS favourites
					  FROM (#__mt_favourites AS f, #__mt_cl AS cl, #__mt_cats AS cat)
					  LEFT JOIN #__mt_links AS l ON l.link_id = f.link_id 
					  WHERE link_published='1' && link_approved='1' 
					  AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= ".$database->Quote($this->now)." ) 
					  AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= ".$database->Quote($this->now)." ) 
					  AND l.link_id = cl.link_id
 					  AND cl.main = 1
					  AND cl.cat_id = cat.cat_id
					  " . ( ($this->getImplodedSubcats()) ? "\n AND cl.cat_id IN (" . $this->getImplodedSubcats() . ") " : '') . "
					  GROUP BY f.link_id 
					  ORDER BY favourites DESC 
					  LIMIT $this->limitstart, " . $this->getLimit()
					);
				$this->link_ids_results = $database->loadObjectList('link_id');
				break;
			case 'listmostreview':
				$database->setQuery(
					"SELECT r.link_id, COUNT(r.link_id) AS reviews
					  FROM (#__mt_reviews AS r, #__mt_cl AS cl, #__mt_cats AS cat)
					  LEFT JOIN #__mt_links AS l ON l.link_id = r.link_id 
					  WHERE link_published='1' && link_approved='1' 
					  AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= ".$database->Quote($this->now)." ) 
					  AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= ".$database->Quote($this->now)." ) 
					  AND l.link_id = cl.link_id
 					  AND cl.main = 1
					  AND cl.cat_id = cat.cat_id
					  " . ( ($this->getImplodedSubcats()) ? "\n AND cl.cat_id IN (" . $this->getImplodedSubcats() . ") " : '') . "
					  AND  r.rev_approved = '1'
					  GROUP BY r.link_id 
					  ORDER BY reviews DESC 
					  LIMIT $this->limitstart, " . $this->getLimit()
					);
				$this->link_ids_results = $database->loadObjectList('link_id');
				break;
		}
		if( !empty($this->link_ids_results) ) {
			foreach( $this->link_ids_results AS $result ) {
				$this->link_ids[] = $result->link_id;
			}
		}
	}
	
	function getSQL() {
		global $mtconf;

		$database	= JFactory::getDBO();
		$nullDate	= $database->getNullDate();

		$sql = '';
		switch( $this->task ) {
			case 'listassociated':
				if( !empty($this->link_ids) )
				{
					$sql = "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, u.username, cat.cat_id, cat.cat_name, cat.cat_association, img.filename AS link_image FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat) "
						. "\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 "
						. "\n LEFT JOIN #__users AS u ON u.id = l.user_id "
						. "\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
						. "\nWHERE l.link_id IN (" . implode(",", $this->link_ids) . ") "
						. "\n AND cl.main = '1' "
						. "\n AND cl.link_id = l.link_id "
						. "\n AND cl.cat_id = cat.cat_id "
						. "LIMIT " . $this->getLimit();
				}
				break;
			case 'listall':
				$sql = "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, u.username, cat.cat_id, cat.cat_name, cat.cat_association, img.filename AS link_image FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat) "
						. "\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 "
						. "\n LEFT JOIN #__users AS u ON u.id = l.user_id "
						. "\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
						. "WHERE link_published='1' && link_approved='1' "
						. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$this->now'  ) "
						. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$this->now' ) "
						. "\n AND l.link_id = cl.link_id "
						. "\n AND cl.main = 1 "
						. "\n AND cl.cat_id = cat.cat_id "
						. ( ($this->getImplodedSubcats()) ? "\n AND cl.cat_id IN (" . $this->getImplodedSubcats() . ") " : '');
				if( $this->getOrderBy() != '' ) {
					$sql .= "\n ORDER BY " . $this->getOrderBy();
				}
				$sql .= "\n LIMIT " . $this->getLimitStart() . ", " . $this->getLimit();
				break;
			case 'listpopular':
				$sql = "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, u.username, cat.cat_id, cat.cat_name, cat.cat_association, img.filename AS link_image FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat) "
						. "\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 "
						. "\n LEFT JOIN #__users AS u ON u.id = l.user_id "
						. "\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
						. "WHERE link_published='1' && link_approved='1' "
						. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$this->now'  ) "
						. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$this->now' ) "
						. "\n AND l.link_id = cl.link_id "
						. "\n AND cl.main = 1 "
						. "\n AND cl.cat_id = cat.cat_id "
						. ( ($this->getImplodedSubcats()) ? "\n AND cl.cat_id IN (" . $this->getImplodedSubcats() . ") " : '')
						. "ORDER BY link_hits DESC "
						. "LIMIT " . $this->getLimitStart() . ", " . $this->getLimit();
				break;
			case 'listmostrated':
				$sql = "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, u.username, cat.cat_id, cat.cat_name, cat.cat_association, img.filename AS link_image FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat) "
						. "\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 "
						. "\n LEFT JOIN #__users AS u ON u.id = l.user_id "
						. "\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
						. "WHERE link_published='1' && link_approved='1' "
						. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$this->now'  ) "
						. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$this->now' ) "
						. "\n AND l.link_id = cl.link_id "
						. "\n AND cl.main = 1 "
						. "\n AND cl.cat_id = cat.cat_id "
						. ( ($this->getImplodedSubcats()) ? "\n AND cl.cat_id IN (" . $this->getImplodedSubcats() . ") " : '')
						. "ORDER BY link_votes DESC, link_rating DESC " 
						. "LIMIT " . $this->getLimitStart() . ", " . $this->getLimit();
				break;
			case 'listtoprated':
				$sql = "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, u.username, cat.cat_id, cat.cat_name, cat.cat_association, img.filename AS link_image FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat) "
						. "\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 "
						. "\n LEFT JOIN #__users AS u ON u.id = l.user_id "
						. "\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
						. "WHERE link_published='1' && link_approved='1' "
						. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$this->now'  ) "
						. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$this->now' ) "
						. "\n AND l.link_id = cl.link_id "
						. "\n AND cl.main = 1 "
						. "\n AND cl.cat_id = cat.cat_id "
						. ( ( $mtconf->get('min_votes_for_toprated') >= 1 ) ? "\n AND l.link_votes >= " . $mtconf->get('min_votes_for_toprated') . " " : '' )
						. ( ($this->getImplodedSubcats()) ? "\n AND cl.cat_id IN (" . $this->getImplodedSubcats() . ") " : '')
						. "ORDER BY link_rating DESC, link_votes DESC  " 
						. "LIMIT " . $this->getLimitStart() . ", " . $this->getLimit();
				break;
			case 'listmostreview':
				if( !empty($this->link_ids) )
				{
					$sql = "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, u.username, cat.cat_id, cat.cat_name, cat.cat_association, COUNT(r.rev_id) AS reviews, img.filename AS link_image FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat) "
						. "\nLEFT JOIN #__mt_reviews AS r ON r.link_id = l.link_id"
						. "\nLEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1"
						. "\nLEFT JOIN #__users AS u ON u.id = l.user_id "
						. "\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
						. "\nWHERE l.link_id IN (" . implode(",", $this->link_ids) . ")"
						. "\nAND cl.main = '1'"
						. "\nAND cl.link_id = l.link_id"				
						. "\nGROUP BY l.link_id"
						. "\nLIMIT " . $this->getLimit();
				}
				break;
			case 'listnew':
				$sql = "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, u.username, cat.cat_id, cat.cat_name, cat.cat_association, img.filename AS link_image FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat) "
						. "\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 "
						. "\n LEFT JOIN #__users AS u ON u.id = l.user_id "
						. "\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
						. "WHERE link_published='1' && link_approved='1' "
						. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$this->now'  ) "
						. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$this->now' ) "
						. "\n AND l.link_id = cl.link_id "
						. "\n AND cl.main = 1 "
						. "\n AND cl.cat_id = cat.cat_id "
						. ( ($this->getImplodedSubcats()) ? "\n AND cl.cat_id IN (" . $this->getImplodedSubcats() . ") " : '')
						. "ORDER BY link_created DESC ";
				$sql .= "LIMIT " . $this->getLimitStart() . ", " . $this->getListNewLinkCount();
				break;
			case 'listupdated':
				$sql = "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, u.username, cat.cat_id, cat.cat_name, cat.cat_association, img.filename AS link_image FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat) "
						. "\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 "
						. "\n LEFT JOIN #__users AS u ON u.id = l.user_id "
						. "\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
						. "WHERE link_published='1' && link_approved='1' "
						. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$this->now'  ) "
						. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$this->now' ) "
						. "\n AND l.link_id = cl.link_id "
						. "\n AND cl.main = 1 "
						. "\n AND cl.cat_id = cat.cat_id "
						. ( ($this->getImplodedSubcats()) ? "\n AND cl.cat_id IN (" . $this->getImplodedSubcats() . ") " : '')
						. "ORDER BY link_modified DESC ";
				$sql .= "LIMIT " . $this->getLimitStart() . ", " . $this->getLimit();
				break;
			case 'listfavourite':
				if( !empty($this->link_ids) )
				{
					$sql = "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, u.username, cat.cat_id, cat.cat_name, cat.cat_association, COUNT(r.rev_id) AS reviews, img.filename AS link_image FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat) "
						.	"\nLEFT JOIN #__mt_reviews AS r ON r.link_id = l.link_id"
						.	"\nLEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1"
						.	"\nLEFT JOIN #__users AS u ON u.id = l.user_id "
						. "\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
						.	"\nWHERE l.link_id IN (" . implode(",", $this->link_ids) . ")"
						.	"\nAND cl.main = '1'"
						.	"\nAND cl.link_id = l.link_id"				
						.	"\nGROUP BY l.link_id"
						.	"\nLIMIT " . $this->getLimit();
				}
				break;
			case 'listfeatured':
				$sql = "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, u.username, cat.cat_id, cat.cat_name, cat.cat_association, img.filename AS link_image FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat) "
						. "\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 "
						. "\n LEFT JOIN #__users AS u ON u.id = l.user_id "
						. "\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
						. "WHERE link_published='1' && link_approved='1' && link_featured='1' "
						. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$this->now'  ) "
						. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$this->now' ) "
						. "\n AND l.link_id = cl.link_id "
						. "\n AND cl.main = 1 "
						. "\n AND cl.cat_id = cat.cat_id "
						. ( ($this->getImplodedSubcats()) ? "\n AND cl.cat_id IN (" . $this->getImplodedSubcats() . ") " : '')
						. "ORDER BY link_name ASC " 
						. "LIMIT " . $this->getLimitStart() . ", " . $this->getLimit();
				break;
			case 'listalpha':
			default:
				$sql = "SELECT l.*, tlcat.cat_id AS tlcat_id, tlcat.cat_name AS tlcat_name, u.username, cat.cat_id, cat.cat_name, cat.cat_association, img.filename AS link_image FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat) "
						. "\n LEFT JOIN #__mt_images AS img ON img.link_id = l.link_id AND img.ordering = 1 "
						. "\n LEFT JOIN #__users AS u ON u.id = l.user_id "
						. "\n LEFT JOIN #__mt_cats AS tlcat ON tlcat.lft <= cat.lft AND tlcat.rgt >= cat.rgt AND tlcat.cat_parent =0 "
						. "WHERE link_published='1' && link_approved='1' "
						. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$this->now'  ) "
						. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$this->now' ) "
						. "\n AND l.link_id = cl.link_id "
						. "\n AND cl.main = 1 "
						. "\n AND cl.cat_id = cat.cat_id "
						. ( ($this->getImplodedSubcats()) ? "\n AND cl.cat_id IN (" . $this->getImplodedSubcats() . ") " : '')
						. "ORDER BY link_name ASC " 
						. "LIMIT " . $this->getLimitStart() . ", " . $this->getLimit();
				break;
		}
		return $sql;
	}
	
	function getListings() {
		$database = JFactory::getDBO();
		
		$this->prepareQuery();

		$sql = $this->getSQL();
		if( !empty($sql) )
		{
			$database->setQuery( $sql );
			$links = $database->loadObjectList();
			$this->sortLinks($links);
		} else {
			$links = array();
		}
		
		
		return $links;
	}

	function getPageNav() {
		global $mtconf;

		$database	= JFactory::getDBO();
		$nullDate	= $database->getNullDate();

		$config_total_listing = $mtconf->get('fe_total_' . substr($this->task,4));

		switch( $this->task ) {

			default:
				# Get the total available listings
				$sql = "SELECT COUNT(*) FROM (#__mt_links AS l, #__mt_cl AS cl) "
					. "WHERE link_published='1' && link_approved='1' "
					. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$this->now'  ) "
					. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$this->now' ) "
					. "\n AND l.link_id = cl.link_id "
					. "\n AND cl.main = 1 ";
				switch( $this->task )
				{
					case 'listmostrated':
						$sql .= "\n AND l.link_votes > 0 ";
						break;
					case 'listtoprated':
						$sql .= "\n AND l.link_rating > 0 ";
						$sql .= ( ( $mtconf->get('min_votes_for_toprated') >= 1 ) ? "\n AND l.link_votes >= " . $mtconf->get('min_votes_for_toprated') . " " : '' );
						break;
				}
				$sql .= ( ($this->getImplodedSubcats()) ? "\n AND cl.cat_id IN (" . $this->getImplodedSubcats() . ") " : '');
				$database->setQuery( $sql );
				$total = $database->loadResult();

				if( in_array($this->task,array('listall','listalpha')) ) {
					$config_total_listing = $total;
				}
				$config_listing_perpage = $this->getLimit();

				if( $total > $config_total_listing ) {
					$total = $config_total_listing;
				}

				break;
			
			case 'listassociated':
				$total = $this->getTotalAssociated();
				break;

			case 'listmostreview':
				$total = $this->getTotalReviewed();
				if( $total > $config_total_listing ) {
					$total = $config_total_listing;
				}
				break;
			
			case 'listfavourite':
				$total = $this->getTotalFavourited();
				if( $total > $config_total_listing ) {
					$total = $config_total_listing;
				}
				break;
				
			case 'listfeatured':
				$total = $this->getTotalFeatured();
				break;
		}
		
		# Page Navigation
		jimport('joomla.html.pagination');
		$pageNav = new JPagination($total, $this->getLimitStart(), $this->getLimit());
		return $pageNav;
	}
	
	function getTotalAssociated()
	{
		$database	= JFactory::getDBO();
		$nullDate	= $database->getNullDate();
		$database->setQuery(
			"SELECT COUNT(DISTINCT l.link_id) FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat, #__mt_links_associations AS lmap) "
					. "WHERE link_published='1' && link_approved='1' "
					. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$this->now'  ) "
					. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$this->now' ) "
					. "\n AND l.link_id = cl.link_id "
					. "\n AND l.link_id = lmap.link_id2 "
					. "\n AND cl.main = 1 "
					. "\n AND cl.cat_id = cat.cat_id "
					. "\n AND lmap.link_id1 = " . $this->link_id
			);
		/*
		$database->setQuery(
			"SELECT COUNT(DISTINCT l.link_id) FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat, #__mt_links_associations AS lmap) "
					. "WHERE link_published='1' && link_approved='1' "
					. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$this->now'  ) "
					. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$this->now' ) "
					. "\n AND l.link_id = cl.link_id "
					. "\n AND l.link_id = lmap.link_id2 "
					. "\n AND cl.main = 1 "
					. "\n AND cl.cat_id = cat.cat_id "
					. "\n AND lmap.link_id1 = " . $this->link_id
					. ( ($this->getImplodedSubcats()) ? "\n AND cl.cat_id IN (" . $this->getImplodedSubcats() . ") " : '')
			);
		*/
			return $database->loadResult();
	}
		
	function getTotalFavourited()
	{
		$database = JFactory::getDBO();
		$nullDate	= $database->getNullDate();
		
		$database->setQuery(
			"SELECT COUNT(DISTINCT l.link_id) FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat, #__mt_favourites AS f) "
					. "WHERE link_published='1' && link_approved='1' "
					. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$this->now'  ) "
					. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$this->now' ) "
					. "\n AND l.link_id = cl.link_id "
					. "\n AND l.link_id = f.link_id "
					. "\n AND cl.main = 1 "
					. "\n AND cl.cat_id = cat.cat_id "
					. ( ($this->getImplodedSubcats()) ? "\n AND cl.cat_id IN (" . $this->getImplodedSubcats() . ") " : '')
			);
			return $database->loadResult();
	}
	
	function getTotalReviewed()
	{
		$database = JFactory::getDBO();
		$nullDate	= $database->getNullDate();

		$database->setQuery(
			"SELECT COUNT(DISTINCT l.link_id) FROM (#__mt_links AS l, #__mt_cl AS cl, #__mt_cats AS cat) "
					. "LEFT JOIN #__mt_reviews AS r ON r.link_id = l.link_id "
					. "WHERE l.link_published='1' && l.link_approved='1' && r.rev_approved='1' "
					. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$this->now'  ) "
					. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$this->now' ) "
					. "\n AND l.link_id = cl.link_id "
					. "\n AND cl.main = 1 "
					. "\n AND cl.cat_id = cat.cat_id "
					. ( ($this->getImplodedSubcats()) ? "\n AND cl.cat_id IN (" . $this->getImplodedSubcats() . ") " : '')
			);
			return $database->loadResult();
	}
	
	function getTotalFeatured()
	{
		$database = JFactory::getDBO();
		$nullDate	= $database->getNullDate();

		$database->setQuery( "SELECT COUNT(*) FROM (#__mt_links AS l, #__mt_cl AS cl) "
			. "WHERE link_published='1' && link_approved='1' && link_featured='1' "
			. "\n AND ( publish_up = ".$database->Quote($nullDate)." OR publish_up <= '$this->now'  ) "
			. "\n AND ( publish_down = ".$database->Quote($nullDate)." OR publish_down >= '$this->now' ) " 
			. "\n AND l.link_id = cl.link_id "
			. "\n AND cl.main = 1 "
			.	( ($this->getImplodedSubcats()) ? "\n AND cl.cat_id IN (" . $this->getImplodedSubcats() . ") " : '')
			);
		return $database->loadResult();
	}
	
	function sortLinks( &$links ) {
		switch( $this->task ) {
			case 'listfavourite':
				for( $i=0; $i<count($links); $i++ ) {
					$links[$i]->favourites = $this->link_ids_results[$links[$i]->link_id]->favourites;
				}
				usort($links,array($this,'sortfavourites'));
				break;
			case 'listmostreview':
				for( $i=0; $i<count($links); $i++ ) {
					$links[$i]->reviews = $this->link_ids_results[$links[$i]->link_id]->reviews;
				}
				usort($links,array($this,'sortreviews'));
				break;			
			
		}
		
	}
	
	function sortfavourites($val1,$val2) {
		if( $val1->favourites < $val2->favourites ) {
			return 1;
		}
		if( $val1->favourites > $val2->favourites ) {
			return -1;
		}
		if( $val1->favourites == $val2->favourites ) {
			return 0;
		}
	}

	function sortreviews($val1,$val2) {
		if( $val1->reviews < $val2->reviews ) {
			return 1;
		}
		if( $val1->reviews > $val2->reviews ) {
			return -1;
		}
		if( $val1->reviews == $val2->reviews ) {
			return 0;
		}
	}
}
?>