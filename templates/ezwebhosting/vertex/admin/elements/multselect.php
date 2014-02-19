<?php
// Ensure this file is being included by a parent file
defined('_JEXEC') or die('Restricted access');
class JFormFieldmultselect extends JFormField {
  protected $type = 'multselect';
  function getInput() {
    $name = $this->name;
    $value = $this->value;
    $node = $this->element;
    $control_name = $this->id;
    $vars = explode(",",$this->element['vars']);
    $options = array();
    for($i=0;$i<count($vars);$i++) {
      $options[] = JHTML::_('select.option',  $vars[$i], $vars[$i]);
    }
    $result  = JHTML::_('select.genericlist', $options, $this->name.'[]', 'class="inputbox" size="5" multiple="multiple"', 'value', 'text', $value);
    return $result;
  }
}