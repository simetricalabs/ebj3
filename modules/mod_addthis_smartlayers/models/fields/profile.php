<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.form.formfield');
 
class JFormFieldProfile extends JFormField {
 
        protected $type = 'Profile'; 
 
        public function getInput() {
    			
    		$ats_db = &JFactory::getDBO();
			$ats_share_query = 'SELECT params FROM #__modules WHERE module="mod_AddThis"';
			$ats_db->setQuery($ats_share_query);			
        	$ats_share_params = json_decode( $ats_db->loadResult(), true );        	
        	$ats_share_profile_id = $ats_share_params['profile_id'];
        	
        	$ats_follow_query = 'SELECT params FROM #__modules WHERE module="mod_addthis_follow"';
			$ats_db->setQuery($ats_follow_query);			
        	$ats_follow_params = json_decode( $ats_db->loadResult(), true );        	
        	$ats_follow_profile_id = $ats_follow_params['atf_profile_id'];

         	$ats_sl_query = 'SELECT params FROM #__modules WHERE module="mod_addthis_smartlayers"';
			$ats_db->setQuery($ats_sl_query);			
        	$ats_sl_params = json_decode( $ats_db->loadResult(), true );        	
        	$ats_sl_profile_id = $ats_sl_params['ats_profile_id'];

        	if((($ats_share_profile_id != $ats_sl_profile_id) ||  ($ats_follow_profile_id != $ats_sl_profile_id)) && ($ats_sl_profile_id !="")) {
        		$ats_share_params['profile_id'] = (string) $ats_sl_profile_id;
        		$ats_share_param_string = json_encode( $ats_share_params );
        		$ats_db->setQuery('UPDATE #__modules SET params = ' .$ats_db->quote( $ats_share_param_string ) . ' WHERE module = '.$ats_db->quote('mod_AddThis'));
        		$ats_db->query();
        		
        		$ats_follow_params['atf_profile_id'] = (string) $ats_sl_profile_id;
        		$ats_follow_param_string = json_encode( $ats_follow_params );
        		$ats_db->setQuery('UPDATE #__modules SET params = ' .$ats_db->quote( $ats_follow_param_string ) . ' WHERE module = '.$ats_db->quote('mod_addthis_follow'));
        		$ats_db->query();        		
        	} else if($ats_sl_profile_id == "" && ($ats_share_profile_id!="" || $ats_follow_profile_id!="")) {
        		$ats_sl_params['ats_profile_id'] = (string) ($ats_share_profile_id!="" ? $ats_share_profile_id : $ats_follow_profile_id);
        		if($ats_sl_params['ats_profile_id']){
	         		$ats_sl_param_string = json_encode( $ats_sl_params );
	        		$ats_db->setQuery('UPDATE #__modules SET params = ' .$ats_db->quote( $ats_sl_param_string ) . ' WHERE module = '.$ats_db->quote('mod_addthis_smartlayers'));
	        		$ats_db->query();        			
        		}
        		$ats_sl_profile_id = $ats_sl_params['ats_profile_id'];   		
        	}
        	
        	if(!$ats_sl_profile_id)
        		$ats_sl_profile_id = "Your Profile ID";

        	return '<input type="text" id="'.$this->id.'" name="'.$this->name.'" value="'.$ats_sl_profile_id.'"/>';
		}
}
