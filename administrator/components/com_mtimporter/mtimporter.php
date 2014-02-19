<?php
/**
 * @version		$Id: mtimporter.php 2098 2013-10-09 10:52:01Z cy $
 * @package		MT Importer
 * @copyright	(C) 2005-2013 Mosets Consulting. All rights reserved.
 * @license		GNU General Public License
 * @author		Lee Cher Yeong <mtree@mosets.com>
 * @url			http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

require_once( JPATH_COMPONENT.'/admin.mtimporter.html.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_mtree/tools.mtree.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_mtree/admin.mtree.class.php' );

jimport('joomla.filesystem.file');

$task		= JFactory::getApplication()->input->getCmd('task', '');

switch( $task ) {

default:
case "check_csv":
	check_csv();
	break;

case "import_csv":
	import_csv();
	break;

case "check_hotproperty":
	check_hotproperty();
	break;

case "import_hotproperty":
	import_hotproperty();
	break;

}

function check_csv() {
	$database =& JFactory::getDBO();
	HTML_mtimporter::check_csv();
}

function get_mt_links_columns()
{
	$database =& JFactory::getDBO();
	$database->setQuery( 'SHOW COLUMNS FROM #__mt_links' );
	$columns = $database->loadObjectList();
	
	$return = array();
	foreach( $columns AS $column )
	{
		$return[] = $column->Field;
	}
	
	return $return;
}

function get_cf_aliases()
{
	$database =& JFactory::getDBO();
	
	$database->setQuery( 'SELECT cf_id, alias FROM #__mt_customfields AS cf '
		. 'LEFT JOIN #__mt_fieldtypes AS ft ON ft.field_type = cf.field_type '
		. 'WHERE cf.iscore = 0 AND (ft.is_file = 0 || cf.field_type IN (\'radiobutton\', \'mcheckbox\', \'selectlist\'))' 
		);
	$aliases = $database->loadAssocList('cf_id', 'alias');
	// echo '<pre>'; var_dump($aliases); echo '</pre>';
	return $aliases;
}

function import_csv() {
	$app = JFactory::getApplication();
	$my = JFactory::getuser();

	$database =& JFactory::getDBO();

	$files = $_FILES;
	
	$file_csv = $files['file_csv'];

	$dryrun = JFactory::getApplication()->input->getInt( 'dryrun' );
	
	if( $dryrun ) {
		$sample_imports = array();
	}
	
	$mt_links_columns = get_mt_links_columns();
	
	$num_of_sample_import_rows = 10;
	
	// Stores custom field aliases, to match column name. If a column name matches one of the aliases here,
	// it will be imported to the custom field.
	$cf_aliases = get_cf_aliases();

	if( isset($file_csv['tmp_name']) && file_exists($file_csv['tmp_name']) )
	{
		# By default, all listings are owned by current user
		$admin_user_id = $my->id;

		# Now, start reading the file
		$row = 0;
		$index_catid = -1;
		$index_linkname = -1;
		$handle = fopen($file_csv['tmp_name'], "r");
	
		// Test if the csv file is using /r as the line ending. If it is, use our custom csv parser.
		$data = fgets($handle, 100000);
		$type = 0;
		// if(strpos($data,"\r") > 0) {
		// 	$type = 1;
		// }
		rewind($handle);

		// This stores a list of column name/alias that can not be imported as core fields or existing custom fields.
		$create_cfs = array();
	
		while (($data = mtgetcsv($handle,$type,$row)) !== FALSE) {
			$row++;

			# Set the field name first
			if ( $row == 1 ) {
				$fields = array();
				for ($f=0; $f < count($data); $f++) {
					if ( $data[$f] == 'cat_id' ) {
						$index_catid = $f;
					}
					$fields[] = $data[$f];
					if( trim($data[$f]) == 'link_name' ) {
						$index_linkname = $f;
					}
					
					if( 
						!in_array(trim($data[$f]),$mt_links_columns) 
						&&
						trim($data[$f]) != 'cat_id' 
						&&
						!in_array($data[$f],$cf_aliases)
						&&
						!empty($data[$f])
					) 
					{
						$create_cfs[] = $data[$f];
					}
				}
				
				// Create custom fields
				$new_cf_ids = array();
				$create_cf_captions = array();

				// The list of custom field IDs (cf_ids) that currently exists in Mosets Tree
				$existing_cf_ids = array_keys($cf_aliases);

				$cf_ordering = 999;
				foreach( $create_cfs AS $create_cf )
				{
					if( in_array($create_cf,$existing_cf_ids) )
					{
						continue; 
					}

					$create_cf_caption = ucwords(str_replace(array('-','_'),' ',$create_cf));
					$database->setQuery( 'INSERT INTO #__mt_customfields '
						. ' ( field_type, size, ordering, caption, alias )' 
						. ' VALUES(\'mtext\', 50, '.$cf_ordering++.', \''.$create_cf_caption.'\', \''.JFilterOutput::stringURLSafe($create_cf).'\')'
						);

					if( $dryrun )
					{
						// echo '<br />' . $database->getQuery();
						$create_cf_captions[$create_cf] = $create_cf_caption;
					}
					else
					{
						$database->execute();
						$new_cf_ids[] = $database->insertid();
						
						
					}
				}
				
				
				// Update #__fields_map for the newly created custom fields
				if( !empty($new_cf_ids) )
				{
					$database->setQuery( 'SELECT cat_id FROM #__mt_cats WHERE cat_parent <= 0' );
					$tlcat_ids = $database->loadColumn();

					foreach( $new_cf_ids AS $cf_id )
					{
						$arr_insert_values = array();
						foreach( $tlcat_ids AS $tlcat_id )
						{
							$arr_insert_values[] = '('.$cf_id.', '.$tlcat_id.')';
						}
						$database->setQuery( 'INSERT INTO #__mt_fields_map (`cf_id`, `cat_id`) VALUES ' . implode(', ',$arr_insert_values) . ';' );
						$database->execute();
					}
				}
				
				// Update cf_aliases with the newly added custom fields above
				$cf_aliases = get_cf_aliases();

				if( $dryrun )
				{
					// echo "<br />Fields list: <b>" .implode("|",$fields) . "</b><br />";
					// echo "<br />Custom fields that will be created: <b>" .implode("|",$create_cfs) . "</b><br />";
					// echo "<br />Custom fields aliases: <b>" .implode("|",$cf_aliases) . "</b><br />";
					// echo '<br />Link Name Index: ' . $index_linkname;
					// echo '<br />Category ID Index: ' . $index_catid;
				}
			} else {
			
				# Make sure the listing has at least a link_name. Everything else is optional.
				if ( !empty($data[$index_linkname]) ) {
					$num = count($data);
					$sql_cf_ids = array();
					$sql_cf_insertvalues = array();
					$sql_insertfields = array('alias','link_published','link_approved','link_created','user_id');
					$sql_insertvalues = array(JFilterOutput::stringURLSafe($data[$index_linkname]),1,1,date('Y-m-d H:i:s'),$admin_user_id);
					for ($c=0; $c < $num; $c++) {

						if ( !empty($data[$c]) && !empty($fields[$c]) && $c != $index_catid ) {
							switch($fields[$c]) {
								case 'alias':
									$sql_insertvalues[0] = $database->escape($data[$c]);
									break;
								case 'link_published':
									$sql_insertvalues[1] = $database->escape($data[$c]);
									break;
								case 'link_approved':
									$sql_insertvalues[2] = $database->escape($data[$c]);
									break;
								case 'link_created':
									$sql_insertvalues[3] = $database->escape($data[$c]);
									break;
								case 'user_id':
									$sql_insertvalues[4] = $database->escape($data[$c]);
									break;
								default:
									if ( in_array($fields[$c],$mt_links_columns) ) {
										// Core Field
										$sql_insertfields[] = $fields[$c];
										$sql_insertvalues[] = $database->escape($data[$c]);
									} else
									{
										// Custom Fields
										if( is_numeric($fields[$c]) && array_key_exists($fields[$c],$cf_aliases) )
										{
											// This field matches one of the custom field alias. 
											// Import it to the custom field.
											$sql_cf_ids[] = $fields[$c];
											$sql_cf_insertvalues[] = $database->escape($data[$c]);

										}
										else
										{
											$sql_cf_ids[] = array_search(JFilterOutput::stringURLSafe($fields[$c]),$cf_aliases);
											$sql_cf_insertvalues[] = $database->escape($data[$c]);
										}
									}
									break;
							}
							if( $dryrun )
							{
								if( $row < ($num_of_sample_import_rows +2) ) {
									$sample_imports[$row][$fields[$c]] = $data[$c];
								}
								// echo "<br /><b>".$fields[$c].": </b>".$database->escape($data[$c]);
							}
						}
						
						// Special IF condition to populate the 'cat_id' column in dry-run sample import data.
						if( $dryrun && $c == $index_catid && $row < ($num_of_sample_import_rows +2) ) {
							$sample_imports[$row][$fields[$c]] = $data[$c];
						}

					}
				
					if ( count($sql_insertfields) == count($sql_insertvalues) && count($sql_insertvalues) > 0 ) {
						# Insert core data
						$sql = "INSERT INTO #__mt_links (".implode(",",$sql_insertfields).") VALUES ('".implode("','",$sql_insertvalues)."')";
						$database->setQuery($sql);
						if( $dryrun )
						{
							// echo '<br />' . $sql;
							$link_id = 0;
						} else {
							$database->execute();
							$link_id = $database->insertid();
						}
					
						# Insert Custom Field's data
						$values = array();

						if(count($sql_cf_ids)>0 && count($sql_cf_insertvalues)>0) {
							$sql = "INSERT INTO #__mt_cfvalues (cf_id,link_id,value) VALUES";
							for($i=0;$i<count($sql_cf_ids);$i++) {
								$values[] = "('" . $sql_cf_ids[$i] . "', '" . $link_id . "', '" . $sql_cf_insertvalues[$i] . "')";
							}
							$sql .= implode(',',$values);
							$database->setQuery($sql);
							if( $dryrun )
							{
								// echo '<br />'.$database->getQuery();
							} else {
								$database->execute();
							}
						}
					
						# Assign listing to categories
						if(!isset($data[$index_catid]) || stristr($data[$index_catid],',') === false) {
							$sql = "INSERT INTO #__mt_cl (link_id, cat_id, main) VALUES (".$link_id.", ".( ($index_catid == -1 || empty($data[$index_catid])) ? 0:$data[$index_catid] ).",1)";
							$database->setQuery($sql);
							if( $dryrun )
							{
								// echo '<br />'.$database->getQuery();
							} else {
								$database->execute();
							}
						# This record is assigning to more than one category at once.
						} else {
							$cat_ids = array();
							if( $index_catid > -1 )
							{
								$cat_ids = explode(',',$data[$index_catid]);
							}
							
							$j = 0;
							foreach($cat_ids AS $cat_id) {
								if( !empty($cat_id) )
								{
									$sqlvalue = '('.$link_id.','.$cat_id.',';
									if($j==0) {
										$sqlvalue .= '1';
									} else {
										$sqlvalue .= '0';
									}
									$sqlvalue .= ')';
									$sqlvalues[] = $sqlvalue;
									++$j;
								}
							}
							if( !empty($sqlvalues) ) {
								$sql = 'INSERT INTO #__mt_cl (link_id, cat_id, main) VALUES ' . implode(', ',$sqlvalues);
								$database->setQuery($sql);
								if( $dryrun )
								{
									// echo $database->getQuery();
								} else {
									$database->execute();
								}

							}
							unset($sqlvalues);
						}
					}
				}
				else
				{
					// echo 'Can not detect name value';
					// echo '<pre>';
					// var_dump($data);
					// echo '</pre>';
				}

			}
			if( $dryrun )
			{
				// echo '<hr />';
			}
		}

		fclose($handle);
		
		if( !$dryrun )
		{
			$app->redirect( 'index.php?option=com_mtree', JText::_( 'COM_MTIMPORTER_IMPORT_PROCESS_COMPLETE' ) );
		} else {
			HTML_mtimporter::csv_import_dryrun_report($fields, $create_cfs, $create_cf_captions, $index_linkname, $index_catid, $row, $sample_imports);
		}
	} else {
		$app->redirect( 'index.php?option=com_mtimporter&task=check_csv', JText::_( 'COM_MTIMPORTER_NO_FILE_SPECIFIED' ) );
	}
}

function mtgetcsv($handle,$type=0,$line=0) {
	switch($type) {
		case 1:
			rewind($handle);
			$data = fgets($handle);
			$newlinedData = explode("\r",$data);
			if(($line+1)>count($newlinedData)) {
				return false;
			} else {
				$expr="/,(?=(?:[^\"]*\"[^\"]*\")*(?![^\"]*\"))/";
				$results=preg_split($expr,trim($newlinedData[$line]));
				return preg_replace("/^\"(.*)\"$/","$1",$results);
			}
			break;
		case 0:
		default:
			return fgetcsv($handle, 100000, ",");
	}
	
}

function check_hotproperty() {
	$app = JFactory::getApplication();
	
	$db = $app->getCfg('db');
	$db_prefix = $app->getCfg('dbprefix');

	$database =& JFactory::getDBO();

	# Select Hot Property types
	$database->setQuery( "show tables from $db like '".$db_prefix."hp_prop_types'" );
	$tmp = $database->loadResult();
	if ( $tmp == $db_prefix."hp_prop_types" ) {
		$database->setQuery( "SELECT count(*) FROM #__hp_prop_types" );
		$results_count['types'] = $database->loadResult();
	} else {
		$results_count['types'] = -1;
	}

	# Select Hot Property properties
	$database->setQuery( "show tables from $db like '".$db_prefix."hp_properties'" );
	$tmp = $database->loadResult();
	if ( $tmp == $db_prefix."hp_properties" ) {
		$database->setQuery( "SELECT count(*) FROM #__hp_properties" );
		$results_count['properties'] = $database->loadResult();
	} else {
		$results_count['properties'] = -1;
	}
	
	# Select the number of Hot Property companies
	$database->setQuery( "show tables from $db like '".$db_prefix."hp_companies'" );
	$tmp = $database->loadResult();
	if ( $tmp == $db_prefix."hp_companies" ) {
		$database->setQuery( "SELECT count(*) FROM #__hp_companies" );
		$results_count['companies'] = $database->loadResult();
	} else {
		$results_count['companies'] = -1;
	}
	
	# Select the number of Hot Property agents
	$database->setQuery( "show tables from $db like '".$db_prefix."hp_agents'" );
	$tmp = $database->loadResult();
	if ( $tmp == $db_prefix."hp_agents" ) {
		$database->setQuery( "SELECT count(*) FROM #__hp_agents" );
		$results_count['agents'] = $database->loadResult();
	} else {
		$results_count['agents'] = -1;
	}
	
	# Find Mosets Tree's Status
	// $database->setQuery( "SELECT count(*) FROM #__mt_links" );
	// $mt_count['listings'] = $database->loadResult();
	// $database->setQuery( "SELECT count(*) FROM #__mt_cats" );
	// $mt_count['cats'] = $database->loadResult();

	HTML_mtimporter::check_hotproperty( $results_count );
}

function import_hotproperty() {
	$app = JFactory::getApplication();
	$database =& JFactory::getDBO();
	$my = JFactory::getUser();

	// Mapping for Hot Property Extra Field types to Mosets Tree field types
	$fieldtype_map = array(
		'text'		=> 'mtext',
		'multitext'	=> 'mtext',
		'selectlist'	=> 'selectlist',
		'selectmultiple'=> 'selectmultiple',
		'checkbox'	=> 'mcheckbox',
		'radiobutton'	=> 'radiobutton',
		'link'		=> 'mweblink'
	);
	
	// Stores the mapping from old Hot Property ID to new Mosets Tree ID
	$id_maps = array();
	
	$db = $app->getCfg('db');
	$db_prefix = $app->getCfg('dbprefix');

	// Get all Hot Property companies and add it as top level category 'Hot Property Companies'
	$sql = 'SELECT * FROM #__hp_companies';
	$database->setQuery( $sql );
	$companies = $database->loadObjectList();

	if( !empty($companies) )
	{
		// Create a top level category in Mosets Tree to store Hot Property Companies data
		$sql = 'INSERT INTO #__mt_cats (cat_name, alias, cat_parent, cat_published, cat_approved) VALUES(\'Hot Property Companies\', \'hot-property-companies\', \'0\', 1, 1)';
		$database->setQuery( $sql );
		$database->query();
		$hotproperty_company_cat_id = $database->insertid();

		foreach( $companies AS $company )
		{
			$sql = 'INSERT INTO #__mt_links (`link_name`, `alias`, `link_desc`, `user_id`, `link_published`, `link_approved`, `address`, `city`, `state`, `country`, `postcode`, `telephone`, `fax`, `email`, `website`)';
			$sql .= 'VALUES ('
				. '\''.$database->escape($company->name).'\', '
				. '\''.JFilterOutput::stringURLSafe($company->name).'\', '
				. '\''.$database->escape($company->desc).'\', '
				. '\''.$my->id.'\', '
				. '\'1\', '
				. '\'1\', '
				. '\''.$database->escape($company->address).'\', '
				. '\''.$database->escape($company->suburb).'\', '
				. '\''.$database->escape($company->state).'\', '
				. '\''.$database->escape($company->country).'\', '
				. '\''.$database->escape($company->postcode).'\', '
				. '\''.$database->escape($company->telephone).'\', '
				. '\''.$database->escape($company->fax).'\', '
				. '\''.$database->escape($company->email).'\', '
				. '\''.$database->escape($company->website).'\' '
				. ')';
			$database->setQuery( $sql );
			$database->query();
			$company_id = $database->insertid();
			$id_maps['companies'][$company->id] = $company_id;

			// Assign to a category.
			$sql = 'INSERT INTO `#__mt_cl` (`link_id`, `cat_id`, `main`) VALUES('.$company_id.', '.$hotproperty_company_cat_id.', 1)';
			$database->setQuery( $sql );
			$database->query();
		}
	}

	// Get all Hot Property agents and add it as top level category 'Hot Property Agents'
	$sql = 'SELECT * FROM #__hp_agents';
	$database->setQuery( $sql );
	$agents = $database->loadObjectList();

	if( !empty($agents) )
	{
		// Create a top level category in Mosets Tree to store Hot Property Agents data
		$sql = 'INSERT INTO #__mt_cats (cat_name, alias, cat_parent, cat_published, cat_approved, cat_association) VALUES(\'Hot Property Agents\', \'hot-property-agents\', \'0\', 1, 1, '.$hotproperty_company_cat_id.')';
		$database->setQuery( $sql );
		$database->query();
		$hotproperty_agent_cat_id = $database->insertid();

		// Create a custom field based on Associated Listing to store agent's company association
		$sql = 'INSERT INTO #__mt_customfields (field_type, caption, alias, published, summary_view)';
		$sql .= 'VALUES ('
			. '\'associatedlisting\', '
			. '\'Company\', '
			. '\'company\', '
			. '\'1\', '
			. '\'1\' '
			. ')';
		$database->setQuery( $sql );
		$database->query();
		$company_cf_id = $database->insertid();
		
		$database->setQuery( 'INSERT INTO #__mt_fields_map (`cf_id`, `cat_id`) VALUES('.$company_cf_id.', '.$hotproperty_agent_cat_id.');' );
		$database->query();

		foreach( $agents AS $agent )
		{
			$sql = 'INSERT INTO #__mt_links (`link_name`, `alias`, `link_desc`, `user_id`, `link_published`, `link_approved`, `telephone`, `email` )';
			$sql .= 'VALUES ('
				. '\''.$database->escape($agent->name).'\', '
				. '\''.JFilterOutput::stringURLSafe($agent->name).'\', '
				. '\''.$database->escape($agent->desc).'\', '
				. '\''.$agent->user.'\', '
				. '\'1\', '
				. '\'1\', '
				. '\''.$database->escape($agent->mobile).'\', '
				. '\''.$database->escape($agent->email).'\' '
				. ')';
			$database->setQuery( $sql );
			$database->query();
			$agent_id = $database->insertid();
			$id_maps['agents'][$agent->id] = $agent_id;
			
			// Maps Agent ID to User ID to be user in assigning properties to User
			$id_maps['users'][$agent->id] = (int) $agent->user;

			// Assign to a category.
			$sql = 'INSERT INTO `#__mt_cl` (`link_id`, `cat_id`, `main`) VALUES('.$agent_id.', '.$hotproperty_agent_cat_id.', 1)';
			$database->setQuery( $sql );
			$database->query();
			
			// Associate agent with company
			$sql = 'INSERT INTO `#__mt_links_associations` (`link_id1`, `link_id2`) VALUES('.$id_maps['companies'][$agent->company].', '.$agent_id.')';
			$database->setQuery( $sql );
			$database->query();
		}
	}

	// Create a top level category in Mosets Tree to store all Hot Property Properties
	$sql = 'INSERT INTO #__mt_cats (cat_name, alias, cat_parent, cat_published, cat_approved, cat_association) VALUES(\'Hot Property Properties\', \'hot-property-properties\', \'0\', 1, 1, '.$hotproperty_agent_cat_id.')';
	$database->setQuery( $sql );
	$database->query();
	$hotproperty_cat_id = $database->insertid();
	
	// Create a custom field based on Associated Listing to store property's agent association
	$sql = 'INSERT INTO #__mt_customfields (field_type, caption, alias, published, summary_view)';
	$sql .= 'VALUES ('
		. '\'associatedlisting\', '
		. '\'Agent\', '
		. '\'agent\', '
		. '\'1\', '
		. '\'1\' '
		. ')';
	$database->setQuery( $sql );
	$database->query();
	$agent_cf_id = $database->insertid();
	
	$database->setQuery( 'INSERT INTO #__mt_fields_map (`cf_id`, `cat_id`) VALUES('.$agent_cf_id.', '.$hotproperty_cat_id.');' );
	$database->query();

	// Import Types
	// Get all Hot Property types and add it to top level category 'Hot Property Properties'
	$sql = 'SELECT * FROM #__hp_prop_types';
	$database->setQuery( $sql );
	$types = $database->loadObjectList();

	foreach( $types AS $type )
	{
		$sql = 'INSERT INTO #__mt_cats (cat_name, alias, cat_desc, cat_parent, cat_published, cat_approved, ordering) ';
		$sql .= 'VALUES(\''.$type->name.'\', \''.JFilterOutput::stringURLSafe($type->name).'\', \''.$type->desc.'\', '.$hotproperty_cat_id.', '.$type->published.', 1, '.$type->ordering.');';
		$database->setQuery( $sql );
		$database->query();
		$id_maps['types'][$type->id] = $database->insertid();
	}
	
	// Import Extra Fields and assign it to $hotproperty_cat_id
	$sql = 'SELECT * FROM #__hp_prop_ef WHERE iscore = 0';
	$database->setQuery( $sql );
	$extrafields = $database->loadObjectList();

	foreach( $extrafields AS $extrafield )
	{
		$sql = 'INSERT INTO #__mt_customfields (field_type, caption, alias, default_value, size, field_elements, prefix_text_mod, suffix_text_mod, prefix_text_display, suffix_text_display, ordering, hidden, published, hide_caption, advanced_search, search_caption, summary_view)';
		$sql .= 'VALUES ('
			. '\''.$fieldtype_map[$extrafield->field_type].'\', '
			. '\''.$database->escape($extrafield->caption).'\', '
			. '\''.$extrafield->name.'\', '
			. '\''.$database->escape($extrafield->default_value).'\', '
			. '\''.$extrafield->size.'\', '
			. '\''.$database->escape($extrafield->field_elements).'\', '
			. '\''.$database->escape($extrafield->prefix_text).'\', '
			. '\''.$database->escape($extrafield->append_text).'\', '
			. '\''.$database->escape($extrafield->prefix_text).'\', '
			. '\''.$database->escape($extrafield->append_text).'\', '
			. '\''.$extrafield->ordering.'\', '
			. '\''.$extrafield->hidden.'\', '
			. '\''.$extrafield->published.'\', '
			. '\''.$extrafield->hideCaption.'\', '
			. '\''.$extrafield->search.'\', '
			. '\''.$database->escape($extrafield->search_caption).'\', '
			. '\''.$extrafield->listing.'\' '
			. ')';
		$database->setQuery( $sql );
		$database->query();
		
		$id_maps['extrafields'][$extrafield->id] = $database->insertid();
	}

	
	// Update #__fields_map for the newly created custom fields
	if( !empty($id_maps['extrafields']) )
	{
		foreach( $id_maps['extrafields'] AS $ef_id => $cf_id )
		{
			$arr_insert_values[] = '('.$cf_id.', '.$hotproperty_cat_id.')';
		}
		$database->setQuery( 'INSERT INTO #__mt_fields_map (`cf_id`, `cat_id`) VALUES ' . implode(', ',$arr_insert_values) . ';' );
		$database->query();
	}

	// Import Properties
	$sql = 'SELECT * FROM #__hp_properties';
	$database->setQuery( $sql );
	$properties = $database->loadObjectList();
	
	foreach( $properties AS $property )
	{
		$sql = 'INSERT INTO #__mt_links (`link_name`, `alias`, `link_desc`, `user_id`, `link_hits`, `link_featured`, `link_published`, `link_approved`,  `metakey`, `metadesc`, `internal_notes`, `link_created`, `publish_up`, `publish_down`, `link_modified`, `address`, `city`, `state`, `country`, `postcode`, `price`)';
		$sql .= 'VALUES ('
			. '\''.$database->escape($property->name).'\', '
			. '\''.JFilterOutput::stringURLSafe($property->name).'\', '
			. '\''.$database->escape($property->intro_text.$property->full_text).'\', '
			. '\''.(
					(
						is_numeric($id_maps['users'][$property->agent]) 
						&& 
						$id_maps['users'][$property->agent] > 0 
					) 
					? 
					$id_maps['users'][$property->agent] 
					: 
					$my->id
				).'\', '
			. '\''.$property->hits.'\', '
			. '\''.$property->featured.'\', '
			. '\''.$property->published.'\', '
			. '\'1\', '
			. '\''.$database->escape($property->metakey).'\', '
			. '\''.$database->escape($property->metadesc).'\', '
			. '\''.$database->escape($property->note).'\', '
			. '\''.$property->created.'\', '
			. '\''.$property->publish_up.'\', '
			. '\''.$property->publish_down.'\', '
			. '\''.$property->modified.'\', '
			. '\''.$database->escape($property->address).'\', '
			. '\''.$database->escape($property->suburb).'\', '
			. '\''.$database->escape($property->state).'\', '
			. '\''.$database->escape($property->country).'\', '
			. '\''.$database->escape($property->postcode).'\', '
			. '\''.$property->price.'\' '
			. ')';
		$database->setQuery( $sql );
		$database->query();
		$link_id = $database->insertid();
		$id_maps['properties'][$property->id] = $link_id;

		// Assign to a category.
		$sql = 'INSERT INTO `#__mt_cl` (`link_id`, `cat_id`, `main`) VALUES('.$link_id.', '.$id_maps['types'][$property->type].', 1)';
		$database->setQuery( $sql );
		$database->query();

		// Associate agent/property with company/agent
		$sql = 'INSERT INTO `#__mt_links_associations` (`link_id1`, `link_id2`) VALUES('.$id_maps['agents'][$property->agent].', '.$link_id.')';
		$database->setQuery( $sql );
		$database->query();
	}

	// Import extra fields data in to #__mt_cfvalues
	$sql = 'SELECT * FROM #__hp_properties2 WHERE value != \'\' AND property > 0;';
	$database->setQuery( $sql );
	$extrafield_values = $database->loadObjectList();
	
	foreach ($extrafield_values as $extrafield_value) {
		
		if( isset($id_maps['extrafields'][$extrafield_value->field]) && $id_maps['properties'][$extrafield_value->property] )
		{
			
			$sql = 'INSERT INTO #__mt_cfvalues (cf_id, link_id, value) ';
			$sql .= 'VALUES('
				. $id_maps['extrafields'][$extrafield_value->field] . ', '
				. $id_maps['properties'][$extrafield_value->property] . ', '
				. '\'' . $database->escape($extrafield_value->value) . '\' '
				. ')';
			$database->setQuery( $sql );
			$database->query();
		}
	}

	// Assign Hot Property core fields to newly created top level category for Properties, Agents & Companies
	$array_fields['companies'] = array('corename', 'coredesc', 'coreaddress', 'corecity', 'corestate', 'corecountry', 'corepostcode', 'coretelephone', 'corefax', 'coreemail', 'corewebsite');
	$array_fields['agents'] = array('corename', 'coreuser', 'coredesc', 'coretelephone', 'coreemail');
	$array_fields['properties'] = array('corename', 'coredesc', 'coreaddress', 'corecity', 'corestate', 'corecountry', 'corepostcode', 'coretelephone', 'corefax', 'coreemail', 'corewebsite', 'coreprice');
	
	foreach( $array_fields AS $table => $fields )
	{
		$sql = 'SELECT cf_id FROM #__mt_customfields WHERE field_type IN (\''.implode('\', \'', $fields).'\');';
		$database->setQuery( $sql );
		$cf_ids = $database->loadResultArray();
		
		if( !empty($cf_ids) )
		{
			$arr_insert_values = array();
			foreach( $cf_ids AS $cf_id )
			{
				switch( $table )
				{
					case 'companies':
						$arr_insert_values[] = '('.$cf_id.', '.$hotproperty_company_cat_id.')';
						break;
					case 'agents':
						$arr_insert_values[] = '('.$cf_id.', '.$hotproperty_agent_cat_id.')';
						break;
					case 'properties':
						$arr_insert_values[] = '('.$cf_id.', '.$hotproperty_cat_id.')';
						break;
				}
			}
			$database->setQuery( 'INSERT INTO #__mt_fields_map (`cf_id`, `cat_id`) VALUES ' . implode(', ',$arr_insert_values) . ';' );
			$database->query();
		}
	}

	// Stores a unique list of link_ids that contains images
	$arr_image_link_ids = array();
	
	// Import agents & companies photos
	$arr_photos = array(
		'companies' => 'company',
		'agents' => 'agent'
		);
	
	foreach( $arr_photos AS $table => $folder )
	{
		$sql = 'SELECT id, photo FROM #__hp_'.$table.' WHERE photo != \'\';';
		$database->setQuery( $sql );
		$photos = $database->loadObjectList();
		
		if( !empty($photos) )
		{
			foreach( $photos AS $photo )
			{
				if( empty($photo->photo) ) continue;
				
				$sql = 'INSERT INTO #__mt_images (`link_id`, `filename` )';
				$sql .= 'VALUES (\''.$id_maps[$table][$photo->id].'\', \''.$photo->photo.'\');';
				$database->setQuery( $sql );
				$database->query();

				JFile::copy(
					JPATH_ROOT.'/media/com_hotproperty/images/'.$folder.'/'.$photo->photo,
					JPATH_ROOT.'/media/com_mtree/images/listings/s/'.$photo->photo
				);
				JFile::copy(
					JPATH_ROOT.'/media/com_hotproperty/images/'.$folder.'/'.$photo->photo,
					JPATH_ROOT.'/media/com_mtree/images/listings/m/'.$photo->photo
				);
				JFile::copy(
					JPATH_ROOT.'/media/com_hotproperty/images/'.$folder.'/'.$photo->photo,
					JPATH_ROOT.'/media/com_mtree/images/listings/o/'.$photo->photo
				);
				
				$arr_image_link_ids[] = $id_maps[$table][$photo->id];
			}
		}
	}

	// Import properties photos
	$sql = 'SELECT * FROM #__hp_photos;';
	$database->setQuery( $sql );
	$photos = $database->loadObjectList();
	
	if( !empty($photos) )
	{
		foreach( $photos AS $photo )
		{
			// We are going to use the thumb's filename as the primary name in #__mt_images.
			// The thumbnail (thumb) is also going to be our primary checks. If it does not 
			// exists, no photo is going to be migrated.
			
			$thb_photo_path = JPATH_ROOT.'/media/com_hotproperty/images/thb/'.$photo->thumb;
			$std_photo_path = JPATH_ROOT.'/media/com_hotproperty/images/std/'.$photo->standard;
			$ori_photo_path = JPATH_ROOT.'/media/com_hotproperty/images/ori/'.$photo->original;
			
			if( empty($photo->thumb) || !JFile::exists($thb_photo_path) ) continue;
			
			$sql = 'INSERT INTO #__mt_images (`link_id`, `filename`, `ordering` )';
			$sql .= 'VALUES (\''.$id_maps['properties'][$photo->property].'\', \''.$photo->thumb.'\', '.$photo->ordering.');';
			$database->setQuery( $sql );
			$database->query();

			JFile::copy(
				$thb_photo_path,
				JPATH_ROOT.'/media/com_mtree/images/listings/s/'.$photo->thumb
			);

			if( JFile::exists($std_photo_path) ) {
				JFile::copy(
					$std_photo_path,
					JPATH_ROOT.'/media/com_mtree/images/listings/m/'.$photo->thumb
				);
			} else {
				JFile::copy(
					$thb_photo_path,
					JPATH_ROOT.'/media/com_mtree/images/listings/m/'.$photo->thumb
				);
			}
			
			if( JFile::exists($ori_photo_path) ) {
				JFile::copy(
					$ori_photo_path,
					JPATH_ROOT.'/media/com_mtree/images/listings/o/'.$photo->thumb
				);
			} elseif( JFile::exists($std_photo_path) ) {
				JFile::copy(
					$std_photo_path,
					JPATH_ROOT.'/media/com_mtree/images/listings/o/'.$photo->thumb
				);
			} else {
				JFile::copy(
					$thb_photo_path,
					JPATH_ROOT.'/media/com_mtree/images/listings/o/'.$photo->thumb
				);
			}
			
			$arr_image_link_ids[] = $id_maps['properties'][$photo->property];
		}
		
		$arr_image_link_ids = array_unique($arr_image_link_ids);

		// Compact the ordering values of the imported images
		$images = new mtImages( $database );
		
		foreach( $arr_image_link_ids AS $image_link_id )
		{
			$images->reorder('link_id='.$image_link_id);
		}

	}

	if( !empty($types) || !empty($agents) || !empty($companies) )
	{
		$tree = new mtTree();
		$tree->rebuild( 0, 1);
		$app->redirect( 'index.php?option=com_mtree', JText::sprintf('Import process Completed. %s properties, %s agents and %s companies imported.', count($properties), count($agents), count($companies)) );
	}
}

class mUpgrade {
	var $updated = false;
	function updated() {
		return $this->updated;
	}
	function addColumn($table, $column_name, $column_info='', $after='') {
		if(addColumn($table, $column_name, $column_info, $after)) {
			$this->updated = true;
		}
	}
	function addRows($table, $rows) {
		if(addRows($table, $rows)) {
			$this->updated = true;
		}	
	}
	function printStatus( $msg, $status=1 ) {
		if( $status == -1 ) {
			echo '<tr><td><b><span style="color:red">Error</span></b> - '.$msg.'</td></tr>';
		} elseif( $status == 1 OR $status == 0 ) {
			echo '<tr><td><b>'.(($status)?'<span style="color:green">OK</span>':'Skipped').'</b> - '.$msg.'</td></tr>';
		} elseif( $status == 2 ) {
			echo '<tr><td><strong>'.$msg.'</strong></td></tr>';
		}
	}
}
?>