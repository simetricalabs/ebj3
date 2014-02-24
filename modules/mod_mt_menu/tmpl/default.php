<?php /* $Id$ */ defined('_JEXEC') or die('Restricted access'); ?>
<ul class="nav menu<?php echo $class_sfx; ?>">
	<?php
	
	if ($show['home']): ?>
	<li><a href="<?php echo JRoute::_(""); ?>"><?php echo JText::_( 'MOD_MT_MENU_HOME' ); ?></a></li>
	<?php endif;
	
	if ($show['directory']): ?>
	<li<?php echo ($active == 'directory') ? ' class="parent active"' : ''; ?>><a href="<?php echo JRoute::_("index.php?option=com_mtree".$itemid); ?>"><?php echo JText::_( 'MOD_MT_MENU_DIRECTORY' ); ?></a></li>
	<?php endif;

	if ($show['allcats']): ?>
	<li<?php echo ($active == 'listallcats') ? ' class="parent active"' : ''; ?>><a href="<?php echo JRoute::_("index.php?option=com_mtree&task=listallcats&cat_id=".$cat_id.$itemid); ?>"><?php echo JText::_( 'MOD_MT_MENU_ALL_CATEGORIES' ); ?></a></li>
	<?php endif;

	if ($show['all']): ?>
	<li<?php echo ($active == 'listall') ? ' class="parent active"' : ''; ?>><a href="<?php echo JRoute::_("index.php?option=com_mtree&task=listall&cat_id=".$toplist_cat_id.$itemid); ?>"><?php echo JText::_( 'MOD_MT_MENU_ALL_LISTINGS' ); ?></a></li>
	<?php endif;

	if ($show['addlisting'] && $cat_allow_submission): ?>
	<li<?php echo ($active == 'addlisting') ? ' class="parent active"' : ''; ?>><a href="<?php echo JRoute::_("index.php?option=com_mtree&task=addlisting&cat_id=".$cat_id.$itemid); ?>"><?php echo JText::_( 'MOD_MT_MENU_ADD_LISTING' ); ?></a></li>
	<?php endif;
	
	if ($show['addcategory']): ?>
	<li<?php echo ($active == 'addcategory') ? ' class="parent active"' : ''; ?>><a href="<?php echo JRoute::_("index.php?option=com_mtree&task=addcategory&cat_id=".$cat_id.$itemid); ?>"><?php echo JText::_( 'MOD_MT_MENU_ADD_CATEGORY' ); ?></a></li>
	<?php endif;

	if ($show['mypage'] && $my->id > 0): ?>
	<li<?php echo ($active == 'mypage') ? ' class="parent active"' : ''; ?>><a href="<?php echo JRoute::_("index.php?option=com_mtree&task=mypage".$itemid); ?>"><?php echo JText::_( 'MOD_MT_MENU_MY_PAGE' ); ?></a></li>
	<?php endif;

	if ($show['myfavourites'] && $mtconf->get('show_favourite') && $my->id > 0): ?>
	<li<?php echo ($active == 'viewusersfav' && $user_id == $my->id) ? ' class="parent active"' : ''; ?>><a href="<?php echo JRoute::_("index.php?option=com_mtree&task=viewusersfav&user_id=".$my->id.$itemid); ?>"><?php echo JText::_( 'MOD_MT_MENU_MY_FAVOURITES' ); ?></a></li>
	<?php endif;

	if ($show['myreviews'] && $mtconf->get('show_review') && $my->id > 0): ?>
	<li<?php echo ($active == 'viewusersreview' && $user_id == $my->id) ? ' class="parent active"' : ''; ?>><a href="<?php echo JRoute::_("index.php?option=com_mtree&task=viewusersreview&user_id=".$my->id.$itemid); ?>"><?php echo JText::_( 'MOD_MT_MENU_MY_REVIEWS' ); ?></a></li>
	<?php endif;

	if ($show['newlisting']): ?>
	<li<?php echo ($active == 'listnew') ? ' class="parent active"' : ''; ?>><a href="<?php echo JRoute::_("index.php?option=com_mtree&task=listnew&cat_id=".$toplist_cat_id.$itemid); ?>"><?php echo JText::_( 'MOD_MT_MENU_NEW_LISTING' ); ?></a></li>
	<?php endif;

	if ($show['recentlyupdatedlisting']): ?>
	<li<?php echo ($active == 'listupdated') ? ' class="parent active"' : ''; ?>><a href="<?php echo JRoute::_("index.php?option=com_mtree&task=listupdated&cat_id=".$toplist_cat_id.$itemid); ?>"><?php echo JText::_( 'MOD_MT_MENU_RECENTLY_UPDATED_LISTING' ); ?></a></li>
	<?php endif;

	if ($show['mostfavoured']): ?>
	<li<?php echo ($active == 'listfavourite') ? ' class="parent active"' : ''; ?>><a href="<?php echo JRoute::_("index.php?option=com_mtree&task=listfavourite&cat_id=".$toplist_cat_id.$itemid); ?>"><?php echo JText::_( 'MOD_MT_MENU_MOST_FAVOURED_LISTINGS' ); ?></a></li>
	<?php endif;

	if ($show['featuredlisting']): ?>
  	<li<?php echo ($active == 'listfeatured') ? ' class="parent active"' : ''; ?>><a href="<?php echo JRoute::_("index.php?option=com_mtree&task=listfeatured&cat_id=".$toplist_cat_id.$itemid); ?>"><?php echo JText::_( 'MOD_MT_MENU_FEATURED_LISTING' ); ?></a></li>
	<?php endif;

	if ($show['popularlisting']): ?>
	<li<?php echo ($active == 'listpopular') ? ' class="parent active"' : ''; ?>><a href="<?php echo JRoute::_("index.php?option=com_mtree&task=listpopular&cat_id=".$toplist_cat_id.$itemid); ?>"><?php echo JText::_( 'MOD_MT_MENU_POPULAR_LISTING' ); ?></a></li>
	<?php endif;

	if ($show['mostratedlisting']): ?>
	<li<?php echo ($active == 'listmostrated') ? ' class="parent active"' : ''; ?>><a href="<?php echo JRoute::_("index.php?option=com_mtree&task=listmostrated&cat_id=".$toplist_cat_id.$itemid); ?>"><?php echo JText::_( 'MOD_MT_MENU_MOST_RATED_LISTING' ); ?></a></li>
	<?php endif;

	if ($show['topratedlisting']): ?>
	<li<?php echo ($active == 'listtoprated') ? ' class="parent active"' : ''; ?>><a href="<?php echo JRoute::_("index.php?option=com_mtree&task=listtoprated&cat_id=".$toplist_cat_id.$itemid); ?>"><?php echo JText::_( 'MOD_MT_MENU_TOP_RATED_LISTING' ); ?></a></li>
	<?php endif;

	if ($show['mostreviewedlisting']): ?>
	<li<?php echo ($active == 'listmostreview') ? ' class="parent active"' : ''; ?>><a href="<?php echo JRoute::_("index.php?option=com_mtree&task=listmostreview&cat_id=".$toplist_cat_id.$itemid); ?>"><?php echo JText::_( 'MOD_MT_MENU_MOST_REVIEWED_LISTING' ); ?></a></li>
	<?php endif; ?>	
</ul>