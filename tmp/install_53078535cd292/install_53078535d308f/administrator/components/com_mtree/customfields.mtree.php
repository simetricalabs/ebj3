<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2005-2013 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */


defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ADMINISTRATOR.'/components/com_mtree/customfields.mtree.html.php' );
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/***
* Custom Fields
*/
function managefieldtypes( $option ) {
	global $mtconf;
	$database	= JFactory::getDBO();

	$database->setQuery( "SELECT ft.* FROM #__mt_fieldtypes AS ft ORDER BY iscore ASC, ft_caption ASC" );
	$rows = $database->loadObjectList('field_type');
	
	HTML_mtcustomfields::managefieldtypes( $option, $rows );
}

function customfields( $option ) {
	global $app, $mtconf;
	
	$database = JFactory::getDBO();
	
	$limit = $app->getUserStateFromRequest( "viewlistlimit".__FUNCTION__, 'limit', $mtconf->getjconf('list_limit') );
	$limitstart = $app->getUserStateFromRequest( "viewcli{$option}limitstart".__FUNCTION__, 'limitstart', 0 );

 	$database->setQuery( 'SELECT COUNT(*) FROM #__mt_customfields');
	$total = $database->loadResult();
	
	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);
	
	$database->setQuery( 'SELECT cf.*, ft.ft_caption FROM #__mt_customfields AS cf '
		.	'LEFT JOIN #__mt_fieldtypes AS ft ON ft.field_type = cf.field_type '
		.	'ORDER BY ordering ASC'
		. "\nLIMIT $pageNav->limitstart,$pageNav->limit");
	$custom_fields = $database->loadObjectList();
	HTML_mtcustomfields::customfields( $custom_fields, $pageNav, $option );
}

function editcf( $cf_id, $option ) {
	global $mtconf;
	
	$database	= JFactory::getDBO();
	
	$row = new mtCustomFields( $database );
	$row->load( $cf_id );
	$form = null;

	if ($row->cf_id == 0) {
		$row->caption = '';
		$row->alias = '';
		$row->placeholder_text = '';
		$row->field_type = 'mtext';
		$row->cat_id = 0;
		$row->ordering = 0;
		$row->hidden = 0;
		$row->published = 1;
		$row->size = 30;
		$row->hide_caption = 0;
		$row->advanced_search = 0;
		$row->simple_search = 0;
		$row->filter_search = 0;
		$row->search_caption = '';
		$row->details_view=1;
		$row->summary_view=0;
		$row->tag_search=0;
		
	} else {
		$fieldtype_path = JPATH_ROOT . $mtconf->get('relative_path_to_fieldtypes') . $row->field_type . '/';
		$fieldtype_params_xml_file = $fieldtype_path.$row->field_type.'.xml';
		if( JFile::exists($fieldtype_params_xml_file) )
		{
			# Parameters
			JForm::addFormPath($fieldtype_path);
			$form = JForm::getInstance('com_mtree.editcf', $row->field_type, array('control' => 'params'), true, '/extension/config/fields');
			
			$registry = new JRegistry;
			$registry->loadString($row->params);

			$form->bind($registry->toArray());
		}
	}

	$lists = array();
	$isDisabled = array();

	# build the html select list for ordering
	$order = JHtml::_('list.genericordering', 'SELECT ordering AS value, caption AS text'
		. "\nFROM #__mt_customfields ORDER BY ordering ASC"	);
	$lists['ordering'] = JHtml::_('select.genericlist', $order, 'ordering', 'size="1"', 'value', 'text', intval( $row->ordering ) );
	
	# Generate the Field Types
	$cf_types = array (
		// 'text' => JText::_( 'COM_MTREE_FIELD_TYPE_TEXT' ),
		'selectlist' => JText::_( 'COM_MTREE_FIELD_TYPE_SELECTLIST' ),
		'selectmultiple' => JText::_( 'COM_MTREE_FIELD_TYPE_SELECTMULTIPLE' ),
		// 'checkbox' => JText::_( 'COM_MTREE_FIELD_TYPE_CHECKBOX' ),
		'radiobutton' => JText::_( 'COM_MTREE_FIELD_TYPE_RADIOBUTTON' )
		);
	# Get custom field types
	$database->setQuery("SELECT * FROM #__mt_fieldtypes WHERE iscore = '0' ORDER BY ft_caption ASC");
	$custom_cf_types = $database->loadObjectList('field_type');

	$lists["field_types"] = '<select name="field_type" onchange="updateInputs(this.value)">';
	$lists["field_types"] .= '<optgroup label="' . JText::_( 'COM_MTREE_BASIC_FIELDTYPES' ) . '">';
	foreach( $cf_types AS $key => $value ) {
		$lists["field_types"] .= '<option value="' . $key . '"' . (($row->field_type == $key)?' selected':'') . '>' . $value . '</option>';
	}
	$lists["field_types"] .= '</optgroup>';
	$lists["field_types"] .= '<optgroup label="' . JText::_( 'COM_MTREE_CUSTOM_FIELDTYPES' ) . '">';
	foreach( $custom_cf_types AS $key => $value ) {
		$lists["field_types"] .= '<option value="' . $key . '"' . (($row->field_type == $key)?' selected':'') . '>' . $value->ft_caption . '</option>';
	}
	$lists["field_types"] .= '</optgroup>';
	$lists["field_types"] .= '</select>';

	if( $cf_id == 1 ) {
		array_push($isDisabled, 'details_view', 'summary_view');
	}

	if( in_array($row->cf_id,array(1)) ) {
		array_push($isDisabled, 'required_field');
	} elseif ( in_array($row->cf_id,array(3,14,15,16,17,18,19,20,21,22)) ) {
		array_push($isDisabled, 'required_field');
	}

	# make order list
	$orders = JHtml::_('list.genericordering', 'SELECT ordering AS value, caption AS text'
		. "\nFROM #__mt_customfields ORDER BY ordering"	);
	$lists["order"] = JHtml::_('select.genericlist', $orders, 'ordering', 'size="1"', 'value', 'text', intval( $row->ordering ) );
	
	# Get top level categories for fields assignment
	$sql = "SELECT * FROM #__mt_cats WHERE cat_approved = 1 AND cat_parent = 0";
	if( $mtconf->get('first_cat_order1') != '' )
	{
		$sql .= ' ORDER BY ' . $mtconf->get('first_cat_order1') . ' ' . $mtconf->get('first_cat_order2');
		if( $mtconf->get('second_cat_order1') != '' )
		{
			$sql .= ', ' . $mtconf->get('second_cat_order1') . ' ' . $mtconf->get('second_cat_order2');
		}
	}
	$database->setQuery($sql);
	$cats = $database->loadObjectList();

	# Get fields mapping
	if ($row->cf_id == 0) {
		$fields_map_cats = array(0);
		foreach($cats AS $cat)
		{
			array_push($fields_map_cats,$cat->cat_id);
		}
	} else {
		$sql = "SELECT cat_id FROM #__mt_fields_map WHERE cf_id = " . $row->cf_id;
		$database->setQuery($sql);
		$fields_map_cats = $database->loadColumn();
	}
	
	HTML_mtcustomfields::editcf( $row, $custom_cf_types, $lists, $isDisabled, $cats, $fields_map_cats, $form, $option );
}

