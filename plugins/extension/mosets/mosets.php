<?php
/**
 * @copyright	Copyright (C) 2011 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Mosets master extension plugin.
 *
 * @package	Mosets.Plugin
 * @subpackage	Extension.Mosets
 * @since	1.0
 */
class plgExtensionMosets extends JPlugin
{
	/**
	 * @var		integer Extension Identifier
	 * @since	1.6
	 */
	private $eid = 0;

	/**
	 * @var		JInstaller Installer object
	 * @since	1.6
	 */
	private $installer = null;

	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	
	/**
	 * @var		integer Fieldtype ID
	 * @since	1.0
	 */
	private $fieldtype_id = null;
	
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Handle post extension install to install Mosets Tree Fieldtype.
	 * Due to the fact that onExtensionAfterUpdate not firing upon update 
	 * for 'file' type, this is also the function that handles update 
	 * routine.
	 *
	 * @param	JInstaller	Installer object
	 * @param	int		Extension Identifier
	 * @since	1.0
	 */
	public function onExtensionAfterInstall($installer, $eid)
	{
		$this->installer = $installer;

		if ($eid && $this->_isMosetsExtension())
		{
			$this->installer = $installer;
			$this->eid = $eid;
			
			switch($this->getMosetsExtensionType())
			{
				case 'mtreeFieldtype':
				
					if( $this->fieldtypeExists($installer->manifest->name) )
					{
						$this->updateFieldtype($installer->manifest);
					}
					else
					{
						$installSuccess = $this->installFieldtype($installer->manifest);
						$this->createField($installer->manifest);
					}
					
					if( $installSuccess ) {
						return true;
					} else {
						return false;
					}
					break;
			}
		} else {
			return false;
		}
	}
	
