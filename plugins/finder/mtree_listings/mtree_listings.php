<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2010-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('JPATH_BASE') or die;

jimport('joomla.application.component.helper');

// Load the base adapter.
require_once JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer/adapter.php';

include( JPATH_ROOT.'/components/com_mtree/init.php');

// Load the language files for the adapter.
// $lang = JFactory::getLanguage();
// $lang->load('plg_finder_mtree_listings');

/**
 * Finder adapter for Moses Tree Listings.
 */
class plgFinderMTree_Listings extends FinderIndexerAdapter
{
	/**
	 * @var		string		The plugin identifier.
	 */
	protected $context = 'MTree_listings';

	/**
	 * The extension name.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $extension = 'com_mtree';
	
	/**
	 * @var		string		The sublayout to use when rendering the results.
	 */
	protected $layout = 'viewlink';

	/**
	 * @var		string		The type of content that the adapter indexes.
	 */
	protected $type_title = 'Listing';

	/**
	 * The table name.
	 *
	 * @var    string
	 * @since  2.5
	 */
	protected $table = '#__mt_links';

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @since   2.5
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Method to update the link information for items that have been changed
	 * from outside the edit screen. This is fired when the item is published,
	 * unpublished, archived, or unarchived from the list view.
	 *
	 * @param   string   $context  The context for the content passed to the plugin.
	 * @param   array    $pks      A list of primary key ids of the content that has changed state.
	 * @param   integer  $value    The value of the state that the content has been changed to.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function onFinderChangeState($context, $pks, $value)
	{
		if ($context == 'com_mtree.listing')
		{
			$this->itemStateChange($pks, $value);
		}
		// Handle when the plugin is disabled
		if ($context == 'com_plugins.plugin' && $value === 0)
		{
			$this->pluginDisable($pks);
		}
	}

	/**
	 * Method to update the item link information when the item category is
	 * changed. This is fired when the item category is published or unpublished
	 * from the list view.
	 *
	 * @param   string   $extension  The extension whose category has been updated.
	 * @param   array    $pks        A list of primary key ids of the content that has changed state.
	 * @param   integer  $value      The value of the state that the content has been changed to.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function onFinderCategoryChangeState($extension, $pks, $value)
	{
		// Make sure we're handling com_mtree categories
		if ($extension == 'com_mtree')
		{
			$this->categoryStateChange($pks, $value);
		}
	}
	
	/**
	 * Method to update index data on category access level changes
	 *
	 * @param   array    $pks    A list of primary key ids of the content that has changed state.
	 * @param   integer  $value  The value of the state that the content has been changed to.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	protected function categoryStateChange($pks, $value)
	{
		// The item's published state is tied to the category
		// published state so we need to look up all published states
		// before we change anything.
		foreach ($pks as $pk)
		{
			$sql = clone($this->getStateQuery());
			$sql->where('c.cat_id = ' . (int) $pk);

			// Get the published states.
			$this->db->setQuery($sql);
			$items = $this->db->loadObjectList();

			// Adjust the state for each item within the category.
			foreach ($items as $item)
			{
				// Translate the state.
				$temp = $this->translateState($item->state, $value);

				// Update the item.
				$this->change($item->id, 'state', $temp);

				// Reindex the item
				$this->reindex($item->id);
			}
		}
	}
	
	/**
	 * Method to get a SQL query to load the published and access states for
	 * an article and category.
	 *
	 * @return  JDatabaseQuery  A database object.
	 *
	 * @since   2.5
	 */
	protected function getStateQuery()
	{
		$sql = $this->db->getQuery(true);
		// Item ID
		$sql->select('l.link_id AS id');
		// Item and category published state
		$sql->select('l.link_published AS state, c.cat_published AS cat_state');
		// Item and category access levels
		$sql->select('\'1\' AS access, \'1\' AS cat_access');
		$sql->from($this->table . ' AS l');
		$sql->join('LEFT', '#__mt_cl AS cl ON cl.link_id = l.link_id AND cl.main = \'1\'');
		$sql->join('LEFT', '#__mt_cats AS c ON c.cat_id = cl.cat_id');

		return $sql;
	}

