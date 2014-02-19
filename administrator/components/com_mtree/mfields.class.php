<?php
/**
 * @version	$Id: mfields.class.php 2119 2013-10-19 07:11:37Z cy $
 * @package	Mosets Tree
 * @copyright	(C) 2005-2012 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

class mFields {
	
	var $fields = null;
	var $mField = null;
	var $pointer = null;
	var $coresValue = null;
	var $assocLink = null;
	var $cat_id = null;
	
	function mFields( $fieldsObjectList=null ) {
		$this->pointer = 0;
		if( !is_null($fieldsObjectList) ) {
			$this->loadFields($fieldsObjectList);
		}
	}

	function loadField( $fieldsObject ) {
		if( $fieldsObject->iscore && !is_null($this->coresValue) ) {
			$name = substr($fieldsObject->field_type,4);
			if( array_key_exists($name,$this->coresValue) ) {
				$fieldsObject->value = $this->coresValue[$name];
			} else {
				$fieldsObject->value = $this->coresValue['link_'.$name];
			}
		}
		$this->fields[] = array(
			'id' => $fieldsObject->cf_id,
			'linkId' => (isset($fieldsObject->link_id)?$fieldsObject->link_id:0),
			'fieldType' => $fieldsObject->field_type,
			'caption' => $fieldsObject->caption,
			'value' => isset($fieldsObject->value)?$fieldsObject->value:'',
			'searchValue' => isset($fieldsObject->searchValue)?$fieldsObject->searchValue:'',
			'defaultValue' => $fieldsObject->default_value,
			'prefixTextMod' => $fieldsObject->prefix_text_mod,
			'suffixTextMod' => $fieldsObject->suffix_text_mod,
			'prefixTextDisplay' => $fieldsObject->prefix_text_display,
			'suffixTextDisplay' => $fieldsObject->suffix_text_display,
			'placeholderText' => $fieldsObject->placeholder_text,
			'catId' => $fieldsObject->cat_id,
			// 'catName' => $fieldsObject->cat_name,
			'ordering' => $fieldsObject->ordering,
			'hidden' => $fieldsObject->hidden,
			'size' => $fieldsObject->size,
			'fieldElements' => $fieldsObject->field_elements,
			'arrayFieldElements' => explode("|",$fieldsObject->field_elements),
			'requiredField' => $fieldsObject->required_field,
			'hideCaption' => $fieldsObject->hide_caption,
			'tagSearch' => $fieldsObject->tag_search,
			'simpleSearch' => $fieldsObject->simple_search,
			'advancedSearch' => $fieldsObject->advanced_search,
			'searchCaption' => $fieldsObject->search_caption,
			'detailsView' => $fieldsObject->details_view,
			'summaryView' => $fieldsObject->summary_view,
			'isCore' => $fieldsObject->iscore,
			'params' => $fieldsObject->params,
			// 'class' => $fieldsObject->ft_class,
			'attachment' => isset($fieldsObject->attachment)?$fieldsObject->attachment:'',
			'counter' => isset($fieldsObject->counter)?$fieldsObject->counter:0
			);
	}

	function loadFields( $fieldsObjectList ) {
		if( is_null($fieldsObjectList) ) {
			// Do nothing
		} else {
			foreach( $fieldsObjectList AS $fieldsObject ) {
				$this->loadField($fieldsObject);
			}
		}
	}
	
	function setCoresValue( $link_name, $link_desc, $address, $city, $state, $country, $postcode, $telephone, $fax, $email, $website, $price, $link_hits, $link_votes, $link_rating, $link_featured, $link_created, $link_modified, $link_visited, $publish_up, $publish_down, $metakey, $metadesc, $user_id, $username ) {
		$this->coresValue['link_name'] = $link_name;
		$this->coresValue['link_desc'] = $link_desc;
		$this->coresValue['address'] = $address;
		$this->coresValue['city'] = $city;
		$this->coresValue['state'] = $state;
		$this->coresValue['country'] = $country;
		$this->coresValue['postcode'] = $postcode;
		$this->coresValue['telephone'] = $telephone;
		$this->coresValue['fax'] = $fax;
		$this->coresValue['email'] = $email;
		$this->coresValue['website'] = $website;
		$this->coresValue['price'] = $price;
		$this->coresValue['link_hits'] = $link_hits;
		$this->coresValue['link_votes'] = $link_votes;
		$this->coresValue['link_rating'] = $link_rating;
		$this->coresValue['link_featured'] = $link_featured;
		$this->coresValue['link_created'] = $link_created;
		$this->coresValue['link_modified'] = $link_modified;
		$this->coresValue['link_visited'] = $link_visited;
		$this->coresValue['link_publishup'] = $publish_up;
		$this->coresValue['link_publishdown'] = $publish_down;
		$this->coresValue['metakey'] = $metakey;
		$this->coresValue['metadesc'] = $metadesc;
		$this->coresValue['link_user'] = $user_id . '|' . $username;
	}

	function setAssocLink( $assoc_link = array() ) {
		if( isset($assoc_link['cat_name']) ) {
			$this->assocLink['cat_name'] = $assoc_link['cat_name'];
		}
		if( isset($assoc_link['cat_id']) ) {
			$this->assocLink['cat_id'] = $assoc_link['cat_id'];
		}
		if( isset($assoc_link['link_name']) ) {
			$this->assocLink['link_name'] = $assoc_link['link_name'];
		}
		if( isset($assoc_link['link_id']) ) {
			$this->assocLink['link_id'] = $assoc_link['link_id'];
		}
	}
	
	function getAssocLink( $key ) {
		if( !isset($key) && !empty($this->assocLink) )
		{
			return $this->assocLink;
		}
		elseif( isset( $this->assocLink[$key] ) )
		{
			return $this->assocLink[$key];
		}
		else
		{
			return false;
		}
	}
	
	function setCatID( $cat_id )
	{
		$db = JFactory::getDBO();

		$this->cat_id = $cat_id;
		
		$top_level_cat_id = $this->getTopLevelCatID($cat_id);
		
		if( $top_level_cat_id === null ) {
			return;
		}
		
		# Check to see if listing has association
		$db->setQuery( 'SELECT * FROM #__mt_cats where cat_id = '.$top_level_cat_id.' LIMIT 1');
		$top_level_cat = $db->loadObject();

		if( 
			isset($top_level_cat) 
			&& 
			isset($this->fields[$this->pointer])
			&&
			$top_level_cat->cat_association > 0 
		)
		{
			$link_id = $this->fields[$this->pointer]['linkId'];
			
			// Get the name/caption of the associated category.
			$db->setQuery( 'SELECT cat_id, cat_name FROM #__mt_cats where cat_id = '.$top_level_cat->cat_association.' LIMIT 1');
			$assoc_cat = $db->loadObject();

			// Now get the associated listings name.
			$db->setQuery( 
				'SELECT DISTINCT link_id2, l.link_id, l.link_name FROM #__mt_links_associations AS links_assoc '
			.	"\n LEFT JOIN #__mt_links AS l ON links_assoc.link_id1 = l.link_id "
			.	"\n WHERE links_assoc.link_id2 = " . $link_id
				);
			$links_assoc = $db->loadObjectList('link_id2');

			$this->setAssocLink(
				array(
					'cat_name'	=> $assoc_cat->cat_name,
					'cat_id'	=> $assoc_cat->cat_id,
					'link_id'	=> (isset($links_assoc[$link_id]->link_id))?$links_assoc[$link_id]->link_id:null,
					'link_name'	=> (isset($links_assoc[$link_id]->link_name))?$links_assoc[$link_id]->link_name:null
				)
			);
		}	
	}
	
	/**
	 * Find the top level category (if it itself is not one)
	 *
	 * @param	int	Category ID
	 * @return	int	Top level category's ID. null if none is found.
	 */
	function getTopLevelCatID($cat_id) {
		$db = JFactory::getDBO();
		
		if( $cat_id <= 0 ) {
			return null;
		}
		
		$cat = new mtCats( $db );
		$cat->load( $cat_id );

		if( $cat->cat_parent == 0 )
		{
			return $cat->cat_id;
		}
		else
		{
			// Get the top most level category
			$db->setQuery("SELECT cat_id FROM #__mt_cats "
			.	"\nWHERE lft < " . $cat->lft . " && rgt > " . $cat->rgt . " && cat_parent >= 0"
			.	"\nORDER BY lft ASC LIMIT 1");
			$top_level_cat_id = $db->loadResult();

			if( !empty($top_level_cat_id) )
			{
				return $top_level_cat_id;
			} else {
				return null;
			}
		}
	}
	
	function loadSearchParams( $post=null ) {
		$searchParams = array();
		
		if( is_null($post) ) {
			$post = $_POST;
		}
		
		$this->resetPointer();
		while( $this->hasNext() ) {
			$field = $this->getField();
			$searchFields = $field->getSearchFields();
			foreach( $searchFields AS $searchField ) {
				if( isset($post[$searchField]) && $post[$searchField] != '') {
					$searchParams[$searchField] = $post[$searchField];
					if( count($searchFields) > 1 )
					{
						$this->fields[$this->pointer]['searchValue'][$searchField] = $post[$searchField];
						// setSearchValue needs to be invoked here to store the search value as cookies
						$field->setSearchValue($post[$searchField], $searchField);
					}
					elseif( 
						$post[$searchField][0] != '' 
						||
						// Special handling for Category field type
						$field->getName() == $post['cfcat']
					)
					{
						$this->fields[$this->pointer]['searchValue'] = $post[$searchField];
						// setSearchValue needs to be invoked here to store the search value as cookies
						$field->setSearchValue($post[$searchField]);
					}
					else
					{
						unset($searchParams[$searchField]);
					}
				}
			}
			$this->next();
		}

		return $searchParams;
	}
	
	function hasNext() {
		if( count($this->fields) > 0 && array_key_exists($this->pointer,$this->fields) ) {
			return true;
		} else {
			return false;
		}
	}
	
	function resetPointer() { $this->pointer = 0; }
	
	function getCurrentPointer() { return $this->pointer; }
	
	function getTotal() { return count($this->fields); }
	
	function getField() {
		$class = $this->getFieldTypeClassName( $this->fields[$this->pointer] );
		$fieldTypeInstance = new $class( $this->fields[$this->pointer] );
		$fieldTypeInstance->setFields($this);
		return $fieldTypeInstance;
	}
	
	function getFieldById( $id ) {
		if( !is_null($this->fields) ) {
			foreach( $this->fields AS $key => $data ) {
				if($data['id'] == $id) {
					$class = $this->getFieldTypeClassName( $data );
					$fieldTypeInstance = new $class( $data );
					$fieldTypeInstance->setFields($this);
					return $fieldTypeInstance;
				}
			}
			return null;
		}
		$class = $this->getFieldTypeClassName();
		$fieldTypeInstance = new $class();
		$fieldTypeInstance->setFields($this);
		return $fieldTypeInstance;
	}

	function getFieldByCaption( $caption ) {
		if( !is_null($this->fields) ) {
			foreach( $this->fields AS $key => $data ) {
				if($data['caption'] == $caption) {
					$class = $this->getFieldTypeClassName( $data );
					$fieldTypeInstance = new $class( $data );
					$fieldTypeInstance->setFields($this);
					return $fieldTypeInstance;
				}
			}
		}
		$class = $this->getFieldTypeClassName();
		$fieldTypeInstance = new $class();
		$fieldTypeInstance->setFields($this);
		return $fieldTypeInstance;
	}
	
	function next() { $this->pointer++; }
	
	function getFieldTypeClassName( $data=array() ) {
		global $mtconf;
		if(class_exists('mFieldType_' . $data['fieldType'])) {
			$class = 'mFieldType_' . $data['fieldType'];
		} else {
			$fieldtype_file = JPATH_ROOT . $mtconf->get('relative_path_to_fieldtypes') . $data['fieldType'] . '/'  . $data['fieldType'] . '.php';
			if( JFile::exists($fieldtype_file) )
			{
				require_once $fieldtype_file;
				$class = 'mFieldType_' . $data['fieldType'];
			} else {
				$class = 'mFieldType';
			}
		}
		return $class;
	}

}

