<?php
/**
 * @version	$Id: fields.json.php 1967 2013-07-16 05:04:58Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

require_once( JPATH_COMPONENT_ADMINISTRATOR.'/mfields.class.php' );

switch($task)
{
	case "fields.list":
		fieldsList($cat_id, $link_id);
		break;
}

function fieldsList($cat_id, $link_id)
{
	$db	= JFactory::getDBO();
	$my	= JFactory::getUser();
	$link	= new mtLinks( $db );

	$is_admin= JFactory::getApplication()->input->getInt('is_admin', 0);

	# Do not allow Guest to edit listing
	if ( $link_id > 0 && $my->id <= 0 ) {
		$link->load( 0 );
	} else {
		$link->load( $link_id );
	}
	
	$cf_ids = array(1);
	$cf_ids = array_merge($cf_ids,getAssignedFieldsID($cat_id));
	
	# Load all published CORE & custom fields
	$sql = "SELECT cf.*, " . ($link_id ? $link_id : 0) . " AS link_id, cfv.value AS value, cfv.attachment, cfv.counter FROM #__mt_customfields AS cf "
		.	"\nLEFT JOIN #__mt_cfvalues AS cfv ON cf.cf_id=cfv.cf_id AND cfv.link_id = " . $link_id
		.	"\nWHERE cf.hidden ='0' AND cf.published='1'"
		.	((!empty($cf_ids))?"\nAND cf.cf_id IN (" . implode(',',$cf_ids). ") ":'')
		.	"\nORDER BY ordering ASC";
	$db->setQuery($sql);
	
	$fieldsOutput = array();
	$fields = new mFields();
	$fields->setCoresValue( $link->link_name, $link->link_desc, $link->address, $link->city, $link->state, $link->country, $link->postcode, $link->telephone, $link->fax, $link->email, $link->website, $link->price, $link->link_hits, $link->link_votes, $link->link_rating, $link->link_featured, $link->link_created, $link->link_modified, $link->link_visited, $link->publish_up, $link->publish_down, $link->metakey, $link->metadesc, $link->user_id, '' );
	$fields->loadFields($db->loadObjectList());
	
	$fields->resetPointer();
	while( $fields->hasNext() ) {
		unset($fieldObj);
		$field = $fields->getField();
		if( $field->hasInputField() )
		{
			if( $is_admin == 1 && in_array($field->getName(),array('metakey','metadesc')) ) {
				$fields->next();
				continue;
			}
			$fieldObj->id = $field->getId();
			$fieldObj->name = $field->getName();
			$fieldObj->caption = $field->getCaption();
			$fieldObj->modPrefixText = $field->getModPrefixText();
			$fieldObj->inputHTML = $field->getInputHTML();
			$fieldObj->modSuffixText = $field->getModSuffixText();
			$fieldObj->jsValidation = $field->getJsValidation();
			$fieldObj->isRequired = $field->isRequired();
			$fieldObj->fieldTypeClassName = $field->getFieldTypeClassName();
			array_push($fieldsOutput,$fieldObj);
		}
		$fields->next();
	}
	
	echo json_encode($fieldsOutput);
}
?>