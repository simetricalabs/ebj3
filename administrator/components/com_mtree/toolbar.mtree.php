<?php
/**
 * @version	$Id: toolbar.mtree.php 2058 2013-09-13 04:13:41Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */


defined('_JEXEC') or die('Restricted access');

require_once( 'toolbar.mtree.html.php' );

$task = JFactory::getApplication()->input->getCmd( 'task', '');
$task2	= strval(JFactory::getApplication()->input->getCmd( 'task2', ''));

$bar =& JToolBar::getInstance('toolbar');

switch ($task) {

	/***
	 * Link
	 */
	case "newlink":
	case "editlink":
	case "editlink_for_approval":
	case "editlink_change_cat":
	case "editlink_browse_cat":
	case "editlink_add_cat":
	case "editlink_remove_cat":
		TOOLBAR_mtree::EDITLINK_MENU();
		break;

	case "links_move":
		TOOLBAR_mtree::MOVELINKS_MENU();
		break;

	case "links_copy":
		TOOLBAR_mtree::COPYLINKS_MENU();
		break;

	/***
	 * Pending / Approval
	 */
	case "listpending_links":
		TOOLBAR_mtree::LISTPENDING_LINKS_MENU();
		break;
	case "listpending_cats":
		TOOLBAR_mtree::LISTPENDING_CATS_MENU();
		break;
	case "listpending_reviews":
		TOOLBAR_mtree::LISTPENDING_REVIEWS_MENU();
		break;
	case "listpending_reports":
		TOOLBAR_mtree::LISTPENDING_REPORTS_MENU();
		break;
	case "listpending_reviewsreports":
		TOOLBAR_mtree::LISTPENDING_REVIEWSREPORTS_MENU();
		break;
	case "listpending_reviewsreply":
		TOOLBAR_mtree::LISTPENDING_REVIEWSREPLY_MENU();
		break;
	case "listpending_claims":
		TOOLBAR_mtree::LISTPENDING_CLAIMS_MENU();
		break;

	/***
	 * Reviews
	 */

	case "newreview":
	case "editreview":
		TOOLBAR_mtree::EDITREVIEW_MENU();
		break;

	case "reviews_list":
		TOOLBAR_mtree::LISTREVIEWS_MENU();
		break;

	/***
	 * Category
	 */
	case "newcat":
	case "editcat":
	case "editcat_browse_cat":
	case "editcat_add_relcat":
	case "editcat_remove_relcat":
		TOOLBAR_mtree::EDITCAT_MENU();
		break;

	case "cats_move":
		TOOLBAR_mtree::MOVECATS_MENU();
		break;

	case "cats_copy":
		TOOLBAR_mtree::COPYCATS_MENU();
		break;
	
	case "removecats":
		TOOLBAR_mtree::REMOVECATS_MENU();
		break;

	case "listcats":
	case "":
		TOOLBAR_mtree::LISTCATS_MENU();
		break;
	
	/***
	* Search Results
	*/
	case "search":
		$search_where	= JFactory::getApplication()->input->getInt( 'search_where', '');
		if ( $search_where == 1 ) {
			TOOLBAR_mtree::SEARCH_LISTINGS();
		} else {
			TOOLBAR_mtree::SEARCH_CATEGORIES();
		}
		break;

	/***
	* Tree Templates
	*/
	case "templates":
		TOOLBAR_mtree::TREE_TEMPLATES();
		break;
	case "template_pages":
		TOOLBAR_mtree::TREE_TEMPLATEPAGES();
		break;
	case "edit_templatepage":
		TOOLBAR_mtree::TREE_EDITTEMPLATEPAGE();
		break;
	case "new_template":
		TOOLBAR_mtree::TREE_NEWTEMPLATE();
		break;
	case "copy_template":
		TOOLBAR_mtree::TREE_COPYTEMPLATE();
		break;

	
	/***
	* Advanced Search
	*/
	case "advsearch":
		TOOLBAR_mtree::ADVSEARCH();
		break;
	case "advsearch2":
		TOOLBAR_mtree::ADVSEARCH2();
		break;

	/***
	* Configuration
	*/
	case "config":
		TOOLBAR_mtree::CONFIG_MENU();
		break;
	
	/***
	* Custom Fields
	*/
	case "customfields":
		TOOLBAR_mtree::CUSTOM_FIELDS();
		break;
	case "editcf":
	case "newcf":
		TOOLBAR_mtree::EDIT_CUSTOM_FIELDS();
		break;
	case 'managefieldtypes':
		TOOLBAR_mtree::MANAGE_FIELD_TYPES();
		break;

	/***
	* Link Checker
	*/
	case 'linkchecker':
		TOOLBAR_mtree::LINKCHECKER_MENU();
		break;

	/***
	* Geocode
	*/
	case 'geocode':
		TOOLBAR_mtree::GEOCODE_MENU();
		break;
		
	/***
	* Spy
	*/
	case 'spy':
		switch($task2) {
			case 'viewuser':
				TOOLBAR_mtree::SPY_VIEWUSER_MENU();
				break;
			default:
				JToolBarHelper::title( JText::_( 'COM_MTREE_SPY_DIRECTORY' ) );
				break;
		}
		break;
	/***
	* Export
	*/
	case "csv":
		TOOLBAR_mtree::EXPORT_MENU();
		break;
	case "csv_export":
		$bar->appendButton('Link', 'arrow-left', 'JTOOLBAR_BACK', 'javascript:history.back();' );
		JToolBarHelper::title( JText::_( 'COM_MTREE_EXPORT' ) );
		break;
	
	/***
	* Tools
	*/
	case "tools":
		TOOLBAR_mtree::TOOLS_MENU();
		break;

	/***
	* About / License / Upgrade
	*/
	case "about":
		TOOLBAR_mtree::ABOUT_MENU();
		break;

	case "upgrade":
		break;
	default:
//		MENU_Default::MENU_Default();
		break;
}
?>