/**
* 
* Abstract mFieldType class.
*
*/
class mFieldType {

	var $id = null;
	var $name = null;
	var $value = null;
	var $searchValue = null;
	var $size = null;
	var $arrayFieldElements = null;
	var $searchFields = null;
	var $params = null;
	var $isCore = null;
	var $numOfInputFields = 1;
	var $numOfSearchFields = 1;
	var $allowHTML = false;
	var $counter = 0;
	var $isFile = false;
	var $fields = null;
	var $linkId = null;
	var $dataValidator = null;
	
	/**
	 * An array of error messages or JExceptions objects.
	 *
	 * @var    array
	 * @since  3.0
	 */
	protected $_errors = array();
	
	/**
	 * @var         boolean  If true, the field type accepts multiple 
	 * 			 values.
	 *			 
	 * @since	3.0
	 */
	var $acceptMultipleValues = false;
	
	/**
	 * @var         boolean  If true, default value(s) are used in input 
	 *			 field when loading for new listings
	 * @since	3.0
	 */
	var $loadDefaultValue = true;

	public function __construct( $data=array() )
	{
		$this->loadLanguage();
		
		if( !is_null($data) )
		{
			foreach( $data AS $key => $value )
			{
				switch($key)
				{
					case 'fieldElements':
						$this->arrayFieldElements = explode("|",$data[$key]);
						break;
					case 'params':
						$this->params = new JRegistry( $value );
						break;
					default:
						$this->$key = $value;
						break;
				}
			}
		}
	}
	
	function loadLanguage( $class = '' ) {
		
		if( empty($class) )
		{
			$class = get_class($this);
		}

		$fieldType = substr($class,11);

		if( !empty($fieldType) )
		{
			$language = JFactory::getLanguage();
			$loaded = $language->load('fld_'.$fieldType, JPATH_SITE);
		}
		
		$parent_class = get_parent_class($class);
		
		if( $parent_class !== false && $parent_class != 'mFieldType' )
		{
			$this->loadLanguage( $parent_class );
		}
	}
	
	function setFields( $fields ) {
		$this->fields = $fields;
	}
	
	/**
	 * Return a true of false depending if the field type accepts mutliple
	 * values.
	 *
	 * @retrun	boolean	Returns true if the fieldtype accepts multiple 
	 *			values and false otherwise.
	 */
	function acceptMultipleValues()
	{
		return $this->acceptMultipleValues;
	}
	
	/**
	 * Set the search value of a field
	 *
	 * @param	string	The field's search value.
	 * @param	string	If a field contains 2 or more search fields, 
	 *			this is this where you can specify the search
	 * 			value's array index.
	 * 
	 * @return 	string	Field's search value.
	 */
	function setSearchValue( $value, $index=null )
	{
		if( !empty($value) )
		{
			if( is_null($index) )
			{
				$this->searchValue = $value;
				setcookie(
					'com_mtree_mfields_searchFieldValue_'.$this->getId(), 
					json_encode($value), 
					0
					);
			}
			else
			{
				$this->searchValue[$index] = $value;
				setcookie(
					'com_mtree_mfields_searchFieldValue_'.$this->getId().'_'.$index,
					json_encode($value), 
					0
					);
			}
		}
	}
	
	function setValue($value)
	{ 
		if( is_array($value))
		{
			$this->value = implode('|',$value);
		}
		else
		{
			$this->value = $value;
		}
		
		return $this->value;
	}
	
	function isCore() {
		if( $this->isCore == 0 ) {
			return false;
		} else {
			return true;
		}
	}
	
	function isRequired() {
		if($this->requiredField) {
			return true;
		} else {
			return false;
		}
	}
	
	function isFile() {
		if($this->isFile) {
			return true;
		} else {
			return false;
		}	
	}
	
	function inBackEnd() {
		return (substr(dirname($_SERVER['PHP_SELF']),-13) == 'administrator') ? true : false;
	}	
	
	function hasValue() { return (!empty($this->value)) ? true: false; }
	
	function hasInputField() { return ( $this->numOfInputFields <= 0 ) ? false:true; }

	function hasSearchField() { return ( $this->numOfSearchFields <= 0 ) ? false:true; }
	
	function hasFilterField() { return ( $this->getFilterHTML() === null ) ? false:true; }
	
	function hasCaption() { return (!empty($this->caption) && $this->hideCaption == 0) ? true: false; }
	
	function hasJSValidation() {
		if($this->getJSValidationFunction() != '' && !is_null($this->getJSValidationFunction())) {
			return true;
		} else {
			return false;
		}
	}
	
	function hasJSPresubmit() {
		if($this->getJSPresubmitFunction() != '' && !is_null($this->getJSPresubmitFunction())) {
			return true;
		} else {
			return false;
		}
	}
	
	function hasJSOnSave() {
		if($this->getJSOnSave() != '' && !is_null($this->getJSOnSave())) {
			return true;
		} else {
			return false;
		}
	}

