<?php
/*
 * @subpackage  mod_addthis_smartlayers
 * +--------------------------------------------------------------------------+
 * | Copyright (c) 2013 Add This, LLC                                         |
 * +--------------------------------------------------------------------------+
 * | This program is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by     |
 * | the Free Software Foundation; either version 3 of the License, or        |
 * | (at your option) any later version.                                      |
 * |                                                                          |
 * | This program is distributed in the hope that it will be useful,          |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
 * | GNU General Public License for more details.                             |
 * |                                                                          |
 * | You should have received a copy of the GNU General Public License        |
 * | along with this program.  If not, see <http://www.gnu.org/licenses/>.    |
 * +--------------------------------------------------------------------------+
 */

	/**
	 *
	 * Creates AddThis Smart Layers and appends it to the user selected pages.
	 * Reads the user settings and creates the Layers accordingly.
	 *
	 * @author AddThis Team - Sol, Vipin
	 * @version 1.0.0
	 */

    // no direct access
	defined('_JEXEC') or die('Restricted access');
	appendAddThisJs($params);
	
    //Adds AddThis Follow to page	
    appendAddThisSL($params);

	/**
	 * append AddThis Smart Layers
	 *
	 * Reads settings page, creates AddThis Smart Layers,
	 *
	 * @param object $params
	 * @return void
	 *
	 */
	function appendAddThisSL($params)
	{

		//Creates addthis smart layers configuration script
		$atslScript = "<!-- AddThis Smart Layers BEGIN -->" . PHP_EOL;
	    $atslScript .= "<script type='text/javascript'>". PHP_EOL;
	    $atslScript .= "function addthis_smart_layers(){\n";
			
		if($params->get("ats_customcode_enabled") == "true" && $params->get("ats_customcode_content") != ""){
			$atslScript .= $params->get("ats_customcode_content");
		} else {		
		    $atslScript .= "\taddthis.layers({". PHP_EOL;
			$atslScript .= "\t\t'theme' : '".$params->get("ats_more_theme")."'". PHP_EOL;
		    
		    if($params->get("ats_share_enabled") == "true") {
				$atslScript .= "\t\t,'share' : {\n\t\t\t'position' : '".$params->get("ats_share_position")."',\n\t\t\t'numPreferredServices' : ".$params->get("ats_share_btn_count")."\n\t\t}". PHP_EOL;
		    }
		    
		    if($params->get("ats_follow_enabled") == "true") { 
		    	
		    	$ats_follow_services = array("facebook","twitter","linkedin","linkedin_comp","google","youtube","flickr","vimeo","pinterest","instagram","foursquare","tumblr","rss");
		    	
				$atslScript .= "\t\t,'follow' : {";
				$atslScript .= "\n\t\t\t'services' : [";
				
				foreach($ats_follow_services as $service) {
					
					$ats_follow_id = "ats_follow_fld_".$service;
					if($params->get($ats_follow_id) != "" && $params->get($ats_follow_id) != "YOUR-PROFILE") {
						if($service == "linkedin_comp")
							$atslScript .= "\n\t\t\t\t{'service': 'linkedin', 'id': '".$params->get($ats_follow_id)."', 'usertype':'company'},";
						else
							$atslScript .= "\n\t\t\t\t{'service': '".$service."', 'id': '".$params->get($ats_follow_id)."'},";
					}	
				}
				$atslScript .= "\n\t\t\t]";
				$atslScript .= "\n\t\t}". PHP_EOL;
		    }
		    
		    if($params->get("ats_whatsnext_enabled") == "true") {   
				$atslScript .= "\t\t,'whatsnext' : {}". PHP_EOL;
		    }
	
		    if($params->get("ats_recommended_enabled") == "true") {
				$atslScript .= "\t\t,'recommended' : {\n\t\t\t'title': '".$params->get("ats_recommended_header")."'\n\t\t}". PHP_EOL;
			}
							     
			$atslScript .= "\t});". PHP_EOL;
					
		} // custom code is not enabled
		$atslScript .= "};//onload function". PHP_EOL;
	    $atslScript .= "</script>". PHP_EOL;
	    $atslScript .= "<!-- AddThis Smart Layers ENDS -->" . PHP_EOL;

		echo $atslScript;
	}


	/**
	 * Appending addthis main script to the head
	 *
	 * @return void
	 */    
    function appendAddThisJs($params){
        	
    	if($params->get("ats_profile_id")!=""){
    		$ats_profile = urlencode($params->get("ats_profile_id"));
    	} else {
    		$ats_profile = "xa-52206d28623a1b2c";
    	}    	
    	//Append addthis javascript file
	    $at_sl_script = "<script type='text/javascript'>". PHP_EOL;
	    $at_sl_script .= "window.addEventListener('load', function (){". PHP_EOL;
	    $at_sl_script .= "\tif(typeof addthis_conf == 'undefined'){". PHP_EOL;
	    $at_sl_script .= "\t\tvar script = document.createElement('script');". PHP_EOL;
	    $at_sl_script .= "\t\tscript.src = '//s7.addthis.com/js/300/addthis_widget.js#pubid=".$ats_profile."';". PHP_EOL;
	    $at_sl_script .= "\t\tscript.onload = function() { addthis_smart_layers(); }". PHP_EOL;
	    $at_sl_script .= "\t\tdocument.getElementsByTagName('head')[0].appendChild(script);". PHP_EOL;	    
	    $at_sl_script .= "\t\tvar addthis_product = 'jsl-1.0';". PHP_EOL;
	    $at_sl_script .= "\t} else{". PHP_EOL;
	    $at_sl_script .= "\t\taddthis_smart_layers();\n\t}\n});". PHP_EOL;
	    $at_sl_script .= "</script>". PHP_EOL;
	    
	    echo $at_sl_script;   	
    }  