	/**
	 * Method to remove the link information for items that have been deleted.
	 *
	 * @param   string  $context  The context of the action being performed.
	 * @param   JTable  $table    A JTable object containing the record to be deleted
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	public function onFinderAfterDelete($context, $table)
	{
		if ( in_array($context,array('com_mtree.listing','com_finder.index')) )
		{
			$id = $table->link_id;
		}
		else
		{
			return true;
		}
		// Remove the items.
		return $this->remove($id);
	}
	
	/**
	 * Method to reindex the link information for an item that has been saved.
	 * This simply return true because there is no ACL in Mosets Tree's 
	 * listings
	 *
	 * @param   string   $context  The context of the content passed to the plugin.
	 * @param   JTable   $row     A JTable object
	 * @param   boolean  $isNew    If the content is just about to be created
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	public function onFinderBeforeSave($context, $row, $isNew)
	{
		return true;
	}
	
	/**
	 * Method to reindex the item when it is saved.
	 *
	 * @param   string   $context  The context of the content passed to the plugin.
	 * @param   JTable   $row      A JTable object
	 * @param   boolean  $isNew    If the content has just been created
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 * @throws  Exception on database error.
	 */
	public function onFinderAfterSave($context, $row, $isNew)
	{
		if ($context == 'com_mtree.listing' )
		{
			// Reindex the item
			$this->reindex($row->link_id);
		}

		return true;
	}
	
	/**
	 * Method to index an item. The item must be a FinderIndexerResult object.
	 *
	 * @param	object		The item to index as an FinderIndexerResult object.
	 * @throws	Exception on database error.
	 */
	protected function index(FinderIndexerResult $item, $format = 'html')
	{
		// Check if the extension is enabled
		if (JComponentHelper::isEnabled($this->extension) == false)
		{
			return;
		}

		// Initialize the item parameters.
		$registry = new JRegistry;
		$registry->loadString($item->params);
		$item->params = $registry;

		$registry = new JRegistry;
		$registry->loadString($item->metadata);
		$item->metadata = $registry;

		// Trigger the onPrepareContent event.
		$item->summary	= FinderIndexerHelper::prepareContent($item->summary, $item->params);
		// $item->body		= FinderIndexerHelper::prepareContent($item->link_desc, $item->params);

		// Use listing name for title
		$item->title = $item->link_name;

		// Build the necessary route and path information.
		$item->url	= $this->getURL($item->link_id);
		$item->route	= $this->getURL($item->link_id) . $this->getItemid('com_mtree');
		$item->path	= FinderIndexerHelper::getContentPath($item->route);

		// Add the meta-data processing instructions.
		$simple_searchable_cf_ids = $this->_getSimplSearchableCustomFieldIDs();
		if( !empty($simple_searchable_cf_ids) )
		{
			foreach( $simple_searchable_cf_ids AS $cf_id )
			{
				$item->addInstruction(FinderIndexer::META_CONTEXT, 'cfvalue'.$cf_id);
			}
		}

		// Deals with simple searchable core fields
		$sql = JFactory::getDbo()->getQuery(true);
		$sql->select('substring(cf.field_type,5) AS customfield');
		$sql->from('#__mt_customfields AS cf');
		$sql->where('published = 1 && simple_search = 1 && iscore = 1');
		$this->db->setQuery($sql);
		$simple_searchable_core_custom_fields = $this->db->loadColumn();

		if( !empty($simple_searchable_core_custom_fields) )
		{
			foreach( $simple_searchable_core_custom_fields AS $custom_field )
			{
				if( !in_array($custom_field,array('name','desc')) )
				{
					$item->addInstruction(FinderIndexer::META_CONTEXT, $custom_field);
				}
			}
		}

		// Translate the state. Listings should only be published if the category is published.
		$item->state = $this->translateState($item->link_published, $item->cat_published);

		// Set the language.
		$item->language	= $item->params->get('language', FinderIndexerHelper::getDefaultLanguage());

		// Add the type taxonomy data.
		$item->addTaxonomy('Type', 'Listing');

		// Add additional taxonomy for custom fields containing elements
		$sql = JFactory::getDbo()->getQuery(true);
		$sql->select('cf.cf_id, cf.field_elements');
		$sql->select('CASE WHEN CHAR_LENGTH(cf.search_caption) THEN cf.search_caption ELSE cf.caption END as caption');
		$sql->from('#__mt_customfields as cf');
		$sql->where('cf.published = 1 AND cf.simple_search = 1 AND field_elements !=\'\'');
		$this->db->setQuery($sql);
		$taxonomies = $this->db->loadObjectList();

		if( !empty($taxonomies) )
		{
			foreach( $taxonomies AS $taxonomy )
			{
				$elements = array();
				$elements = explode('|',$taxonomy->field_elements);
				if( !empty($elements) ) {
					foreach( $elements AS $element )
					{
						$item->addTaxonomy($taxonomy->caption, trim($element));
					}
				}
			}
		}

		// Add the category taxonomy data.
		if (!empty($item->cat_name)) {
			$item->addTaxonomy('Category', $item->cat_name, $item->cat_published);
		}

		// Get content extras.
		FinderIndexerHelper::getContentExtras($item);
		
		// Index the item.
		$this->indexer->index($item);
	}