	function hasJSOnInit() {
		if($this->getJSOnInit() != '' && !is_null($this->getJSOnInit())) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Method to test if a provided value is valid
	 *
	 * @param	string		$value	The value passed to the field.
	 *
	 * @return	boolean		true if the value is valid, false otherwise.
	 *
	 * @since 3.0
	 */
	function validateValue( $value )
	{
		return true;
	}
	
	/**
	 * Method to convert a user given value to one that is suitable for 
	 * storage
	 *
	 * @param	string		$value		The value passed to the field.
	 *
	 * @return	string		Returns the parsed value. Return an empty 
	 * 				string if you do not wish to store the value.
	 *
	 * @since 3.0
	 */
	function parseValue( $value )
	{ 
		if ( is_array($value) )
		{
			return ($this->allowHTML) ? implode("|",$value) : strip_tags(implode("|",$value));
		}
		else
		{
			$value = trim($value);
			return ($this->allowHTML) ? $value : strip_tags($value);
		}
	}

	/**
	 * Add an error message.
	 *
	 * @param   string  $error  Error message.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function setError($error)
	{
		array_push($this->_errors, $error);
	}

	/**
	 * Return all errors, if any.
	 *
	 * @return  array  Array of error messages or JErrors.
	 *
	 * @since   3.0
	 */
	public function getErrors()
	{
		return $this->_errors;
	}

	/**
	 * Get the most recent error message.
	 *
	 * @param   integer  $i         Option error index.
	 * @param   boolean  $toString  Indicates if JError objects should return their error message.
	 *
	 * @return  mixed   Error message or false if no error.
	 *
	 * @since   11.1
	 */
	public function getError($i = null, $toString = true)
	{
		// Find the error
		if ($i === null)
		{
			// Default, return the last message
			$error = end($this->_errors);
		}
		elseif (!array_key_exists($i, $this->_errors))
		{
			// If $i has been specified but does not exist, return false
			return false;
		}
		else
		{
			$error = $this->_errors[$i];
		}

		// Check if only the string is requested
		if ($error instanceof Exception && $toString)
		{
			return (string) $error;
		}

		return $error;
	}

	function getAssocLink( $key=null ) {
		if( !isset($key) && !empty($this->fields->assocLink) )
		{
			return $this->fields->assocLink;
		}
		elseif( isset( $this->fields->assocLink[$key] ) )
		{
			return $this->fields->assocLink[$key];
		}
		else
		{
			return false;
		}
	}
	
	function getId() { return $this->id; }
	
	function getLinkId() { return $this->linkId; }

	function getCatId() { return $this->catId; }

	function getCatName() { return $this->catName; }

	function getDirectoryId() { return $this->topLevelCatId; }

	function getDirectoryName() { return $this->topLevelCatName; }

	function getFieldType() { return $this->fieldType; }

	function getFieldTypeClassName() { return strtolower(get_class($this)); }
	
	function getDefaultValue($arg=null)
	{
		if( $this->acceptMultipleValues )
		{
			$defaultValue = explode("|",$this->defaultValue);

			if(is_null($arg))
			{
				return $defaultValue;
			}
			elseif(is_numeric($arg))
			{
				if(array_key_exists(($arg-1),$defaultValue)) {
					return trim($defaultValue[($arg-1)]);
				} else {
					return '';
				}
			} else {
				return '';
			}
		}
		else
		{
			if( empty($this->defaultValue) )
			{
				return '';
			}
			else
			{
				return $this->defaultValue;
			}
		}
	}

	/**
	 * Method to return the data for the value attribute of custom field's
	 * input element. If it is for new listing and has a default value 
	 * define, a default value will be returned. Other the stored value
	 * will be returned.
	 *
	 * @return 	string	The data for the value attribute of custom 
	 * 			field's input element.
	 *
	 * @since	3.0
	 */
	function getInputValue($arg=null)
	{
		if( $this->useDefaultValue() )
		{
			return $this->getDefaultValue($arg);
		}
		else
		{
			return $this->getValue($arg);
		}
	}
	
	function getValue($arg=null)
	{ 
		if(is_null($arg))
		{
			if( $this->acceptMultipleValues && !is_array($this->value))
			{
				return explode('|',$this->value);
			}
			else
			{
				return $this->value;
			}
		}
		elseif(is_numeric($arg))
		{
			$values = explode('|',$this->value);
			
			if(array_key_exists(($arg-1),$values))
			{
				return trim($values[($arg-1)]);
			}
			else
			{
				return '';
			}
		}
		else
		{
			return '';
		}
	}
	
	function hasSearchValue() {
		if( !empty($this->searchValue) ) {
			return true;
		} else {
			return false;
		}
	}
	
	function getSearchValue() {
		if( $this->hasSearchValue() ) {
			return $this->searchValue;
			
		// Check if cookie contains search value information
		} else {
			$numOfSearchFields = $this->getNumOfSearchFields();
			if( $numOfSearchFields == 1 )
			{
				$searchValueCookie = JFactory::getApplication()->input->get( 
					'com_mtree_mfields_searchFieldValue_'.$this->getId(),
					'', 
					'COOKIE'
					);

				if( !empty($searchValueCookie) )
				{
					return json_decode($searchValueCookie);
				} else {
					return false;
				}
			}
			elseif( $numOfSearchFields > 1 )
			{
				$returnSearchValue = array();
				for($i = 1; $i <= $numOfSearchFields; $i++)
				{
					$returnSearchValue[$this->getSearchFieldName($i)] = json_decode(
						JFactory::getApplication()->input->get( 								
							'com_mtree_mfields_searchFieldValue_'.$this->getId().'_'.$this->getSearchFieldName($i),
							'', 
							'COOKIE'
						)
					);
				}
				return $returnSearchValue;
			}
			else
			{
				return false;
			}
		}
	}
	
	function getSize() { return $this->size; }

	function getFieldElements() { return $this->fieldElements; }
	
	function getArrayFieldElements() { return $this->arrayFieldElements; }
	
	function getInputHTML()
	{
		$value = $this->getInputValue();
		
		if( !empty($this->arrayFieldElements[0]) )
		{
			$html = '<select'.($this->isRequired() ? ' required':'').' name="' . $this->getInputFieldName(1) . '" id="' . $this->getInputFieldID(1) . '">';
			$html .= '<option value="">&nbsp;</option>';
			foreach($this->arrayFieldElements AS $fieldElement) {
				$html .= '<option value="'.htmlspecialchars($fieldElement).'"';
				if( $fieldElement == $value )
				{
					$html .= ' selected';
				}
				$html .= '>' . $fieldElement . '</option>';
			}
			$html .= '</select>';
			return $html;
		}
		else
		{
			$html = '<input'
				. ($this->isRequired() ? ' required':'')
				. $this->getDataValidatorAttr()
				. ' class="'.($this->isRequired() ? ' required':'')
				. '" type="text" name="' . $this->getInputFieldName(1)
				. '" id="' . $this->getInputFieldID(1)
				. '" size="' . ($this->getSize()?$this->getSize():'30');
			$html .= '" value="' . htmlspecialchars($this->getInputValue()) ;
			$html .= '" />';
			return $html;
		}
	}

	function getSearchHTML( $showSearchValue=false, $showPlaceholder=false, $idprefix='search_' ) {
		$html = '';
		$searchValue = $this->getSearchValue();
		
		if( !empty($this->arrayFieldElements[0]) )
		{
			$html .= '<select name="' . $this->getName() . '" >';
			$html .= '<option value="">&nbsp;</option>';
			foreach($this->arrayFieldElements AS $fieldElement) {
				$html .= '<option value="'.htmlspecialchars($fieldElement).'"';
				if( $showSearchValue && $fieldElement == $searchValue ) {
					$html .= ' selected=selected';
				}
				$html .= '>' . $fieldElement . '</option>';
			}
			$html .= '</select>';
			return $html;
		} else {
			$html .= '<input type="text" name="' . $this->getName();
			$html .= '" id="'.$idprefix.$this->getInputFieldID(1);
			$html .= '" size="' . $this->getSize();

			if( $showSearchValue && $this->getSearchValue() !== false ) {
				$html .= '" value="'.$this->getSearchValue();
			}

			if( $showPlaceholder && $this->getPlaceholderText() !== false ) {
				$html .= '" placeholder="'.htmlspecialchars($this->getPlaceholderText());
			}

			$html .= '" />';
			return $html;
		}
	}

	function getHiddenHTML() {
		$html = '';
		$searchValue = $this->getSearchValue();

		if( $this->hasSearchValue() )
		{
			if( is_array($searchValue) ) {
				foreach($searchValue AS $value)
				{
					$html .= '<input';
					$html .= ' type="hidden"';
					$html .= ' name="'.$this->getName().'[]"';
					$html .= ' value="'.$value.'"';
					$html .= ' />';
				}
			} else {
				$html .= '<input';
				$html .= ' type="hidden"';
				$html .= ' name="'.$this->getName().'"';
				$html .= ' value="'.$this->getSearchValue().'"';
				$html .= ' />';
			}
			return $html;
		} else {
			return null;
		}
	}
	
	function getJSOnSave() {
		return null;
	}
	
	function getJSOnInit() {
		return null;
	}
	
	function getFilterHTML() {
		return $this->getSearchHTML( true, true, $idprefix='filter_' );
	}

	function getDataValidatorAttr() {
		if($this->hasDataValidator()) {
			return ' data-validators="'.$this->getDataValidatorName().'"';
		} else {
			return '';
		}
	}
	
	function getDataValidatorName() {
		return $this->dataValidator;
	}
	
	function hasDataValidator() {
		if( !is_null($this->dataValidator) ) {
			return true;
		} else {
			return false;
		}
	}
	
	function getJSValidation() {
		if( $this->hasJSValidation() ) {
			$js = '{';
			$js .= 'execute:'.$this->getJSValidationFunction();
			$js .= ',';
			$js .= 'message:"'.$this->getJSValidationMessage().'"';
			$js .= ',';
			$js .= 'caption:"'.htmlspecialchars($this->getCaption(true)).'"';
			$js .= '}';
			return $js;
		} else {
			return null;
		}
	}
	
	function getJSValidationFunction() {
		return null;
	}

	function getJSPresubmitFunction() {
		return null;
	}

	function getJSValidationMessage() {
		return '';
	}
	
	function getName() { 
		if( empty($this->name) ) {
			return 'cf' . $this->id;
		} else {
			return $this->name; 
		}
	}
	
	function getCaption($forceShow=false) {
		if( empty($this->caption) || ($this->hideCaption && !$forceShow) ) {
			return false;
		} else {
			return $this->caption;
		}
	}
	
	function getFieldTypeAttachmentURL($arg) {
		global $mtconf;
		return JUri::root() . $mtconf->get('relative_path_to_fieldtypes_media') . $this->fieldType . '/' . $arg;
		// return JUri::root() . $mtconf->get('relative_path_to_fieldtypes_media') . '/' . $this->fieldType . '/' . $arg;
	}

	function getDataAttachmentURL() {
		return JRoute::_( JUri::root().str_replace('&','&amp;','index.php?option=com_mtree&task=att_download&link_id=' . $this->getLinkId() . '&cf_id=' . $this->getId()) );
	}
	
	function getModPrefixText() {
		if( empty($this->prefixTextMod) ) {
			return false;
		} else {
			$html = '<span class="prefix">' . $this->prefixTextMod . '</span>';
			return $this->prefixTextMod;
		}
	}
	
	function getModSuffixText() {
		if( empty($this->suffixTextMod) ) {
			return false;
		} else {
			$html = '<span class="suffix">' . $this->suffixTextMod . '</span>';
			return $html;
		}
	}
	
	function getDisplayPrefixText() {
		if( empty($this->prefixTextDisplay) ) {
			return false;
		} else {
			$html = '<span class="prefix">' . $this->prefixTextDisplay . '</span>';
			return $html;
		}
	}
	
	function getDisplaySuffixText() {
		if( empty($this->suffixTextDisplay) ) {
			return false;
		} else {
			$html = '<span class="suffix">' . $this->suffixTextDisplay . '</span>';
			return $html;
		}
	}
	
	function getPlaceholderText() {
		if( empty($this->placeholderText) ) {
			return false;
		} else {
			return $this->placeholderText;
		}
	}
	
	function getParam( $key, $default='' ) {
		return $this->params->get( $key, $default );
	}
	
	function getNumOfSearchFields()
	{
		return $this->numOfSearchFields;
	}
	
	/**
	* Get the search fields' names
	*
	* @access public
	* @return array
	*/
	function getSearchFields() {
		$arrFields = array();
		for($i=1;$i<=$this->numOfSearchFields;$i++) {
			$arrFields[] = $this->getSearchFieldName($i);
		}
		return $arrFields;
	}
	
	function getSearchFieldName($count=1) {
		if($count == 1) {
			return $this->getName();
		} elseif( $count <= $this->numOfSearchFields ) {
			return $this->getName() . '_' . $count;
		}		
	}	
	
	function getInputFieldName($count=1) {
		if($count == 1) {
			return $this->getName();
		} elseif( $count <= $this->numOfInputFields ) {
			return $this->getName() . '_' . $count;
		}
	}
	
	function getInputFieldID($count=1) {
		if($count == 1) {
			return 'cf' . $this->id;
		} elseif( $count <= $this->numOfInputFields ) {
			// return $this->getName() . '_' . $count;
			return 'cf' . $this->id . '_' . $count;
		}
	}
	
	function getKeepFileName() {
		return 'keep_' . $this->getInputFieldName(1);
	}
	
	function getKeepFileCheckboxHTML($hasAttachment=1) {
		return '<input type="checkbox" name="' . $this->getKeepFileName() . '" value="' . $hasAttachment . '" id="' . $this->getKeepFileName() . '" checked />';
	}
	
	/**
	* Return the formatted output
	* @param int Type of output to return. Especially useful when you need to display expanded 
	*			 information in detailed view and use can use this display a summarized version
	*			 for summary view. $view = 1 for Normal/Details View. $view = 2 for Summary View.
	* @return str The formatted value of the field
	*/
	function getOutput() {
		if( $this->tagSearch && $this->hasValue() )
		{
			$arrTags = explode(',',$this->getValue());
			$countTags = count($arrTags);

			for($i=0;$i<$countTags;$i++)
			{
				$arrTags[$i] = trim($arrTags[$i]);

				$outputTags[$i] = '';
				$outputTags[$i] .= '<a class="tag" rel="tag" href="'.JRoute::_('index.php?option=com_mtree&task=searchby&cf_id='.$this->getId().'&value='.urlencode($arrTags[$i])).'">';
				$outputTags[$i] .= $arrTags[$i];
				$outputTags[$i] .= '</a>';
			}
			$html = '';
			$html .= implode(',&nbsp;',$outputTags);
			return $html;
		} else {
			return $this->getValue();
		}
	}
	
	function getWhereCondition() {
		if( func_num_args() == 0 ) {
			return null;
		} else {
			if( $this->isCore() ) {
				return $this->getName() . ' LIKE \'%' . JFactory::getDBO()->escape( func_get_arg(0), true ) . '%\'';
			} else {
				return '(cfv#.value LIKE \'%' . JFactory::getDBO()->escape( func_get_arg(0), true ) . '%\')';
			}
		}
	}
	
	/*
	 * Utility Functions
	 */
	
	function stripTags($value, $allowedTags='u,b,i,a,ul,li,pre,br,blockquote') {
		if(!empty($allowedTags)) {
			$tmp = explode(',',$allowedTags);
			array_walk($tmp,'trim');
			$allowedTags = '<' . implode('><',$tmp) . '>';
		} else {
			$allowedTags = '';
		}
		return strip_tags( $value, $allowedTags );
	}
	
	function parseMambots( &$html )
	{
		$params = new JRegistry( '' );
		$link = new stdclass;
		$link->text = $html;
		$link->id = 1;
		$link->title = '';
		$page = 0;

		JPluginHelper::importPlugin('content');
		$dispatcher = JDispatcher::getInstance();
		$results = $dispatcher->trigger('onContentPrepare', array ('com_mtree.field', &$link, & $params, 0));

		$html = $link->text;			

		return true;
	}
	
	function linkcreator( $matches ) {	
		$url = 'http://';
		$append = '';

		if ( in_array(substr($matches[1],-1), array('.',')')) ) {
			$url .= substr($matches[1], 0, -1);
			$append = substr($matches[1],-1);

		# Prevent cutting off breaks <br />
		} elseif( substr($matches[1],-3) == '<br' ) {
			$url .= substr($matches[1], 0, -3);
			$append = substr($matches[1],-3);

		} elseif( substr($matches[1],-1) == '>' ) {
			$regex = '/<(.*?)>/i';
			preg_match( $regex, $matches[1], $tags );
			if( !empty($tags[1]) ) {
				$append = '<'.$tags[1].'>';
				$url .= $matches[1];
				$url = str_replace( $append, '', $url );
			}
		} else {
			$url .= $matches[1];
		}

		return '<a href="'.$url.'" target="_blank">'.$url.'</a>'.$append.' ';
	}

	function strlen_utf8($str) {
		return strlen(utf8_decode($this->utf8_html_entity_decode($str)));
	}

	function utf8_replaceEntity($result){
		$value = intval($result[1]);
		$string = '';
		$len = round(pow($value,1/8));
		for($i=$len;$i>0;$i--){
		    $part = ($value AND (255>>2)) | pow(2,7);
		    if ( $i == 1 ) $part |= 255<<(8-$len);
		    $string = chr($part) . $string;
		    $value >>= 6;
		}
		return $string;
	}

	function utf8_html_entity_decode($string) {
		return preg_replace_callback('/&#([0-9]+);/u',array($this,'utf8_replaceEntity'),$string);
	}

	function html_cutstr($str, $len) {
		if (!preg_match('/\&#[0-9]*;.*/i', $str)) {
			return substr($str,0,$len);
		}
		$chars = 0;
		$start = 0;
		for($i=0; $i < strlen($str); $i++) {
			if ($chars >= $len) {
				break;
			}
		    $str_tmp = substr($str, $start, $i-$start);
		    if (preg_match('/\&#[0-9]*;.*/i', $str_tmp)) {
				$chars++;
		        $start = $i;
		    }
		}
		$rVal = substr($str, 0, $start);
		if (strlen($str) > $start)
		return $rVal;
	}
	function html_substr($str, $start, $length = NULL) {
		if ($length === 0) return '';

		//check if we can simply use the built-in functions
		if (strpos($str, '&') === false) {
			if ($length === NULL) return substr($str, $start);
			else return substr($str, $start, $length);
		}

		// create our array of characters and html entities
		$chars = preg_split('/(&[^;\s]+;)|/', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE);
		$html_length = count($chars);

		// check if we can predict the return value and save some processing time
		if (
		     ($html_length === 0) /* input string was empty */ or
		     ($start >= $html_length) /* $start is longer than the input string */ or
		     (isset($length) and ($length <= -$html_length)) /* all characters would be omitted */
		) {
		  return '';
		}

		//calculate start position
		if ($start >= 0) {
			$real_start = $chars[$start][1];
		} else { //start'th character from the end of string
			$start = max($start,-$html_length);
			$real_start = $chars[$html_length+$start][1];
		}

		if (!isset($length)) {
			// no $length argument passed, return all remaining characters
			return substr($str, $real_start);
		} else if ($length > 0) {
			// copy $length chars
			if ($start+$length >= $html_length) {
				// return all remaining characters
				return substr($str, $real_start);
			} else {
				//return $length characters
				return substr($str, $real_start, $chars[max($start,0)+$length][1] - $real_start);
			}
		} else { //negative $length. Omit $length characters from end
			return substr($str, $real_start, $chars[$html_length+$length][1] - $real_start);
		}

	}
	
	function html_strlen($str) {
		$chars = preg_split('/(&[^;\s]+;)|/', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		return count($chars);
	}
	
	/**
	 * Method to compute and return whether to use default values. By 
	 * default, when $loadDefaultValue is true and listing is new, it
	 * return true. Otherwise false.
	 *
	 * @return 	boolean	True if we are loading on a new listing and 
	 * 		    	$loadDefaultValue is true.
	 *
	 * @since	3.0
	 */
	function useDefaultValue()
	{
		if( $this->getLinkId() == 0 && $this->getLoadDefaultValue() && !$this->hasValue() )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function getLoadDefaultValue()
	{
		return $this->loadDefaultValue;
	}
	
	function setLoadDefaultValue($toggle)
	{
		if( $toggle )
		{
			$this->loadDefaultValue = true;
		}
		else
		{
			$this->loadDefaultValue = false;
		}
	}
}

class mFieldType_text extends mFieldType {
}

class mFieldType_multitext extends mFieldType
{
	function getInputHTML()
	{
		$html = '';
		$html .= '<textarea'.($this->isRequired() ? ' required':'');
		$html .= ' name="' . $this->getInputFieldName(1) . '"';
		$html .= ' id="' . $this->getInputFieldID(1) . '"';
		$html .= ' cols="60"';
		$html .= ' rows="'.$this->getSize().'">';
		$html .=  htmlspecialchars($this->getInputValue());
		$html .= '</textarea>';
		return $html;
	}
	
	function getSearchHTML( $showSearchValue=false, $showPlaceholder=false, $idprefix='search_' )
	{
		$html = '';
		$html .= '<input type="text" name="' . $this->getName();
		$html .= '" id="'.$idprefix.$this->getName();
		$html .= '" size=30"';

		if( $showSearchValue && $this->getSearchValue() !== false ) {
			$html .= '" value="'.$this->getSearchValue();
		}

		if( $showPlaceholder && $this->getPlaceholderText() !== false ) {
			$html .= '" placeholder="'.htmlspecialchars($this->getPlaceholderText());
		}

		$html .= '" />';
		return $html;
	}
}

class mFieldType_weblink extends mFieldType {
	var $dataValidator = 'validate-url';
	
	function getInputHTML()
	{
		$showGo = $this->getParam('showGo',0);
		$html = '';
		$html .= '<input type="url"'
			. ' name="' . $this->getInputFieldName(1) . '"'
			. ($this->isRequired() ? ' required':'')
			. $this->getDataValidatorAttr()
			. ' id="' . $this->getInputFieldID(1) . '"'
			. ' size="' . ($this->getSize()?$this->getSize():'30') . '"';
		$html .= ' value="' . htmlspecialchars($this->getInputValue()) . '"';
		$html .= ' />';
		if($showGo && $this->inBackEnd())
		{
			$html .= '&nbsp;';
			$html .= '<button type="button" class="btn" onclick=\'';
			$html .= 'javascript:window.open("index.php?option=com_mtree&task=openurl&url="+escape(document.getElementById("' . $this->getInputFieldID(1) . '").value))\'>';
			$html .= '<i class="icon-out-2"></i> ';
			$html .= JText::_( 'FLD_WEBLINK_GO' );
			$html .= '</button>';
		}
		return $html;
	}

	function parseValue($value) {
		$value = trim(strip_tags($value));
		if(substr($value,0,7) == 'http://' || substr($value,0,8) == 'https://') {
			return $value;
		} elseif(!empty($value)) {
			return 'http://' . $value;
		} else {
			return '';
		}
	}
	
	function getJSValidation() {
		$js = '{';
		$js .= 'execute:'.$this->getJSValidationFunction();
		$js .= ',';
		$js .= 'message:"'.$this->getJSValidationMessage().'"';
		$js .= ',';
		$js .= 'caption:"'.$this->getCaption(true).'"';
		$js .= '}';
		return $js;
	}	
	
	function getJSValidationFunction() {
		return 'function(){return /^(http:\/\/|https:\/\/)?([a-zA-Z0-9_]+\.[a-zA-Z0-9\-]+|[a-zA-Z0-9\-]+)\.[a-zA-Z\.]{2,6}(\/[a-zA-Z0-9\.\?=\/#%&\+-]+|\/|)/i.test(arguments[0].value)}';
	}

	function getJSValidationMessage() {
		return JText::_( 'FLD_WEBLINK_PLEASE_ENTER_A_VALID_URL' );
	}
	
	function getOutput() {
		$maxUrlLength		= $this->getParam('maxUrlLength',60);
		$text			= $this->getParam('text','');
		$openNewWindow		= $this->getParam('openNewWindow',1);
		$hideProtocolOutput	= $this->getParam('hideProtocolOutput',1);
		$showCounter 		= $this->getParam('showCounter',1);
		
		$html = '';
		$html .= '<a href="';
		$html .= $this->getOutputURL();
		$html .= '"';
		if( $openNewWindow == 1 ) {
			$html .= ' target="_blank"';
		}
		$html .= '>';
		if(!empty($text)) {
			$html .= $text;
		} else {
			$value = $this->getValue();
			if(strpos($value,'://') !== false && $hideProtocolOutput) {
				$value = trim(substr($value,(strpos($value,'://')+3)));

				// If $value has a single slash and this is at the end of the string, we can safely remove this.
				if( substr($value,-1) == '/' && substr_count($value,'/') == 1 )
				{
					$value = substr($value,0,-1);
				}
			}
			if( empty($maxUrlLength) || $maxUrlLength == 0 ) {
				$html .= $value;
			} else {
				$html .= substr($value,0,$maxUrlLength);
				if( strlen($value) > $maxUrlLength ) {
					$html .= $this->getParam('clippedSymbol');
				}
			}
		}
		$html .= '</a>';
		
		if( $showCounter )
		{
			$html .= '<span class="counter">('.JText::sprintf('FLD_WEBLINK_NUMBER_OF_VISITS', $this->counter).')</span>';
		}
		
		return $html;
	}
	
	function getOutputURL() {
		$useInternalRedirect = $this->getParam('useInternalRedirect',0);

		$url = '';
		
		if( $useInternalRedirect ) {
			$url .= JRoute::_( 
				'index.php?option=com_mtree&task=visit&link_id=' . $this->getLinkId() . '&cf_id=' . $this->getId() 
				);
		} else {
			// parseValue always make sure the protocol bits is always prepended before storing to database.
			// We are going to do another check here, just in case the value is stored without going through
			// the check.
			$url .= $this->parseValue($this->getValue());
		}
		return $url;
	}
}

class mFieldType_selectlist extends mFieldType
{
	function getInputHTML()
	{
		$value = $this->getInputValue();
		
		$html = '<select'.($this->isRequired() ? ' required':'');
		$html .= ' name="' . $this->getInputFieldName(1) . '"';
		$html .= ' id="' . $this->getInputFieldID(1) . '"';
		$html .= '>';
		$html .= '<option value="">&nbsp;</option>';
		foreach($this->arrayFieldElements AS $fieldElement) {
			$html .= '<option value="'.htmlspecialchars($fieldElement).'"';
			if( $fieldElement == $value )
			{
				$html .= ' selected';
			}
			$html .= '>' . $fieldElement . '</option>';			
		}
		$html .= '</select>';
		return $html;
	}

	function getWhereCondition() {
		if( func_num_args() == 0 ) {
			return null;
		} else {
			if( $this->isCore() ) {
				return $this->getName() . ' = \'' . JFactory::getDBO()->escape( func_get_arg(0), true ) . '\'';
			} else {
				return '(cfv#.value = \'' . JFactory::getDBO()->escape( func_get_arg(0), true ) . '\')';
			}
		}
	}
}

class mFieldType_selectmultiple extends mFieldType
{
	var $acceptMultipleValues = true;
	
	function getInputHTML()
	{
		$selectmultiple_values = $this->getInputValue();

		$html = '<select'.($this->isRequired() ? ' required':'').' name="' . $this->getInputFieldName(1) . '[]" id="' . $this->getInputFieldID(1) . '"';
		$html .= ' multiple size="'.($this->getSize() +1).'"';
		$html .= '>';
		$html .= '<option value="">&nbsp;</option>';

		foreach($this->arrayFieldElements AS $fieldElement) {
			$html .= '<option value="'.htmlspecialchars($fieldElement).'"';
			if(
				(
					!empty($selectmultiple_values)
					&& 
					in_array($fieldElement,$selectmultiple_values)
				)
			) {
				$html .= ' selected';
			}
			$html .= '>' . $fieldElement . '</option>';
		}
		$html .= '</select>';
		return $html;
	}
	
	function getSearchHTML( $showSearchValue=false, $showPlaceholder=false, $idprefix='search_' ) {
		$html = '';
		$searchValue = $this->getSearchValue();
		
		$html .= '<select name="' . $this->getName() . '[]"';
		$html .= ' multiple size="'.($this->getSize() +1).'"';
		$html .= '>';
		$html .= '<option value="">&nbsp;</option>';
		foreach($this->arrayFieldElements AS $fieldElement) {
			$html .= '<option value="'.htmlspecialchars($fieldElement).'"';
			if( $showSearchValue && $searchValue !== false && in_array($fieldElement,$searchValue) ) {
				$html .= ' selected=selected';
			}
			$html .= '>' . $fieldElement . '</option>';
		}
		$html .= '</select>';
		return $html;
	}
	
	function getWhereCondition() {
		$args = func_get_arg(0);
		$return = '(';
		
		if( is_array($args) ) {
			foreach( $args AS $arg ) {
				$where[] = 'cfv#.value LIKE \'%' . JFactory::getDBO()->escape( $arg, true ) . '%\'';
			}
		}
		if( count($where) > 1 ) {
			$return .= '((' . implode(') AND (',$where) . '))';
			$return .= ')';
			return $return;
		} else {
			$return .= $where[0] . ')';
			return $return;
		}
	}
	
	function getOutput() {
		$arrayValue = explode('|',$this->value);

		if( $this->tagSearch && $this->hasValue() )
		{
			$countTags = count($arrayValue);
			for($i=0;$i<$countTags;$i++)
			{
				$arrTags[$i] = trim($arrayValue[$i]);

				$outputTags[$i] = '';
				$outputTags[$i] .= '<a class="tag" rel="tag" href="'.JRoute::_('index.php?option=com_mtree&task=searchby&cf_id='.$this->getId().'&value='.urlencode($arrTags[$i])).'">';
				$outputTags[$i] .= $arrTags[$i];
				$outputTags[$i] .= '</a>';
			}
			$arrayValue = $outputTags;
		}
		
		$html = '<ul>';
		foreach( $arrayValue AS $value ) {
			$html .= '<li>' . $value . '</li>';
		}
		$html .= '</ul>';
		return $html;
	}
		
}

class mFieldType_radiobutton extends mFieldType {
	var $validate_one_required_added = false;
	
	function getDataValidatorAttr() {
		if($this->isRequired() && !$this->validate_one_required_added) {
			$this->validate_one_required_added = true;
			return ' data-validators="validate-one-required"';
		} else {
			return '';
		}
	}
	
	function getInputHTML()
	{
		$value = $this->getInputValue();
		$html = '';
		$i = 0;

		$html .= '<ul>';
		// $html .= '<ul style="margin:0;padding:0;list-style-type:none">';
		foreach($this->arrayFieldElements AS $fieldElement)
		{
			if(!empty($fieldElement)) {
				//$html .= '<li style="width:' . floor(100 / $this->columns) . '%;float:left;background-image:none;padding:0">';
				$html .= '<li style="background-image:none;padding:0">';
				$html .= '<label for="' . $this->getInputFieldID(1) . '_' . $i . '" class="radio">';
				$html .= '<input'
					. ($this->isRequired() ? ' required':'')
					. $this->getDataValidatorAttr()
					. ' type="radio" name="' . $this->getInputFieldName(1)
					. '" value="'.htmlspecialchars($fieldElement)
					. '" id="' . $this->getInputFieldID(1) . '_' . $i . '" ';

				if( $fieldElement == $value )
				{
					$html .= 'checked ';
				}
				
				$html .= '/>';
				$html .= $fieldElement;
				$html .= '</label>';
				$html .= '</li>';
				$i++;
			}
		}
		$html .= '</ul>';
		return $html;
	}
	
	function getSearchHTML( $showSearchValue=false, $showPlaceholder=false, $idprefix='search_' ) {
		
		$html = '';
		$i = 0;
		$html .= '<ul style="margin:0;padding:0;list-style-type:none">';
		$searchValue = $this->getSearchValue();
		
		foreach($this->arrayFieldElements AS $fieldElement) {
			if(!empty($fieldElement)) {
				$html .= '<li';
				if( $showSearchValue && $fieldElement == $searchValue ) {
					$html .= ' class="active"';
				}
				$html .= '>';
				$html .= '<label for="' . $idprefix . $this->getName() . '_' . $i . '" class="radio">';
				$html .= '<input type="radio" name="' . $this->getName();
				$html .= '" value="'.htmlspecialchars($fieldElement);
				$html .= '" id="' . $idprefix . $this->getName() . '_' . $i . '" ';
				if( $showSearchValue && $fieldElement == $searchValue ) {
					$html .= 'checked ';
				} 
				$html .= '/>';
				$html .= $fieldElement;
				$html .= '</label>';
				$html .= '</li>';
				$i++;
			}
		}
		$html .= '</ul>';
		return $html;
	}
	
	function getWhereCondition() {
		if( func_num_args() == 0 ) {
			return null;
		} else {
			return '(cfv#.value = \'' . JFactory::getDBO()->escape( func_get_arg(0), true ) . '\')';
		}
	}
}

class mFieldType_checkbox extends mFieldType {
	var $validate_one_required_added = false;
	var $acceptMultipleValues = true;
	
	function getDataValidatorAttr() {
		if($this->isRequired() && !$this->validate_one_required_added) {
			// $this->validate_one_required_added = true;
			return ' data-validators="validate-one-required"';
		} else {
			return '';
		}
	}

	function getInputHTML()
	{
		$value = $this->getInputValue();
		$i = 0;
		$html = '';

		$html .= '<ul>';
		foreach($this->arrayFieldElements AS $fieldElement) {
			// $html .= '<div style="width:' . floor(100 / $this->columns) . '%;float:left;">';
			$html .= '<li>';
			$html .= '<input'
				. ($this->isRequired() ? ' required':'')
				. $this->getDataValidatorAttr()
				. ' type="checkbox" name="' . $this->getInputFieldName(1) . '[]"'
				. ' value="'.htmlspecialchars($fieldElement)
				. '" id="' . $this->getInputFieldID(1) . '_' . $i . '" ';

			if( in_array($fieldElement,$value) )
			{
				$html .= 'checked ';
			}

			$html .= '/>';
			$html .= '<label for="' . $this->getInputFieldID(1) . '_' . $i . '">'.$fieldElement.'</label><br>';
			$html .= '</li>';
			$i++;
		}
		$html .= '</ul>';
		return $html;
	}
	
	function getSearchHTML( $showSearchValue=false, $showPlaceholder=false, $idprefix='search_' ) {
		$i = 0;
		$html = '';
		
		if( $this->getSearchValue() !== false ) {
			$checkbox_values = $this->getSearchValue();
		} else {
			$checkbox_values = array();
		}
		
		$html .= '<ul style="margin:0;padding:0;list-style-type:none">';
		foreach($this->arrayFieldElements AS $fieldElement) {
			$html .= '<li';
			if( $showSearchValue && in_array($fieldElement,$checkbox_values) ) {
				$html .= ' class="active"';
			}
			$html .= '>';
			$html .= '<input type="checkbox" name="' . $this->getName();
			$html .= '[]" value="'.htmlspecialchars($fieldElement);
			$html .= '" id="' . $idprefix . $this->getName() . '_' . $i . '" ';
			if( $showSearchValue && in_array($fieldElement,$checkbox_values) ) {
				$html .= 'checked ';
			}
			$html .= '/>';
			$html .= '<label for="' . $idprefix . $this->getName() . '_' . $i . '">';
			$html .= $fieldElement;
			$html .= '</label>';
			$html .= '</li>';
			$i++;
		}
		$html .= '</ul>';
		
		return $html;
	}

	function getWhereCondition() {
		$args = func_get_arg(0);
		$return = '(';
		$where = array();
		
		if( is_array($args) ) {
			foreach( $args AS $arg ) {
				$where[] = 'cfv#.value LIKE \'%' . JFactory::getDBO()->escape( $arg, true ) . '%\'';
			}
		}
		if( count($where) > 1 ) {
			$return .= '((' . implode(') AND (',$where) . '))';
			$return .= ')';
			return $return;
		} else {
			$return .= $where[0] . ')';
			return $return;
		}
	}
	
	function getOutput($view=1) {
		$arrayValue = explode('|',$this->value);
		$html = '';

		if( $this->tagSearch && $this->hasValue() )
		{
			$countTags = count($arrayValue);
			for($i=0;$i<$countTags;$i++)
			{
				$arrTags[$i] = trim($arrayValue[$i]);

				$outputTags[$i] = '';
				$outputTags[$i] .= '<a class="tag" rel="tag" href="'.JRoute::_('index.php?option=com_mtree&task=searchby&cf_id='.$this->getId().'&value='.urlencode($arrTags[$i])).'">';
				$outputTags[$i] .= $arrTags[$i];
				$outputTags[$i] .= '</a>';
			}
			$arrayValue = $outputTags;
		}
			
		switch($view) {
			# Details view
			case '1':
				$html .= '<ul>';
				foreach( $arrayValue AS $value ) {
					if( $value != '' ) {
						$html .= '<li>' . $value . '</li>';
					}
				}
				$html .= '</ul>';
				break;
			# Summary view
			case '2':
				$html .= implode(',',$arrayValue);
				break;
		}
		return $html;
	}
	
}

class mFieldType_file extends mFieldType {
	var $isFile = true;
	
	function parseValue( $value )
	{ 
		return $value;
	}

	function getOutput() {
		$html = '';
		$showCounter 	= $this->getParam('showCounter',1);

		if(!empty($this->value)) {
			$html .= '<a href="' . $this->getDataAttachmentURL() . '" target="_blank">';
			$html .= $this->getValue();
			$html .= '</a>';
		}
		
		if( $showCounter )
		{
			$html .= '<span class="counter">('.JText::sprintf('FLD_FILE_NUMBER_OF_VIEWS', $this->counter).')</span>';
		}

		return $html;
	}
	
	function getInputHTML()
	{
		$html = '';
		if( $this->attachment > 0 ) {
			$html .= $this->getKeepFileCheckboxHTML($this->attachment);
			$html .= '&nbsp;';
			$html .= '<a href="' . $this->getDataAttachmentURL() . '" target="_blank">';
			$html .= $this->getValue();
			$html .= '</a>';

			$showCounter = $this->getParam('showCounter',1);
			if( $showCounter ) {
				$html .= ' (' . JText::sprintf('FLD_FILE_NUMBER_OF_VIEWS', $this->counter) . ')';
			}
			
			$html .= '</br >';
		}
		$html .= '<input'.($this->isRequired() ? ' required':'');
		$html .= ' type="file" name="' . $this->getInputFieldName(1) . '"';
		$html .= ' id="' . $this->getSearchFieldName(1) . '"';
		$html .= ' />';
		return $html;
	}

	function getSearchHTML( $showSearchValue=false, $showPlaceholder=false, $idprefix='search_' ) {
		$searchValue = $this->getSearchValue();
		
		$html = '';
		$html .= '<label for="' . $idprefix . $this->getName() . '" class="checkbox">';
		$html .= '<input type="checkbox"';
		$html .= ' name="' . $this->getSearchFieldName(1) . '"';
		$html .= ' id="' . $idprefix . $this->getSearchFieldName(1) . '"';
		$html .= ' value=1';
		if( $showSearchValue && in_array($searchValue,array(1)) ) {
			$html .= ' checked';
		}
		$html .= ' />';
		$html .= JText::_( 'FLD_FILE_CONTAINS_FILE' );
		$html .= '</label>';
		return $html;
	}
	
	function getWhereCondition() {
		if( func_num_args() == 0 ) {
			return null;
		} else {
			return '(cfv#.attachment = \'1\')';
		}
	}

}

class mFieldType_number extends mFieldType {
	var $numOfSearchFields = 2;
	var $dataValidator = 'validate-integer';
	
	function getJSValidationFunction() {
		if( in_array($this->getName(),array('link_hits','link_votes','link_visited','link_rating')) ) {
			return 'function(){return(arguments[0].value != "" && /^[-]?([1-9]{1}[0-9]{0,}(\.[0-9]{0,2})?|0(\.[0-9]{0,2})?|\.[0-9]{1,2})$/i.test(arguments[0].value)==true);}'; 
		} else {
			return 'function(){return(arguments[0].value != "" && /^[-]?([1-9]{1}[0-9]{0,}(\.[0-9]{0,2})?|0(\.[0-9]{0,2})?|\.[0-9]{1,2})$/i.test(arguments[0].value)==true);}'; 
		}
	}

	function getJSValidationMessage() {
		return JText::_( 'FLD_NUMBER_PLEASE_ENTER_A_VALID_NUMBER' );
	}

	function validateValue($value)
	{
		if(is_numeric($value)) {
			return true;
		} else {
			$this->setError(JText::_( 'FLD_NUMBER_PLEASE_ENTER_A_VALID_NUMBER' ));
			return false;
		}
	}
	
	function parseValue($value) {
		if(is_numeric($value)) {
			return trim($value);
		} else {
			return '';
		}
	}

	function getSearchHTML( $showSearchValue=false, $showPlaceholder=false, $idprefix='search_' ) {
		$searchValue = $this->getSearchValue();

		$operators = array(
			''	=> '',
			'1'	=> JText::_( 'FLD_NUMBER_EXACTLY' ),
			'2'	=> JText::_( 'FLD_NUMBER_MORE_THAN' ),
			'3'	=> JText::_( 'FLD_NUMBER_LESS_THAN' )
		);
		
		$html = '<select name="' . $this->getSearchFieldName(2) . '">';
		foreach( $operators AS $key => $value ) {
			$html .= '<option value="'.$key.'"';
			if( 
				$showSearchValue 
				&& 
				isset($searchValue[$this->getSearchFieldName(2)]) 
				&& 
				$key == $searchValue[$this->getSearchFieldName(2)] 
			) {
				$html .= ' selected="selected"';
			}
			$html .= '>';
			$html .= $value;
			$html .= '</option>';
		}
		$html .= '</select>';
		$html .= ' ';
		$html .= '<input name="' . $this->getSearchFieldName(1);
		$html .= '" type="text"';
		$html .= ' size="'.$this->getSize().'"';
		if( $showSearchValue && isset($searchValue[$this->getSearchFieldName(1)]) ) {
			$html .= ' value="'.$searchValue[$this->getSearchFieldName(1)].'"';
		}
		if( $showPlaceholder ) {
			$html .= ' placeholder="'.$this->getPlaceholderText().'"';
		}
		$html .='/>';
		return $html;
	}
	
	function getWhereCondition() {
		$args = func_get_args();
		
		if( !isset($args[0]) || !isset($args[1]) )
		{
			return null;
		}
		
		if( $this->isCore() ) {
			$fieldname = $this->getName();
		} else {
			$fieldname = 'cfv#.value';
		}
		
		if( ($args[1] >= 1 || $args[1] <= 3) && is_numeric($args[0]) ) {
			switch($args[1]) {
				case 1:
					return $fieldname . ' = ' . (int) $args[0];
					break;
				case 2:
					return $fieldname . ' > ' . (int) $args[0];
					break;
				case 3:
					return $fieldname . ' < ' . (int) $args[0];
					break;
			}
		} else {
			return null;
		}
	}
}

class mFieldType_date extends mFieldType {
	var $numOfSearchFields = 4;
	
	function hasFilterField() {
		return true;
	}

	function parseValue( $value ) { 
		$exploded = explode('-',trim($value));

		if( is_numeric($exploded[0]) && is_numeric($exploded[1]) && is_numeric($exploded[2]) ) {
			return implode('-',$exploded);
		} else {
			return '';
		}
	}
	
	function getOutput() {
		$dateFormat = $this->getParam('dateFormat','%Y-%m-%d');
		
		$lang = JFactory::getLanguage();
		setlocale(LC_TIME, $lang->getLocale());

		$value = $this->getValue();
		$unixTime = mktime(0,0,0,intval(substr($value,5,2)),intval(substr($value,8,2)),intval(substr($value,0,4)));
		
		$output = '<time datetime="'.strftime('%Y-%m-%d',$unixTime).'">';
		$output .= strftime($dateFormat,$unixTime);
		$output .= '</time>';
		
		return $output;
	}

	function getSearchHTML( $showSearchValue=false, $showPlaceholder=false, $idprefix='search_' ) {

 		JHtml::_('behavior.framework');
		JHtml::_('behavior.calendar');
		
		$startYear = $this->getParam('startYear',(date('Y')-70));
		$endYear = $this->getParam('endYear',date('Y'));
		
		$searchValue = $this->getSearchValue();

		$html = '';
		$html .= '<div class="row-fluid">';
		$html .= '<label for="' . $this->getSearchFieldName(1) . 'a" class="radio">';
		$html .= '<input id="' . $this->getSearchFieldName(1) . 'a"';
		$html .= ' name=' . $this->getSearchFieldName(1);
		$html .= ' type="radio"';
		$html .= ' value="1"';
		if( $showSearchValue && isset($searchValue[$this->getSearchFieldName(1)]) && $searchValue[$this->getSearchFieldName(1)] == '1' ) {
			$html .= ' checked';
		}
		$html .= ' />';
		$html .= JText::_( 'FLD_DATE_EXACTLY_ON' );
		
		$html .= JHtml::_(
			'calendar', 
			(isset($searchValue[$this->getSearchFieldName(2)])?$searchValue[$this->getSearchFieldName(2)]:''), 
			$this->getSearchFieldName(2), 
			$this->getSearchFieldName(2), 
			'%Y-%m-%d'
			);
		$html .= '</label>';
		$html .= '</div>';

		$html .= '<div class="row-fluid">';
		$html .= '<label for="' . $this->getSearchFieldName(1) . 'b" class="radio">';
		$html .= '<input id="' . $this->getSearchFieldName(1) . 'b"';
		$html .= ' name=' . $this->getSearchFieldName(1);
		$html .= ' type="radio"';
		$html .= ' value="2"';
		if( $showSearchValue && isset($searchValue[$this->getSearchFieldName(1)]) && $searchValue[$this->getSearchFieldName(1)] == '2' ) {
			$html .= ' checked';
		}
		$html .= ' />';
		$html .= JText::_( 'FLD_DATE_BETWEEN' );
		
		$html .= JHtml::_(
			'calendar', 
			(isset($searchValue[$this->getSearchFieldName(3)])?$searchValue[$this->getSearchFieldName(3)]:''), 
			$this->getSearchFieldName(3), 
			$this->getSearchFieldName(3), 
			'%Y-%m-%d'
			);

		$html .= '&nbsp;';
		$html .= JText::_( 'FIL_DATE_AND' );
		$html .= '&nbsp;';
		
		$html .= JHtml::_(
			'calendar', 
			(isset($searchValue[$this->getSearchFieldName(4)])?$searchValue[$this->getSearchFieldName(4)]:''), 
			$this->getSearchFieldName(4), 
			$this->getSearchFieldName(4), 
			'%Y-%m-%d'
			);
		$html .= '</label>';
		$html .= '</div>';

		return $html;
	}

	function getInputHTML() {
		$startYear = $this->getParam('startYear',(date('Y')-70));
		$endYear = $this->getParam('endYear',date('Y'));
		$value = $this->getInputValue();

		$html = '';
		$html .= JHtml::_(
			'calendar', 
			$value, 
			$this->getInputFieldName(), 
			$this->getInputFieldName(), 
			'%Y-%m-%d'
			);

		return $html;
	}

	function getJSOnInit() {
		$js = null;
		if( $this->isRequired() )
		{
			$js .= 'jQuery(\'#'.$this->getInputFieldId(1).'\').attr(\'required\', true)';
		}
		return $js;
	}

	function getWhereCondition() {
		$args = func_get_args();
		if( isset($args[1]) ) $date0 = $args[1];
		if( isset($args[2]) ) $date1 = $args[2];
		if( isset($args[3]) ) $date2 = $args[3];

		if( $this->isCore() ) {
			$fieldname = $this->getName();
		} else {
			$fieldname = 'cfv#.value';
		}
		
		if($args[0] == 1) {
			if( isset($date0) ) {
				return 'STR_TO_DATE('.$fieldname.',\'%Y-%m-%d\') = STR_TO_DATE(\'' . JFactory::getDBO()->escape( $date0 ) .'\',\'%Y-%m-%d\')';
			}
		} else {
			if( isset($date1) && isset($date2) ) {
				return 'STR_TO_DATE('.$fieldname.',\'%Y-%m-%d\') >= STR_TO_DATE(\'' . JFactory::getDBO()->escape( $date1 ) .'\',\'%Y-%m-%d\')';
			} elseif( isset($date2) & !isset($date1) ) {
				return 'STR_TO_DATE('.$fieldname.',\'%Y-%m-%d\') <= STR_TO_DATE(\'' . JFactory::getDBO()->escape( $date2 ) .'\',\'%Y-%m-%d\')';
			} elseif( isset($date1) & !isset($date2) ) {
				$timestamp1 = strtotime($date2);
				$timestamp2 = strtotime($date3);
				if($timestamp1>$timestamp2) {
					$maxDate = $date1;
					$minDate = $date2;
				} else {
					$maxDate = $date2;
					$minDate = $date1;
				}
				if($maxDate == $minDate) {
					return 'STR_TO_DATE('.$fieldname.',\'%Y-%m-%d\') = STR_TO_DATE(\'' . JFactory::getDBO()->escape( $date1 ) . '\',\'%Y-%m-%d\')';
				} else {
					return '(STR_TO_DATE('.$fieldname.',\'%Y-%m-%d\') >= STR_TO_DATE(\'' . JFactory::getDBO()->escape( $minDate ) .'\',\'%Y-%m-%d\') AND STR_TO_DATE('.$fieldname.',\'%Y-%m-%d\') <= STR_TO_DATE(\'' . JFactory::getDBO()->escape( $maxDate ) .'\',\'%Y-%m-%d\'))';
				}
			} else {
				return null;
			}
		}
		return null;
	}
}

class mFieldType_email extends mFieldType {
	var $dataValidator = 'validate-email';
	
	function getJSValidationFunction() {
		return 'function(){return(/^[a-zA-Z0-9._\-\+]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z0-9.-]{2,6}$/i.test(arguments[0].value)==true)}';
	}

	function getJSValidationMessage() {
		return JText::_( 'FLD_EMAIL_PLEASE_ENTER_A_VALID_EMAIL' );
	}
	
	function validateValue( $value )
	{
		if (!preg_match("/^[a-zA-Z0-9._\-\+]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z0-9.-]{2,6}$/i", $value))
		{
			$this->setError(JText::_( 'FLD_EMAIL_PLEASE_ENTER_A_VALID_EMAIL' ));
	        	return false;
		}
		else
		{
	        	return true;
		}
	}

	function getInputHTML()
	{
		$html = '';
		$html .= '<input'.($this->isRequired() ? ' required':'');
		$html .= ' class="'.($this->isRequired() ? ' required':'');
		$html .= '" type="email" name="' . $this->getInputFieldName(1) . '"';
		$html .= ' id="' . $this->getInputFieldID(1) . '"';
		$html .= ' size="' . ($this->getSize()?$this->getSize():'30') . '"';
		$html .= ' value="' . htmlspecialchars($this->getInputValue()) . '"';
		$html .= ' />';
		
		return $html;
	}
	
	function getOutput() {
		$email = $this->getValue();
		$html = '';
		if(!empty($email)) {
			$html .= '<script type="text/javascript"><!--' . "\n" . 'document.write(\'<a hr\'+\'ef="mai\'+\'lto\'+\':\'+\'';
			for($i=0;$i<strlen($email);$i++) {
				$html .= '%'.dechex(ord(substr($email,$i,1)));
			}
			$html .= '">';
			for($j=0;$j<strlen($email);$j++){
			    $check = htmlentities($email[$j],ENT_QUOTES);
			   $html .= ($email[$j] == $check) ? "&#".ord($email[$j]).";" : $check;
			}
			$html .= '<\/a>\');' . "\n" . '//--></script>';
		}
		return $html;
	}
}

class mFieldType_tags extends mFieldType {

	function hasFilterField() {
		return true;
	}

	function getSearchHTML( $showSearchValue=false, $showPlaceholder=false, $idprefix='search_' ) {
		$i = 0;
		$html = '';
		
		if( $this->getSearchValue() !== false ) {
			$checkbox_values = (array) $this->getSearchValue();
		} else {
			$checkbox_values = array();
		}
		
		$cf_id = $this->getId();
		
		$db = JFactory::getDBO();
		$db->setQuery('SELECT REPLACE(value,\'|\',\',\') FROM #__mt_cfvalues WHERE cf_id = ' . $db->Quote($cf_id));
		$arrTags = $db->loadColumn();
		
		// Read through array of strings and return an array mapping tag with number of occurances
		$rawTags = array();
		foreach( $arrTags AS $tag )
		{
			$results = explode(',',$tag);
			$count = count($results);
	
			for($i=0;$i<$count;$i++)
			{
				$results[$i] = trim($results[$i]);
			}
	
			$rawTags = array_merge($rawTags,array_unique($results));
		}
		$rawTags = array_count_values($rawTags);
		arsort($rawTags);
		
		$html .= '<ul>';
		foreach( $rawTags AS $tag => $items )
		{
			$tags[$i]->value = $tag;
			$tags[$i]->items = $items;
			$tags[$i]->link  = JRoute::_('index.php?option=com_mtree&task=searchby&cf_id='.$cf_id.'&value='.$tag);
			$tags[$i]->elementId  = 'searchbytags-value-'.JFilterOutput::stringURLUnicodeSlug($tag);
			
			$html .= '<li>';
			$html .= '<label for="' . $this->getName() . '_' . $i . '" id="' . $tags[$i]->elementId . '" class="checkbox">';
			$html .= '<input type="checkbox" name="' . $this->getName();
			$html .= '[]" value="'.htmlspecialchars($tags[$i]->value);
			$html .= '" id="' . $this->getName() . '_' . $i . '" ';
			if( $showSearchValue && in_array($tags[$i]->value,$checkbox_values) ) {
				$html .= 'checked ';
			}
			$html .= '/>';
			$html .= $tags[$i]->value;
			$html .= '</label>';
			$html .= '</li>';
			// $html .= '<br>';
			$i++;
		}			
		$html .= '</ul>';

		return $html;
	}

	function getInputHTML()
	{
		$params['maxChars'] = intval($this->getParam('maxChars',80));
	
		$html = '';
		$html .= '<input'.($this->isRequired() ? ' required':'');
		$html .= ' type="text" name="' . $this->getInputFieldName(1) . '"';
		$html .= ' id="' . $this->getInputFieldID(1) . '"';
		$html .= ' size="' . $this->getSize() . '"';
		$html .= ' maxlength="'.$params['maxChars'].'"';
		$html .= ' value="' . htmlspecialchars($this->getInputValue()) . '"';
		$html .= ' />';

		return $html;
	}

	function getOutput() {
		if( !$this->hasValue() ) {
			return '';
		}

		$arrTags = explode(',',$this->getValue());
		$countTags = count($arrTags);

		for($i=0;$i<$countTags;$i++)
		{
			$arrTags[$i] = trim($arrTags[$i]);
			
			$outputTags[$i] = '';
			$outputTags[$i] .= '<a class="tag" rel="tag" href="'.JRoute::_('index.php?option=com_mtree&task=searchby&cf_id='.$this->getId().'&value='.urlencode($arrTags[$i])).'">';
			$outputTags[$i] .= $arrTags[$i];
			$outputTags[$i] .= '</a>';
		}

		$html = '';
		$html .= implode(', ',$outputTags);

		return $html;
	}
	
	function getWhereCondition() {
		$args = func_get_arg(0);
		$return = '(';
		$where = array();
		
		if( is_array($args) ) {
			foreach( $args AS $arg ) {
				$where[] = 'cfv#.value LIKE \'%' . JFactory::getDBO()->escape( $arg, true ) . '%\'';
			}
		}
		if( count($where) > 1 ) {
			$return .= '((' . implode(') AND (',$where) . '))';
			$return .= ')';
			return $return;
		} else {
			$return .= $where[0] . ')';
			return $return;
		}
	}
	
	function parseValue($value) {
		$params['maxChars'] = intval($this->getParam('maxChars',80));
		$value = JString::substr($value,0,$params['maxChars']);
		
		// Allow alphanumeric with dashes, and spaces
		$pattern = "/^[ŞşİıÇçĞğÜüÖöАаӐӑӒӓӘәӚӛӔӕБбВвГгҐґЃѓҒғӶӷҔҕДдЂђЕеЀѐЁёӖӗҼҽҾҿЄєЖжӁӂҖҗӜӝЗзҘҙӞӟӠӡЅѕИиЍѝӤӥӢӣІіЇїӀӀЙйҊҋЈјКкҚқҞҟҠҡӃӄҜҝЛлӅӆЉљМмӍӎНнҤҥҢңӉӊӇӈЊњОоӦӧӨөӪӫҨҩПпҦҧРрҎҏСсҪҫТтҬҭЋћЌќУуЎўӲӳӰӱӮӯҮүҰұФфХхҲҳҺһЦцҴҵЧчӴӵҶҷӋӌҸҹЏџШшЩщЪъЫыӸӹЬьҌҍЭэӬӭЮюЯяA-Za-z0-9- ]+$/";

		$arrTags = explode(',',$value);
		$countTags = count($arrTags);
		
		for($i=0;$i<$countTags;$i++)
		{
			$arrTags[$i] = trim($arrTags[$i]);
			if( !preg_match( $pattern, $arrTags[$i] ) ) {
			    unset($arrTags[$i]);
			}
		}
		return implode(', ',$arrTags);
	}
}
?>