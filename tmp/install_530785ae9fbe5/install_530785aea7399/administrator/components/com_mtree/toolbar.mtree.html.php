<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2005-2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */


defined('_JEXEC') or die('Restricted access');

class TOOLBAR_mtree {

	/***
	 * Link
	 */
	public static function EDITLINK_MENU() {
		
		$task	= JFactory::getApplication()->input->getCmd( 'task', '');
		
		JToolBarHelper::title(  ($task=='newlink') ? JText::_( 'COM_MTREE_ADD_LISTING' ) : JText::_( 'COM_MTREE_EDIT_LISTING' ), 'article.png' );

		if($task == 'editlink_for_approval') {
			JToolbarHelper::save('savelink', 'COM_MTREE_SAVE_CHANGES');
		} else {
			JToolbarHelper::apply('applylink');
			JToolbarHelper::save('savelink');
		}
		JToolBarHelper::cancel( 'cancellink' );
	}

	public static function MOVELINKS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_MOVE_LINK' ), 'article.png' );
		JToolBarHelper::save( 'links_move2' );
		JToolbarHelper::cancel('cancellinks_move');
	}

	public static function COPYLINKS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_COPY_LINK' ), 'article.png' );
		JToolBarHelper::save( 'links_copy2' );
		JToolbarHelper::cancel('cancellinks_copy');
	}

	/***
	 * Category
	 */
	public static function EDITCAT_MENU() {
		$task = JFactory::getApplication()->input->getCmd( 'task', '');

		JToolBarHelper::title( ( ($task=='newcat') ? JText::_( 'COM_MTREE_ADD_CATEGORY' ) : JText::_( 'COM_MTREE_EDIT_CATEGORY' )), 'categories.png' );
		JToolBarHelper::apply( 'applycat' );
		JToolBarHelper::save( 'savecat' );
		JToolBarHelper::cancel( 'cancelcat' );
	}

	public static function MOVECATS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_MOVE_CATEGORY' ), 'move_f2.png' );
		JToolBarHelper::save( 'cats_move2' );
		JToolbarHelper::cancel('cancelcats_move');
	}

	public static function COPYCATS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_COPY_CATEGORY' ), 'copy_f2.png' );
		JToolBarHelper::save( 'cats_copy2' );
		JToolbarHelper::cancel('cancelcats_copy');
	}

	public static function REMOVECATS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_DELETE' ), 'trash.png' );
		JToolBarHelper::custom( 'removecats2', 'delete', '', 'JTOOLBAR_DELETE', false );
		JToolbarHelper::cancel('cancelcat');
	}

	public static function LISTCATS_MENU() {
		JToolBarHelper::title( JText::_('COM_MTREE_MOSETS_TREE'), 'mosetstree' );
		JToolBarHelper::deleteList('','removecats', 'COM_MTREE_DELETE_CATEGORIES');
		JToolBarHelper::custom( 'cats_copy', 'copy.png', 'copy_f2.png', 'COM_MTREE_COPY_CATEGORIES' );
		JToolBarHelper::custom( 'cats_move', 'move.png', 'move_f2.png', 'COM_MTREE_MOVE_CATEGORIES' );
		JToolBarHelper::divider();
		
		JToolBar::getInstance()->addButtonPath(__DIR__ . '/toolbar');
		$bar = JToolBar::getInstance('toolbar');
		
		$bar->appendButton('ListingStandard', '', 'delete', 'COM_MTREE_DELETE_LISTINGS', 'removelinks' );
		$bar->appendButton('ListingStandard', '', 'copy', 'COM_MTREE_COPY_LISTINGS', 'links_copy' );
		$bar->appendButton('ListingStandard', '', 'move', 'COM_MTREE_MOVE_LISTINGS', 'links_move' );
	}

	/***
	 * Approval
	 */
	public static function LISTPENDING_LINKS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_PENDING_LISTING' ), 'article.png' );

		JToolBar::getInstance()->addButtonPath(__DIR__ . '/toolbar');
		$bar = JToolBar::getInstance('toolbar');
		
		$bar->appendButton('ListingStandard', '', 'publish', 'COM_MTREE_APPROVE_AND_PUBLISH_LISTING', 'approve_publish_links' );
		$bar->appendButton('ListingStandard', '', 'delete', 'COM_MTREE_DELETE_LISTINGS', 'removelinks' );

	}

	public static function LISTPENDING_CATS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_PENDING_CATEGORIES' ), 'categories.png' );
		JToolBarHelper::custom( 'approve_publish_cats', 'publish.png', 'publish_f2.png',JText::_( 'COM_MTREE_APPROVE_AND_PUBLISH' ), true );
		JToolBarHelper::custom( 'approve_cats', 'publish.png', 'publish_f2.png', JText::_( 'COM_MTREE_APPROVE_CATEGORIES' ), true );
		JToolBarHelper::divider();
		JToolBarHelper::deleteList('', 'removecats');
	}

	public static function LISTPENDING_REVIEWS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_PENDING_REVIEWS' ), 'article.png' );
		JToolBarHelper::apply( 'save_pending_reviews' );
	}

	public static function LISTPENDING_REPORTS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_PENDING_REPORTS' ), 'article.png' );
		JToolBarHelper::apply( 'save_reports' );
	}

	public static function LISTPENDING_REVIEWSREPORTS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_PENDING_REVIEWS_REPORTS' ), 'article.png' );
		JToolBarHelper::apply( 'save_reviewsreports' );
	}

	public static function LISTPENDING_REVIEWSREPLY_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_PENDING_REVIEWS_REPLIES' ), 'article.png' );
		JToolBarHelper::apply( 'save_reviewsreply' );
	}

	public static function LISTPENDING_CLAIMS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_PENDING_CLAIMS' ), 'article.png' );
		JToolBarHelper::apply( 'save_claims' );
	}

	/***
	 * Reviews
	 */
	public static function LISTREVIEWS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_REVIEWS' ), 'article.png' );

		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', 'arrow-left', 'JTOOLBAR_BACK', 'javascript:history.back();' );

		JToolBarHelper::custom( 'newreview', 'new.png', 'new_f2.png', 'JTOOLBAR_NEW', false );
		JToolBarHelper::editList( 'editreview' );
		JToolBarHelper::deleteList( '', 'removereviews' );
		// JToolBarHelper::divider();
		
	}

	public static function EDITREVIEW_MENU() {
		$task = JFactory::getApplication()->input->getCmd( 'task', '');
		
		JToolBarHelper::title(  (($task=='newreview') ? JText::_( 'COM_MTREE_ADD' ) : JText::_( 'COM_MTREE_EDIT' )) . ' ' . JText::_( 'COM_MTREE_REVIEW' ), 'article.png' );
		JToolBarHelper::apply( 'applyreview' );
		JToolBarHelper::save( 'savereview' );
		JToolBarHelper::cancel( 'cancelreview' );
	}

	/***
	*	Search Results
	*/
	public static function SEARCH_LISTINGS() {
		JHtml::_('behavior.framework');
		JToolBarHelper::title( JText::_( 'COM_MTREE_SEARCH_RESULTS' ) . ' - ' . JText::_( 'COM_MTREE_LISTINGS' ) , 'article.png' );

		JToolBar::getInstance()->addButtonPath(__DIR__ . '/toolbar');
		$bar = JToolBar::getInstance('toolbar');
		
		$bar->appendButton('ListingStandard', '', 'delete', 'COM_MTREE_DELETE_LISTINGS', 'removelinks' );
		$bar->appendButton('ListingStandard', '', 'copy', 'COM_MTREE_COPY_LISTINGS', 'links_copy' );
		$bar->appendButton('ListingStandard', '', 'move', 'COM_MTREE_MOVE_LISTINGS', 'links_move' );

	}

	public static function SEARCH_CATEGORIES() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_SEARCH_RESULTS' ) . ' - ' . JText::_( 'COM_MTREE_CATEGORIES' ) , 'article.png' );
		JToolBarHelper::custom( 'editcat', 'edit.png', 'edit_f2.png', 'COM_MTREE_EDIT_CATEGORY', true );
		JToolBarHelper::custom( 'removecats', 'delete.png', 'delete_f2.png', 'COM_MTREE_DELETE_CATEGORIES', true );
		JToolBarHelper::custom( 'cats_move', 'move.png', 'move_f2.png', 'COM_MTREE_MOVE_CATEGORIES', true );
	}

	/***
	* Tree Templates
	*/
	public static function TREE_TEMPLATES() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_TREE_TEMPLATES' ), 'thememanager' );
		JToolBarHelper::addNew('new_template');
		JToolBarHelper::makeDefault('default_template');
		JToolBarHelper::editList( 'template_pages' );
		JToolBarHelper::custom( 'copy_template', 'copy.png', 'copy_f2.png', 'COM_MTREE_COPY_TEMPLATE', true );
		JToolBarHelper::deleteList( '','delete_template' );
	}
	
	public static function TREE_TEMPLATEPAGES() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_TREE_TEMPLATES' ), 'thememanager' );
		JToolBarHelper::save( 'save_templateparams' );
		JToolBarHelper::apply( 'apply_templateparams' );
		JToolBarHelper::cancel( 'cancel_templatepages' );
	}

	public static function TREE_EDITTEMPLATEPAGE() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_TEMPLATE_PAGE_EDITOR' ), 'thememanager' );
		JToolBarHelper::save( 'save_templatepage' );
		JToolBarHelper::apply( 'apply_templatepage' );
		JToolBarHelper::cancel( 'cancel_edittemplatepage' );
	}
	
	public static function TREE_NEWTEMPLATE() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_UPLOAD_NEW_TEMPLATE' ), 'thememanager' );
		JToolBarHelper::cancel( 'cancel_templatepages' );
	}
	
	public static function TREE_COPYTEMPLATE() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_COPY_TEMPLATE' ), 'thememanager' );
		JToolBarHelper::save( 'copy_template2' );
		JToolBarHelper::cancel( 'cancel_templatepages' );
	}
	
	
	/***
	* Advanced Search
	*/
	public static function ADVSEARCH() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_ADVANCED_SEARCH' ) );
	}
	
	public static function ADVSEARCH2() {
		JHtml::_('behavior.framework');
		JToolBarHelper::title( JText::_( 'COM_MTREE_ADVANCED_SEARCH_RESULTS' ) );

		JToolBar::getInstance()->addButtonPath(__DIR__ . '/toolbar');
		$bar = JToolBar::getInstance('toolbar');
		
		$bar->appendButton('ListingStandard', '', 'delete', 'COM_MTREE_DELETE_LISTINGS', 'removelinks' );
		$bar->appendButton('ListingStandard', '', 'copy', 'COM_MTREE_COPY_LISTINGS', 'links_copy' );
		$bar->appendButton('ListingStandard', '', 'move', 'COM_MTREE_MOVE_LISTINGS', 'links_move' );
	}
	
	/***
	* Configuration
	*/
	public static function CONFIG_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_CONFIGURATION' ), 'config.png' );
		JToolBarHelper::apply('saveconfig');
		
		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', 'cancel', 'JTOOLBAR_CANCEL', 'javascript:history.back();' );

	}
	
	/***
	* Custom Fields
	*/
	public static function CUSTOM_FIELDS() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_CUSTOM_FIELDS' ), 'module' );
		JToolBarHelper::publishList('cf_publish');
		JToolBarHelper::unpublishList('cf_unpublish');
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'newcf', 'new.png', 'new_f2.png', 'JTOOLBAR_NEW', false );
		JToolBarHelper::deleteList( '', 'removecf' );
	}
	
	public static function EDIT_CUSTOM_FIELDS() {
		$cf_id = JFactory::getApplication()->input->getInt( 'cfid' );
		JToolBarHelper::title( JText::_( 'COM_MTREE_CUSTOM_FIELD' ) . ': ' . (($cf_id)?JText::_('JTOOLBAR_EDIT') : JText::_('JTOOLBAR_NEW'))  , 'module' );
		JToolBarHelper::save( 'savecf' );
		JToolBarHelper::apply( 'applycf' );
		JToolBarHelper::cancel( 'cancelcf' );
	}
	
	public static function MANAGE_FIELD_TYPES() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_INSTALLED_FIELD_TYPES' ), 'install.png' );
		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', 'arrow-left', 'COM_MTREE_BACK_TO_CUSTOM_FIELDS', 'index.php?option=com_mtree&amp;task=customfields' );
	}

	/***
	* Link Checker
	*/
	public static function LINKCHECKER_MENU() {
		JToolBarHelper::save('linkchecker');
	}
	
	/***
	* Tools
	*/
	public static function TOOLS_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_TOOLS' ), 'config.png'  );

		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', 'arrow-left', 'JTOOLBAR_BACK', 'javascript:history.back();' );
	}

	public static function EXPORT_MENU() {
		$bar = JToolBar::getInstance('toolbar');
		JToolBarHelper::title( JText::_( 'COM_MTREE_EXPORT' ) );
		
		$bar->appendButton('Link', 'arrow-left', 'JTOOLBAR_BACK', 'javascript:history.back();' );
		$bar->appendButton('Link', 'download', 'COM_MTREE_EXPORT', 'javascript:submitbutton(\\\'csv_export\\\')' );
	}
	
	public static function GEOCODE_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_LOCATE_LISTINGS_IN_MAP' ) );
		
		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', 'arrow-left', 'JTOOLBAR_BACK', 'javascript:history.back();' );
	}
	
	/***
	* Spy
	*/
	public static function SPY_VIEWUSER_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_USER' ), 'user' );
		JToolBarHelper::deleteList();
	}

	/***
	* About
	*/
	public static function ABOUT_MENU() {
		JToolBarHelper::title( JText::_( 'COM_MTREE_ABOUT_MOSETS_TREE' ) );

		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', 'arrow-left', 'JTOOLBAR_BACK', 'javascript:history.back();' );
	}
	
}
?>