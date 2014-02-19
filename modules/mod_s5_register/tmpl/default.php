<?php







/**







* @title		Shape 5 Register Module
* @version		1.0
* @package		Joomla
* @website		http://www.shape5.com
* @copyright	Copyright (C) 2009 Shape 5 LLC. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access



defined( '_JEXEC' ) or die( 'Restricted access' );



session_start();



?>
<?php 



jimport( 'joomla.html.parameter' );



JHtml::_('behavior.keepalive');



JHtml::_('behavior.tooltip');



JHtml::_('behavior.formvalidation');



JHTML::_('behavior.calendar');



$app		= JFactory::getApplication();



JPluginHelper::importPlugin('user');

$plugin = JPluginHelper::getPlugin('user', 'profile');
$pluginParams = new JRegistry();
$pluginParams->loadString($plugin->params);


@$params1 = new JRegistry($plugin1->params);
$data = $params1->get('public_key');



$lang =& JFactory::getLanguage();



$extension = 'com_users';



$base_dir = JPATH_SITE;



$reload = true;



$lang->load($extension, $base_dir, $language_tag, $reload);



$document = &JFactory::getDocument();  



$url = JURI::root().'modules/mod_s5_register/';



$document->addCustomTag('<link rel="stylesheet" href="'.$url.'css/s5_register.css" type="text/css" />');






//if($modules_params->get('captcha')==1) {

//	$document->addScript($url.'captcha/CaptchaSecurityImages.php');

//}



jimport('joomla.application.component.helper');

$content_params = & JComponentHelper::getParams( 'com_users' );

$allres = $content_params->get( 'allowUserRegistration' );

if($allres == 0)

{

    echo JTEXT::_('MOD_REGISTER_PLUGIN_ALERT');

}

else

{

	 $document->addScriptDeclaration('

	window.addEvent("domready", function() {

		$("submit_btn").addEvent("click", function(event) {

			//prevent the page from changing

			document.formvalidator = new JFormValidator;

			if(document.formvalidator.isValid(document.getElementById("josForm")))

			{

				ajaxdata();

			} else {

				return false;

			}

		});

	 });



');  

?>
<script type="application/javascript">

function getXMLHttp()

{

			  var xmlHttp

			  try

			  {

				//Firefox, Opera 8.0+, Safari

				xmlHttp = new XMLHttpRequest();

			  }

			  catch(e)

			  {

				//Internet Explorer

				try

				{

				  xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");

				}

				catch(e)

				{

				  try

				  {

					xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");

				  }

				  catch(e)

				  {

					alert("Your browser does not support AJAX!")

					return false;

				  }

				}

			  }

			  return xmlHttp;

		}

function ajaxdata()

{

	 // document.getElementById("josForm").submit();

	  var capch = document.getElementById("security_code").value;

	  var jform_username = document.getElementById("jform_username").value;

	  var jform_email1 = document.getElementById("jform_email1").value;

	  var jform_email12 = document.getElementById("jform_email2").value;

	  var jform_password1 = document.getElementById("jform_password1").value;

	  var jform_password2 = document.getElementById("jform_password2").value;

	  var captchaval = document.getElementById("captchaval").value;

	  //alert(jform_email1);

	  var xmlHttp = getXMLHttp();

	  xmlHttp.onreadystatechange = function()

	  {

		if(xmlHttp.readyState == 4)

		{

			  HandleResponse(xmlHttp.responseText);

		}

	  }

	

	  xmlHttp.open("POST", "<?php echo $url.'captcha/validate.php'; ?>?jcapch="+capch+"&jemail="+jform_email1+"&juser="+jform_username+"&jemail2="+jform_email12+"&jpass1="+jform_password1+"&jpass2="+jform_password2+"&captchaval="+captchaval, true);

	  xmlHttp.send(null);

}

		

function HandleResponse(response)

{

	  if(response == 'Success')

	  {

		document.getElementById("josForm").submit();

	  }

	  else

	  {

		  document.getElementById("s5_regresponse").style.display = "block";

		  document.getElementById('s5_regresponse').innerHTML = response;  

	  }

}

</script>

<div id="s5_regresponse"></div>
<form class="form-validate" name="josForm" id="josForm" method="post" action="index.php" >
  <div id="result" style="display:none;"></div>
  <div class="s5_regfloatleft s5_reglabel">
    <label for="jform_name" id="jform_name-lbl"><?php echo JTEXT::_('COM_USERS_REGISTER_NAME_LABEL');?> *</label>
  </div>
  <div class="s5_regfloatleft s5_reginput">
    <input type="text" maxlength="50" class="inputbox required" value="" size="30" id="jform_name" name="jform[name]"/>
  </div>
  <div style="clear:both;"></div>
  <div class="s5_regfloatleft s5_reglabel">
    <label for="jform_username" id="jform_username-lbl"><?php echo JTEXT::_('COM_USERS_REGISTER_USERNAME_LABEL');?> *</label>
  </div>
  <div class="s5_regfloatleft s5_reginput">
    <input type="text" maxlength="25" class="inputbox required validate-username" value="" size="30" name="jform[username]" id="jform_username"/>
  </div>
  <div style="clear:both;"></div>
  <div class="s5_regfloatleft s5_reglabel">
    <label for="jform_email1" id="jform_email1-lbl"><?php echo JTEXT::_('COM_USERS_REGISTER_EMAIL1_LABEL');?> *</label>
  </div>
  <div class="s5_regfloatleft s5_reginput">
    <input type="text" maxlength="100" class="inputbox required validate-email" value="" size="30" name="jform[email1]" id="jform_email1"/>
  </div>
  <div style="clear:both;"></div>
  <div class="s5_regfloatleft s5_reglabel">
    <label for="jform_email2" id="jform_email2-lbl"><?php echo JTEXT::_('COM_USERS_REGISTER_EMAIL2_LABEL');?> *</label>
  </div>
  <div class="s5_regfloatleft s5_reginput">
    <input type="text" maxlength="100" class="inputbox required validate-email" value="" size="30" name="jform[email2]" id="jform_email2"/>
  </div>
  <div style="clear:both;"></div>
  <div class="s5_regfloatleft s5_reglabel">
    <label for="jform_password1" id="jform_password1-lbl"><?php echo JTEXT::_('COM_USERS_REGISTER_PASSWORD1_LABEL');?> * </label>
  </div>
  <div class="s5_regfloatleft s5_reginput">
    <input type="password" value="" size="30" name="jform[password1]" id="jform_password1" class="inputbox required validate-password"/>
  </div>
  <div style="clear:both;"></div>
  <div class="s5_regfloatleft s5_reglabel">
    <label for="jform_password2" id="jform_password2-lbl"><?php echo JTEXT::_('COM_USERS_REGISTER_PASSWORD2_LABEL');?> *</label>
  </div>
  <div class="s5_regfloatleft s5_reginput">
    <input type="password" value="" size="30" name="jform[password2]" id="jform_password2" class="inputbox required validate-passverify"/>
  </div>
  <div style="clear:both;"></div>
  <?php 







if($plugin){



	if($pluginParams->get('register-require_address1')!=0) {?>
  <?php if($pluginParams->get('register-require_address1')==1){ $class="hasTip"; $class1=""; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_ADDRESS1_LABEL');}else { $class="hasTip required"; $class1="required"; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_ADDRESS1_LABEL')."*";}?>
  <div class="s5_regfloatleft s5_reglabel">
    <label for="jform_profile_address1" id="jform_profile_address1-lbl" class="<?php echo $class ;?>" title="<?php echo JTEXT::_('PLG_USER_PROFILE_FIELD_ADDRESS1_LABEL')?>"> <?php echo $lable;?> </label>
  </div>
  <div class="s5_regfloatleft s5_reginput">
    <input id="jform_profile_address1" type="text" size="30" value="" name="jform[profile][address1]" class="<?php echo $class1 ;?>">
  </div>
  <div style="clear:both;"></div>
  <?php } ?>
  <?php 



	if($pluginParams->get('register-require_address2')!=0) { 



		if($pluginParams->get('register-require_address2')==1){ $class="hasTip"; $class1=""; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_ADDRESS2_LABEL');}else { $class="hasTip required"; $class1="required"; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_ADDRESS2_LABEL')."*"; } ?>
  <div class="s5_regfloatleft s5_reglabel">
    <label for="jform_profile_address2" id="jform_profile_address2-lbl" class="<?php echo $class ;?>" title="<?php echo JTEXT::_('PLG_USER_PROFILE_FIELD_ADDRESS2_LABEL')?>"> <?php echo $lable;?> </label>
  </div>
  <div class="s5_regfloatleft s5_reginput">
    <input id="jform_profile_address2" type="text" size="30" value="" name="jform[profile][address2]" class="<?php echo $class1 ;?>">
  </div>
  <div style="clear:both;"></div>
  <?php } ?>
  <?php 



	if($pluginParams->get('register-require_city')!=0) { 



		if($pluginParams->get('register-require_city')==1){ $class="hasTip"; $class1=""; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_CITY_LABEL');}else { $class="hasTip required"; $class1="required"; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_CITY_LABEL')."*";}



		?>
  <div class="s5_regfloatleft s5_reglabel">
    <label for="jform_profile_city" id="jform_profile_city-lbl" class="<?php echo $class ;?>" title="<?php echo JTEXT::_('PLG_USER_PROFILE_FIELD_CITY_LABEL')?>"><?php echo $lable;?></label>
  </div>
  <div class="s5_regfloatleft s5_reginput">
    <input id="jform_profile_city" type="text" size="30" value="" name="jform[profile][city]" class="<?php echo $class1 ;?>" aria-invalid="false">
  </div>
  <div style="clear:both;"></div>
  <?php	} ?>
  <?php 



	if($pluginParams->get('register-require_region')!=0) {



		if($pluginParams->get('register-require_region')==1){ $class="hasTip"; $class1=""; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_REGION_LABEL');}else { $class="hasTip required"; $class1="required"; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_REGION_LABEL')."*";}







		?>
  <div class="s5_regfloatleft s5_reglabel">
    <label for="jform_profile_region" id="jform_profile_region-lbl" class="<?php echo $class ;?>" title="<?php echo JTEXT::_('PLG_USER_PROFILE_FIELD_REGION_LABEL')?>"><?php echo $lable;?></label>
  </div>
  <div class="s5_regfloatleft s5_reginput">
    <input id="jform_profile_region" type="text" size="30" value="" name="jform[profile][region]" class="<?php echo $class1 ;?>">
  </div>
  <div style="clear:both;"></div>
  <?php	} ?>
  <?php 



	if($pluginParams->get('register-require_country')!=0) { 







		if($pluginParams->get('register-require_country')==1){ $class="hasTip"; $class1=""; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_COUNTRY_LABEL');}else { $class="hasTip required"; $class1="required"; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_COUNTRY_LABEL')."*";}



		?>
  <div class="s5_regfloatleft s5_reglabel">
    <label for="jform_profile_country" id="jform_profile_country-lbl" class="<?php echo $class ;?>" title="<?php echo JTEXT::_('PLG_USER_PROFILE_FIELD_COUNTRY_LABEL')?>"><?php echo $lable?></label>
  </div>
  <div class="s5_regfloatleft s5_reginput">
    <input id="jform_profile_country" type="text" size="30" value="" name="jform[profile][country]" class="<?php echo $class1 ;?>">
  </div>
  <div style="clear:both;"></div>
  <?php } ?>
  <?php 



	if($pluginParams->get('register-require_postal_code')!=0) { 



		if($pluginParams->get('register-require_postal_code')==1){ $class="hasTip"; $class1=""; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_POSTAL_CODE_LABEL');}else { $class="hasTip required"; $class1="required"; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_POSTAL_CODE_LABEL')."*";}



		?>
  <div class="s5_regfloatleft s5_reglabel">
    <label for="jform_profile_postal_code" id="jform_profile_postal_code-lbl" class="<?php echo $class ;?>" title="<?php echo JTEXT::_('PLG_USER_PROFILE_FIELD_POSTAL_CODE_LABEL')?>"><?php echo $lable;?></label>
  </div>
  <div class="s5_regfloatleft s5_reginput">
    <input id="jform_profile_postal_code" type="text" size="30" value="" name="jform[profile][postal_code]" class="<?php echo $class1 ;?>">
  </div>
  <div style="clear:both;"></div>
  <?php } ?>
  <?php 



	if($pluginParams->get('register-require_phone')!=0) { 



		if($pluginParams->get('register-require_phone')==1){ $class="hasTip"; $class1=""; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_PHONE_LABEL');}else { $class="hasTip required"; $class1="required";$lable=JTEXT::_('PLG_USER_PROFILE_FIELD_PHONE_LABEL')."*";}



		?>
  <div class="s5_regfloatleft s5_reglabel">
    <label for="jform_profile_phone" id="jform_profile_phone-lbl" class="<?php echo $class ;?>" title="<?php echo JTEXT::_('PLG_USER_PROFILE_FIELD_PHONE_LABEL')?>"><?php echo $lable;?></label>
  </div>
  <div class="s5_regfloatleft s5_reginput">
    <input id="jform_profile_phone" type="text" size="30" value="" name="jform[profile][phone]" class="<?php echo $class1 ;?>">
  </div>
  <div style="clear:both;"></div>
  <?php	} ?>
  <?php 



	if($pluginParams->get('register-require_website')!=0) { 



		if($pluginParams->get('register-require_website')==1){ $class="hasTip"; $class1=""; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_WEB_SITE_LABEL');}else { $class="hasTip required"; $class1="required"; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_WEB_SITE_LABEL')."*";}



		?>
  <div class="s5_regfloatleft s5_reglabel">
    <label for="jform_profile_website" id="jform_profile_website-lbl" class="<?php echo $class ;?>" title="<?php echo JTEXT::_('PLG_USER_PROFILE_FIELD_WEB_SITE_LABEL')?>"><?php echo $lable;?></label>
  </div>
  <div class="s5_regfloatleft s5_reginput">
    <input id="jform_profile_website" type="text" size="30" value="" name="jform[profile][website]" class="<?php echo $class1 ;?>">
  </div>
  <div style="clear:both;"></div>
  <?php } ?>
  <?php 



	if($pluginParams->get('register-require_favoritebook')!=0) { 



		if($pluginParams->get('register-require_favoritebook')==1){ $class="hasTip"; $class1=""; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_FAVORITE_BOOK_LABEL');}else { $class="hasTip required"; $class1="required"; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_FAVORITE_BOOK_LABEL')."*";}



		?>
  <div class="s5_regfloatleft s5_reglabel">
    <label for="jform_profile_favoritebook" id="jform_profile_favoritebook-lbl" class="<?php echo $class ;?>" title="<?php echo JTEXT::_('PLG_USER_PROFILE_FIELD_FAVORITE_BOOK_LABEL')?>"><?php echo $lable;?></label>
  </div>
  <div class="s5_regfloatleft s5_reginput">
    <input id="jform_profile_favoritebook" type="text" size="30" value="" name="jform[profile][favoritebook]" class="<?php echo $class1 ;?>">
  </div>
  <div style="clear:both;"></div>
  <?php } ?>
  <?php 



	if($pluginParams->get('register-require_aboutme')!=0) { 



		if($pluginParams->get('register-require_aboutme')==1){ $class="hasTip"; $class1=""; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_ABOUT_ME_LABEL');}else { $class="hasTip required"; $class1="required"; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_ABOUT_ME_LABEL')."*";}



		?>
  <div class="s5_regfloatleft s5_reglabel">
    <label for="jform_profile_aboutme" id="jform_profile_aboutme-lbl" class="<?php echo $class ;?>" title="<?php echo JTEXT::_('PLG_USER_PROFILE_FIELD_ABOUT_ME_LABEL')?>"><?php echo $lable; ?></label>
  </div>
  <div class="s5_regfloatleft s5_reginput">
    <textarea id="jform_profile_aboutme" rows="5" cols="30" name="jform[profile][aboutme]" class="<?php echo $class1 ;?>"></textarea>
  </div>
  <div style="clear:both;"></div>
  <?php } ?>
  <?php 



	if($pluginParams->get('register-require_dob')!=0) { 



		if($pluginParams->get('register-require_dob')==1){ $class="hasTip"; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_DOB_LABEL');}else { $class="hasTip required"; $lable=JTEXT::_('PLG_USER_PROFILE_FIELD_DOB_LABEL')."*";}



		?>
  <div class="s5_regfloatleft s5_reglabel">
    <label for="jform_profile_dob" id="jform_profile_dob-lbl" class="<?php echo $class ;?>" title="<?php echo JTEXT::_('PLG_USER_PROFILE_FIELD_DOB_LABEL')?>"><?php echo $lable;?></label>
  </div>
  <div class="s5_regfloatleft s5_reginput"> <?php echo JHTML::calendar('','jform[profile][dob]', 'jform_profile_dob1', '%Y-%m-%d',''); ?> </div>
  <div style="clear:both;"></div>
  <?php } ?>
  <?php  if($pluginParams->get('register-require_tos') && $pluginParams->get('register_tos_article') > 0){ ?>
  <div class="s5_regfloatleft s5_regagreement">
    <?php 	 



		$db = JFactory::getDBO();



		$q = 'SELECT introtext FROM #__content WHERE id ="'.$pluginParams->get('register_tos_article').'"';



		$db->setQuery($q);



		$introtext = $db->loadResult();



  	 	echo $introtext;



		?>
  </div>
  <div class="s5_regfloatleft s5_regagreewrap s5_reglabel">
    <input id="jform_profile_tos0" type="radio" value="1" name="jform[profile][tos]">
    <label for="jform_profile_tos0"><?php echo JTEXT::_('PLG_USER_PROFILE_OPTION_AGREE');?></label>
  </div>
  <div style="clear:both;"></div>
  <?php } ?>
  <?php } ?>
  <?php 







  if($modules_params->get('captcha')==1) {?>
  <div class="s5_regfloatleft s5_reglabel">
    <label for="security_code" id="s5_regsecurity_label"><?php echo JTEXT::_('MOD_REGISTER_SECURITY_TEXT');?></label>
  </div>
  <div style="clear:both;"></div>
  <img id="s5_regsecurity_img" src="<?php echo $url.'captcha/CaptchaSecurityImages.php'; ?>?width=90&height=30&characters=5&t=<?php echo time();?>" alt="" />
  <div style="clear:both;"></div>
  <div class="s5_regfloatleft s5_reginput">
    <input id="security_code" name="security_code" class="inputbox" type="text" />
  </div>
  <input type="hidden" name="captchaval" id="captchaval" value="1" />
  <?php } else { ?>



		    <input type="hidden" name="captchaval" id="captchaval" value="0" />



            <?php 



			echo '<div id="security_code"></div>';



	}?>
  <div style="clear:both;"></div>
  <div class="s5_regfloatleft s5_regrequiredfields s5_reglabel">
    <label><?php echo JTEXT::_('MOD_REGISTER_REQUIRED_FILEDS');?></label>
  </div>
  <div style="clear:both;"></div>
  <?php $version = new JVersion(); ?>
  <input type="hidden" name="option" value="com_users" />
  <button id="submit_btn" type="button"  class="<?php if($version->RELEASE >= '3.0') { ?>btn btn-primary <?php } else { ?>button <?php } ?>validate"><?php echo JTEXT::_('JREGISTER');?></button>
  <input type="hidden" name="task" value="registration.register" />
  <?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php } ?>