function savecf( $option ) {
	
	$app		= JFactory::getApplication('site');
	$database	= JFactory::getDBO();
	$row 		= new mtCustomFields( $database );
	
	// Check for request forgeries
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	$hide_caption = JFactory::getApplication()->input->getInt( 'hide_caption', 0 );

	$params = JFactory::getApplication()->input->get( 'params', '' );
	$post	= $_POST;
	$post['prefix_text_mod'] = JFactory::getApplication()->input->get('prefix_text_mod', '', 'HTML');
	$post['suffix_text_mod'] = JFactory::getApplication()->input->get('suffix_text_mod', '', 'HTML');
	$post['prefix_text_display'] = JFactory::getApplication()->input->get('prefix_text_display', '', 'HTML');
	$post['suffix_text_display'] = JFactory::getApplication()->input->get('suffix_text_display', '', 'HTML');

	if( !array_key_exists('hide_caption', $post) || $post['hide_caption'] != '1' )  {
		$post['hide_caption'] = 0;
	}
	
	if( empty($post['alias']) )
	{
		$post['alias'] = JFilterOutput::stringURLSafe($post['caption']);
	}
	
 	// Save parameters
	$params = JFactory::getApplication()->input->get( 'params', array(), 'array' );

	if ( is_array( $params ) ) {
		$attribs = array();
		foreach ( $params as $k=>$v) {
			$attribs[] = "$k=$v";
		}
		$row->params = implode( "\n", $attribs );
		unset($post['params']);
	}
	
	if (!$row->bind( $post )) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}

	if( $row->cf_id == 0 )
	{
		// Set the tag_search colum based on taggable value of the field type
		$database->setQuery( 'SELECT taggable FROM #__mt_fieldtypes WHERE field_type = ' . $database->Quote($row->field_type) . ' LIMIT 1' );
		$row->tag_search = $database->loadResult();
	}
	
	# Successively remove '|' at the start and end to eliminate blank options
	while (substr($row->field_elements, -1) == '|') {
		$row->field_elements = substr($row->field_elements, 0, -1);
	}
	while (substr($row->field_elements, 0, 1) == '|') {
		$row->field_elements = substr($row->field_elements, 1);
	}

	# Clean up Field Elements's data. Remove spaces around '|' so that it is used correctly in SET COLUMN in MySQL
	$tmp_fe_array = explode('|',$row->field_elements);
	foreach($tmp_fe_array AS $tmp_fe) {
		# Detect usage of comma.
		if (strrpos($tmp_fe,',') == FALSE) 
		{
			$tmp_fe_array2[] = trim($tmp_fe);
		} else {
			echo "<script> alert('".JText::_( 'COM_MTREE_WARNING_COMMAS_ARE_NOT_ALLOWED_IN_FIELD_ELEMENTS' )."'); window.history.go(-1); </script>\n";
			exit();
		}
	}
	$row->field_elements = implode('|',$tmp_fe_array2);

	# Put new item to last
	if($row->cf_id <= 0) $row->ordering = 999;

	# Check if field_type is taggable. If yes, we set 1 to tag_search for this custom field
	/*
	$database->setQuery( 'SELECT taggable FROM #__mt_fieldtypes WHERE field_type = ' . $database->Quote($row->field_type) . ' LIMIT 1' );
	$taggable = $database->loadResult();
	
	if( $taggable == 1 ) {
		$row->tag_search = 1;
	} else {
		$row->tag_search = 0;
	}
	*/
	if (!$row->store()) {
		echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
		exit();
	}
	$row->reorder( 'published >= 0' );

	# Save fields map
	$database->setQuery("DELETE FROM #__mt_fields_map WHERE cf_id = " . $row->cf_id);
	$database->execute();
	
	if( !empty($post['fields_map_cats']) )
	{
		$fields_map_insert_values = implode('),('.$row->cf_id.',',$post['fields_map_cats']);
		$database->setQuery("INSERT INTO #__mt_fields_map (`cf_id`,`cat_id`) VALUES (".$row->cf_id.",".$fields_map_insert_values.")");
		$database->execute();
	}
	
	$task = JFactory::getApplication()->input->getCmd( 'task', '' );

	if ( $task == "applycf" ) {
		$app->redirect( "index.php?option=$option&task=editcf&cfid=" . $row->cf_id );
	} else {
		$app->redirect( "index.php?option=$option&task=customfields" );
	}

}