	/**
	 * Method to get the SQL query used to retrieve the list listings.
	 *
	 * @return	object		A JDatabaseQuery object.
	 */
	protected function getListQuery($sql = null)
	{
		global $mtconf;
		
		$simple_searchable_cf_ids = $this->_getSimplSearchableCustomFieldIDs();

		$db = JFactory::getDbo();
		// Check if we can use the supplied SQL query.
		$sql = is_a($sql, 'JDatabaseQuery') ? $sql : $db->getQuery(true);

		$sql->select('\'1\' AS access');
		$sql->select('l.link_id, l.link_name, l.alias, l.user_id');
		$sql->select('l.address, l.city, l.state, l.country, l.postcode, l.telephone, l.fax, l.email, l.website, l.price');
		$sql->select('l.link_desc AS summary');
		$sql->select('c.cat_id, c.cat_name, c.alias, c.cat_published, c.cat_approved');
		$sql->select('l.link_published, l.link_approved');
		$sql->select('l.publish_up AS publish_start_time, l.publish_down AS publish_end_time');

		switch( $mtconf->get('sef_link_slug_type') )
		{
			case 1:
			default:
				$sql->select('l.alias as slug');
				break;
			case 2:
				$sql->select('l.link_id as slug');
				break;
		}
		
		$sql->select('c.alias as catslug');
		$sql->select('u.name AS author');
		$sql->from('#__mt_links AS l');
		$sql->join('LEFT', '#__mt_cl AS cl ON cl.link_id = l.link_id AND cl.main = 1');
		$sql->join('LEFT', '#__mt_cats AS c ON c.cat_id = cl.cat_id');
		$sql->join('LEFT', '#__users AS u ON u.id = l.user_id');
		
		if( !empty($simple_searchable_cf_ids) )
		{
			foreach( $simple_searchable_cf_ids AS $cf_id )
			{
				$sql->select('cfv'.$cf_id.'.value AS cfvalue'.$cf_id);
				$sql->join(
					'LEFT', '#__mt_cfvalues AS cfv'.$cf_id
					.' ON cfv'.$cf_id.'.cf_id = '.$cf_id.' AND cfv'.$cf_id.'.link_id = l.link_id'
					);
			}
		}
		
		return $sql;
	}