	public function createField( $manifest )
	{
		if( $manifest )
		{
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->insert('#__mt_customfields');
			$query->set('field_type = ' . $db->Quote($manifest->name));
			$query->set('caption = '. $db->Quote($manifest->caption));
			$query->set('published = 0');
			$query->set('ordering = 99');
			$query->set('advanced_search = 0');
			$query->set('simple_search = 0');
			$query->set('iscore = 0');

			$db->setQuery($query);
			if ($db->execute())
			{
				$cf_id = $db->insertid();
				
				require_once( JPATH_ADMINISTRATOR.'/components/com_mtree/admin.mtree.class.php' );
				$row = new mtCustomFields( $db );
				$row->reorder( 'published >= 0' );

				// Make this custom field available to all top level categories
				$db->setQuery(
					"INSERT INTO #__mt_fields_map (`cf_id`,`cat_id`) "
					."SELECT '$cf_id', cat_id FROM #__mt_cats WHERE cat_approved = 1 AND cat_parent <= 0"
					);
				if($db->execute()) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
	}
	
	public function updateFieldtype( $manifest )
	{
		$this->installFieldtype( $manifest, true );
	}
	
	public function installFieldtype( $manifest, $update=false )
	{
		if( $manifest )
		{
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			
			if( $update ) {
				$query->update('#__mt_fieldtypes');
				$query->where('field_type = ' . $db->Quote($manifest->name));
			} else {
				$query->insert('#__mt_fieldtypes');
				$query->set('field_type = ' . $db->Quote($manifest->name));
			}
			$query->set('ft_caption = '. $db->Quote($manifest->caption));
			$query->set('ft_version = '. $db->Quote($manifest->version));
			$query->set('ft_website = '. $db->Quote($manifest->authorUrl));
			$query->set('ft_desc = '. $db->Quote($manifest->description));
			$query->set('use_elements = '. $db->Quote($manifest->useElements));
			$query->set('use_size = '. $db->Quote($manifest->useSize));
			if ($manifest->useColumns)
			{
				$query->set('use_columns = '. $db->Quote($manifest->useColumns));
			} else {
				$query->set('use_columns = 0');
			}
			if ($manifest->usePlaceholder)
			{
				$query->set('use_placeholder = '. $db->Quote($manifest->usePlaceholder));
			} else {
				$query->set('use_placeholder = 0');
			}
			
			if ($manifest->isFile)
			{
				$query->set('is_file = '. $db->Quote($manifest->isFile));
			} else {
				$query->set('is_file = 0');
			}
			
			if ($manifest->taggable)
			{
				$query->set('taggable = '. $db->Quote($manifest->taggable));
			} else {
				$query->set('taggable = 0');
			}
			$query->set('iscore = 0');
			$db->setQuery($query);
			if ($db->execute())
			{
				if( !$update ) {
					$this->fieldtype_id = $db->insertid();
				}
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	public function fieldtypeExists( $fieldtypeName )
	{
		if( !empty($fieldtypeName) )
		{
			$db = JFactory::getDBO();
			
			$query = $db->getQuery(true);
			$query->select('ft_id');
			$query->from('#__mt_fieldtypes');
			$query->where('field_type = ' . $db->Quote($fieldtypeName));
			$db->setQuery($query);
			
			$ft_id = $db->loadResult();
			
			if( $ft_id ) {
				return $ft_id;
			} else {
				return false;
			}
			
		} else {
			return false;
		}
	}
	
	public function getMosetsExtensionType()
	{
		if($this->installer && !is_null($this->installer->manifest->attributes()->mosetsExtension))
		{
			return $this->installer->manifest->attributes()->mosetsExtension;
		} else {
			return false;
		}	
	}

	private function _isMosetsExtension()
	{
		if($this->installer)
		{
			if( $this->installer->manifest->attributes()->mosetsExtension == 'mtreeFieldtype' ) {
				return true;
			} else {
				return false;
			}
		}
	}

	public function onExtensionBeforeUninstall($eid)
	{
		$installer = JInstaller::getInstance();
		
		$fieldtype = JTable::getInstance('Extension');
		$fieldtype->load($eid);
		if ($fieldtype->type == 'file')
		{
			$manifestFile = JPATH_ADMINISTRATOR . '/components/com_mtree/fieldtypes/' . $fieldtype->name . '/' . $fieldtype->name .'.xml';
			if (file_exists($manifestFile))
			{
				$xml =JFactory::getXML($manifestFile);

				if( !$xml) {
					return false;
				} else {
					
					$db = JFactory::getDBO();
					$query = $db->getQuery(true);
					
					// Get cf_id(s) that uses this field type
					$query->select('cf_id')->from('#__mt_customfields')->where('field_type = ' . $db->quote($xml->name));
					$db->setQuery($query);
					$cf_ids = $db->loadColumn();
					
					if(count($cf_ids)>0) {
						// Delete attachments
						$query->clear();
						$query->delete()->from('#__mt_cfvalues_att')->where('cf_id IN (' . implode(',',$cf_ids) . ')');
						$db->setQuery($query);
						$db->execute();

						// Delete values the uses this field type
						$query->clear();
						$query->delete()->from('#__mt_cfvalues')->where('cf_id IN (' . implode(',',$cf_ids) . ')');
						$db->setQuery($query);
						$db->execute();	

						# Delete instances of this field type
						$query->clear();
						$query->delete()->from('#__mt_customfields')->where('cf_id IN (' . implode(',',$cf_ids) . ')');
						$db->setQuery($query);
						$db->execute();	

						# Delete fields map
						$query->clear();
						$query->delete()->from('#__mt_fields_map')->where('cf_id IN (' . implode(',',$cf_ids) . ')');
						$db->setQuery($query);
						$db->execute();		
					}
					
					// Delete the fieldtype
					$query->clear();
					$query->delete()->from('#__mt_fieldtypes')->where('field_type = '. $db->Quote($xml->name));
					$db->setQuery($query);

					if ($db->execute())
					{
						return true;
					} else {
						return false;
					}
				}
				
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
?>