function ordercf( $cf_id, $inc, $option ) {
	
	$app		= JFactory::getApplication('site');
	$database	= JFactory::getDBO();
	
	$row = new mtCustomFields( $database );
	$row->load( $cf_id );
	$row->move( $inc, '' );
	$app->redirect( 'index.php?option='. $option .'&task=customfields' );
}

function cf_publish( $cf_id, $publish=1 ,$option ) {

	$app		= JFactory::getApplication('site');
	$database	= JFactory::getDBO();

	// Check for request forgeries
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	if (!is_array( $cf_id ) || count( $cf_id ) < 1) {
		echo "<script> alert('".JText::_( 'COM_MTREE_PLEASE_SELECT_AN_ITEM_TO_PUBLISH_OR_UNPUBLISH' )."'); window.history.go(-1);</script>\n";
		exit();
	}

	$ids = implode( ',', $cf_id );

	$database->setQuery( 'UPDATE #__mt_customfields SET published = ' . intval($publish) . " WHERE cf_id IN ($ids) AND cf_id NOT IN (1)" );
	if (!$database->execute()) {
		echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		exit();
	}

	$app->redirect( "index.php?option=$option&task=customfields" );

}

function removecf( $id, $option ) {

	$app		= JFactory::getApplication('site');
	$database	= JFactory::getDBO();

	// Check for request forgeries
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	for ($i = 0; $i < count($id); $i++) {
		$query = "SELECT iscore FROM #__mt_customfields WHERE cf_id='" . intval($id[$i]) . "' LIMIT 1";
		$database->setQuery($query);
		
		if(($iscore = $database->loadResult()) == null) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
		}
		
		if ($iscore == 1) {
			$app->redirect( "index.php?option=$option&task=customfields", JText::_( 'COM_MTREE_CANNOT_DELETE_CORE_FIELD' ) );
		} else {
			# Delete the main fields data
			$database->setQuery("DELETE FROM #__mt_customfields WHERE `cf_id`='".intval($id[$i])."'");
			$database->execute();

			# Delete the data associated with this field
			$database->setQuery("DELETE FROM #__mt_cfvalues WHERE `cf_id`='".intval($id[$i])."'");
			$database->execute();
			
			# Delete the data associated with this field
			$database->setQuery("DELETE FROM #__mt_cfvalues_att WHERE `cf_id`='".intval($id[$i])."'");
			$database->execute();
			
			# Delete fields map
			$database->setQuery("DELETE FROM #__mt_fields_map WHERE `cf_id`='".intval($id[$i])."'");
			$database->execute();
			
		}
	}
	$app->redirect("index.php?option=$option&task=customfields",JText::plural('COM_MTREE_N_CUSTOM_FIELDS_DELETED', count($id)));
}

function cancelcf( $option ) {
	JFactory::getApplication('site')->redirect( 'index.php?option='. $option .'&task=customfields' );
}

?>