	/**
	 * Method to get the URL for the item. The URL is how we look up the link
	 * in the Finder index.
	 *
	 * @param   integer  $link_id    The link id of the listing.
	 * @param   string   $extension  The extension the category is in.
	 * @param   string   $task       The task for the URL.
	 *
	 * @return  string  The URL of the item.
	 *
	 * @since   2.5
	 */
	protected function getURL($link_id, $extension='com_mtree', $task='viewlink')
	{
		return 'index.php?option=' . $extension . '&task=' . $task . '&link_id=' . $link_id;
	}

	/**
	 * Method to get the Itemid of a published component's menu item.
	 *
	 * @param	string		component string in the form of: com_xxx
	 * @return	string		'&Itemid=X' or empty if no results.
	 */
	protected function getItemId($option)
	{
		$items	= JMenu::getInstance('Site')->getItems('component',$option);
		if( !empty($items) )
		{
			return '&Itemid='.$items[0]->id;
		}
		else
		{
			return '';
		}
	}

	/**
	 * Method to translate the native content states into states that the
	 * indexer can use.
	 *
	 * @param   integer  $item      The item state.
	 * @param   integer  $category  The category state. [optional]
	 *
	 * @return  integer  The translated indexer state.
	 *
	 * @since   2.5
	 */
	protected function translateState($item, $category = null)
	{
		// If category is present, factor in its states as well
		if ($category !== null)
		{
			if ($category == 0)
			{
				$item = 0;
			}
		}
		
		if ($item <= 0) {
			return 0;
		} else {
			return 1;
		}
	}
	
	/**
	 * Method to update index data on published state changes
	 *
	 * @param   array    $pks    A list of primary key ids of the content that has changed state.
	 * @param   integer  $value  The value of the state that the content has been changed to.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	protected function itemStateChange($pks, $value)
	{
		// The item's published state is tied to the category
		// published state so we need to look up all published states
		// before we change anything.
		foreach ($pks as $pk)
		{
			$sql = clone($this->getStateQuery());
			$sql->where('l.link_id = ' . (int) $pk);

			// Get the published states.
			$this->db->setQuery($sql);
			$item = $this->db->loadObject();

			// Translate the state.
			$temp = $this->translateState($value, $item->cat_state);

			// Update the item.
			$this->change($pk, 'state', $temp);

			// Reindex the item
			$this->reindex($pk);
		}
	}

	/**
	 * Method to get a list of simple searchable custom field IDs
	 *
	 * @return	array		An array of custom field ids
	 */
	private function _getSimplSearchableCustomFieldIDs()
	{
		$sql = JFactory::getDbo()->getQuery(true);
		$sql->select('cf.cf_id');
		$sql->from('#__mt_customfields as cf');
		$sql->where('cf.published = 1 AND cf.simple_search = 1');
		$this->db->setQuery($sql);
		
		return $this->db->loadColumn();
	}
	
	/**
	 * Method to get a content item to index.
	 *
	 * @param	integer		The id of the content item.
	 * @return	object		A FinderIndexerResult object.
	 * @throws	Exception on database error.
	 */
	protected function getItem($id)
	{
		// Get the list query and add the extra WHERE clause.
		$sql = $this->getListQuery();
		$sql->where('l.link_id = '.(int)$id);

		// Get the item to index.
		$this->db->setQuery($sql);
		$row = $this->db->loadAssoc();

		// Check for a database error.
		if ($this->db->getErrorNum()) {
			// Throw database error exception.
			throw new Exception($this->db->getErrorMsg(), 500);
		}

		// Convert the item to a result object.
		$item = JArrayHelper::toObject($row, 'FinderIndexerResult');

		// Set the item type.
		$item->type_id	= $this->type_id;

		// Set the item layout.
		$item->layout	= $this->layout;

		return $item;
	}
	
	/**
	 * Method to setup the indexer to be run.
	 *
	 * @return	boolean		True on success.
	 */
	protected function setup()
	{
		return true;
	}
	
}