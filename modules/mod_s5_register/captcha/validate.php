<?php 
	session_start();
	define( '_JEXEC', 1 );
	if(!defined('DS')){
		define('DS',DIRECTORY_SEPARATOR);
	}
	define( 'JPATH_BASE', realpath(dirname(__FILE__).'/../../..' ));
	require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
	require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
	JDEBUG ? $_PROFILER->mark( 'afterLoad' ) : null;
	$mainframe =& JFactory::getApplication('site');
	$mainframe->initialise();
	JPluginHelper::importPlugin('system');
	JDEBUG ? $_PROFILER->mark('afterInitialise') : null;
	$mainframe->triggerEvent('onAfterInitialise');
	$data = JRequest::get('post');
	$user = JFactory::getUser();
	$db = JFactory::getDBO();
	$app1 = &JFactory::getApplication();
	$lang =& JFactory::getLanguage();
	
	$extension = 'com_users';
	$base_dir = JPATH_SITE;
	$language_tag = 'en-GB';
	$reload = true;
	$lang->load($extension, $base_dir, $language_tag, $reload);
	
	$modules5 = 'mod_s5_register';
	$base_dirs5 = JPATH_SITE;
	$language_tags5 = 'en-GB';
	$reloads5 = true;
	$lang->load($modules5, $base_dirs5, $language_tags5, $reloads5);
	$email=0;
	$user_name = 0;
	$html = '';
	if($_REQUEST['jemail'] !=$_REQUEST['jemail2']) $html .= '<br>'.JTEXT::_('COM_USERS_REGISTER_EMAIL2_MESSAGE'); 
	if($_REQUEST['jpass1'] !=$_REQUEST['jpass2']) $html .= '<br>'.JTEXT::_('COM_USERS_REGISTER_PASSWORD1_MESSAGE'); 
	$query = 'SELECT id FROM #__users WHERE email = "'.$_REQUEST['jemail'].'"';
	$db->setQuery($query);
	$e_id = $db->loadResult();
	if($e_id)$html .= '<br>'.JTEXT::_('COM_USERS_REGISTER_EMAIL1_MESSAGE');
	$query1 = 'SELECT id FROM #__users WHERE username = "'.$_REQUEST['juser'].'"';
	$db->setQuery($query1);
	$u_id = $db->loadResult();
	if($u_id)$html .= '<br>'.JTEXT::_('COM_USERS_REGISTER_USERNAME_MESSAGE');  
	  $sc = $_SESSION['security_code'];//$app1->getUserState('security_code');
	error_reporting(0);
	if($_REQUEST['captchaval'] == 1) {
		if( $sc == $_REQUEST['jcapch'] && !empty($sc ) ) {
			unset($sc);
		} else {
				  $html .= '<br>'.JTEXT::_('MOD_REGISTER_CAPTCHA_ERROR'); 
		}		
	} // condition   
    if($html=='') { unset($_SESSION['security_code']);echo 'Success'; exit; }
    else{echo $html;exit;}
?>

