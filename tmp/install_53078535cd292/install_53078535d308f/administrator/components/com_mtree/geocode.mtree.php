<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2009 Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('_JEXEC') or die('Restricted access');

$app		= JFactory::getApplication('site');
$task2		= JFactory::getApplication()->input->getCmd( 'task2', '');

switch($task2) {
	case 'savecoord':
		savecoord();
		break;
	case 'saveaddress':
		saveaddress();
		break;
	case 'getlistings':
		$count = JFactory::getApplication()->input->getInt( 'count', 25);
		getlistings($count);
		break;
	default:
		geocode();
}

function savecoord() {
	$db = JFactory::getDBO();
		
	$link_ids = JFactory::getApplication()->input->get( 'link_id', array(), 'array' );
	JArrayHelper::toInteger($link_ids, array(0));

	$lats = JFactory::getApplication()->input->get( 'lat', array(), 'array' );
	$lngs = JFactory::getApplication()->input->get( 'lng', array(), 'array' );
	
	$coordinates = array();
	$done_link_ids = array();
	
	$return = (object) array(
		'status'	=> '',
		'data'		=> array()
	);
	
	if( !empty($link_ids) ) {
		foreach( $link_ids AS $link_id ) {
			if( $lats[$link_id] && !empty($lats[$link_id]) && $lngs[$link_id] && !empty($lngs[$link_id]) ) {
				$coordinates[$link_id] = array('lat'=>$lats[$link_id], 'lng'=>$lngs[$link_id]);
			}
		}
		if( !empty($coordinates) ) {
			foreach( $coordinates AS $link_id => $coordinate ) {
				$sql = 'UPDATE #__mt_links SET lat = ' . $db->Quote($coordinate['lat']) . ', lng = ' . $db->Quote($coordinate['lng']) . ', zoom = ' . $db->Quote('10') . ' WHERE link_id = ' . $db->Quote($link_id) . ' LIMIT 1';
				$db->setQuery($sql);
				$db->execute();
				if( $db->getAffectedRows() > 0 ) {
					$done_link_ids[] = $link_id;
				}
			}
		}
	}
	
	if( !empty($done_link_ids) ) {
		$return->status = 'OK';
		$return->data = $done_link_ids;
	}

	echo json_encode($return);
}

function saveaddress() {
	$db = JFactory::getDBO();
		
	$link_id = JFactory::getApplication()->input->getInt( 'link_id', 0 );

	$address = JFactory::getApplication()->input->get( 'address', '', 'string' );
	$city = JFactory::getApplication()->input->get( 'city', '', 'string' );
	$state = JFactory::getApplication()->input->get( 'state', '', 'string' );
	$postcode = JFactory::getApplication()->input->get( 'postcode', '', 'string' );
	$country = JFactory::getApplication()->input->get( 'country', '', 'string' );
	$return = (object) array(
		'status'	=> ''
	);
	
	if( !empty($link_id) ) {
		$db->setQuery( 'UPDATE #__mt_links SET address = ' . $db->Quote($address) . ', city = ' . $db->Quote($city) . ', state = ' . $db->Quote($state) . ', postcode = ' . $db->Quote($postcode) . ', country = ' . $db->Quote($country) . ' WHERE link_id = ' . $db->Quote($link_id) . ' LIMIT 1' );
		$db->execute();
		$return->status = 'OK';
	} else {
		$return->status = 'ERROR';
	}
	echo json_encode($return);
}

function getlistings($count=10) {
	$link_ids = JFactory::getApplication()->input->get( 'link_id', array(), 'array' );
	JArrayHelper::toInteger($link_ids, array(0));
	
	$listings = getUnGeocodedListings($count,$link_ids);
	$return = (object) array(
		'status'	=> 'OK',
		'data'		=> array()
		);
	$i = 0;
	foreach( $listings AS $listing ) {
		$return->data[$i]->link_id = $listing->link_id;
		$return->data[$i]->link_name = $listing->link_name;
		$return->data[$i]->address = $listing->address;
		$return->data[$i]->city = $listing->city;
		$return->data[$i]->state = $listing->state;
		$return->data[$i]->postcode = $listing->postcode;
		$return->data[$i]->country = $listing->country;
		$i++;
	}
	echo json_encode($return);
}

function geocode() {
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	
	if( 
		strpos(strtolower($user_agent),'firefox') === false 
		&&
		strpos(strtolower($user_agent),'opera') === false 
		&&
		strpos(strtolower($user_agent),'webkit') === false 
	) {
		HTML_mtgeocode::incompatible_browser();
		return;
	}
	
	$db = JFactory::getDBO();
	
	// Get total number of listings
	$db->setQuery( 'SELECT COUNT(*) FROM #__mt_links' );
	$total['all'] = $db->loadResult();
	
	// Get the number of geocoded listings, ie: listings that has latitude & longitude values
	$db->setQuery( 'SELECT COUNT(*) FROM #__mt_links WHERE lat != 0 && lng != 0 && zoom != 0' );
	$total['geocoded'] = $db->loadResult();
	
	// Get the number of listings which needs to be geocoded
	$db->setQuery( 'SELECT COUNT(*) FROM #__mt_links WHERE (lat = 0 && lng = 0 && zoom = 0) && (address != \'\' || city != \'\' || state != \'\' || country != \'\' || postcode != \'\')' );
	$total['req_geocoding'] = $db->loadResult();
	
	$listings = getUnGeocodedListings(25);
	
	HTML_mtgeocode::status( $listings, $total );
}

function getUnGeocodedListings($count=20,$exclude=null) {
	$db = JFactory::getDBO();
	
	// Get listings
	$sql = 'SELECT link_id, link_name, address, city, state, postcode, country '
		. 'FROM #__mt_links '
		. 'WHERE '
		. '(lat = 0 && lng = 0 && zoom = 0) '
		. ' && (address != \'\' || city != \'\' || state != \'\' || country != \'\' || postcode != \'\') ';
	if( !is_null($exclude) && !empty($exclude) ) {
		$sql .= ' && link_id NOT IN (' . implode(',',$exclude) . ')';
	}
	$sql .= ' LIMIT ' . $count;
	$db->setQuery( $sql );
	$listings = $db->loadObjectList();
	
	return $listings;
}

class HTML_mtgeocode {

	public static function status($listings, $total) {
		global $mtconf;
		$app = JFactory::getApplication('site');
		
		JHtml::_('behavior.framework');

		JText::script('COM_MTREE_GEOCODER_NOT_OK', true);
		JText::script('COM_MTREE_GEOCODER_FOUND', true);
	?>
	<script src="http://maps.googleapis.com/maps/api/js?v=3.6&amp;sensor=false" type="text/javascript"></script>
	<style type="text/css">
	#btnSave, #btnGeocode {
		font-weight: bold;
	}
	#grid tr {
		background-color: #F1F3F5;
	}
	#grid td {
		border-bottom: 1px solid #C9CCCD;
	}
	#grid td span.link_name {
		line-height: 2em;
	}
	#geocodeMessage {
		margin:0 15px;
	}
	.status {
		float: left;
		background-color: #E6F5D3;
		padding: 0 5px;
		margin: 1px 5px 0 0;
	}
	#grid td span.fulladdress {
		color: #0B55C4;
		line-height: 1.5em;
		white-space:nowrap;
		empty-cells:show;
		border-collapse: collapse;
		display: block;
	}
	#grid td.link_name {
		width: 180px;
		display:block;
		overflow:hidden;
		padding-left:10px;
	}
	#grid td.address {
		width:100%;
		max-width:440px;
		overflow:hidden;
	}
	#grid .editform {
		display: none;
		border-left: 1px solid #C9CCCD;
		padding-left:10px;
		margin-top:9px;
	}
	#grid .editform span {
		display:block;
		margin-bottom:6px;
	}
	#grid .editform label {
		width: 60px;
		display: block;
		float:left;
	}
	.editformcancel {
		margin-left:5px;
		color: #0B55C4;
		line-height:2.1em;
		margin-top:2px;
	}
	#grid input.linkcheckbox {
		position:relative;
		top:-3px;
		margin-right: 6px;
	}
	#grid span.found {
		color: green;
		font-weight: bold;
	}
	#grid span.notfound {
		color: red;
	}
	tfoot td {
		padding: 10px 6px 0 0;
		line-height: 2.5em;
		font-weight:bold;
	}
	#map img {
		max-width: none;
	}
	</style>
	<script language="javascript" type="text/javascript">
	jQuery.noConflict();
	var geocoder = new google.maps.Geocoder();

	var admin_site_url = '<?php echo JUri::root(); ?>' + 'administrator/index.php';
	var defaultLat = '<?php echo addslashes($mtconf->get('map_default_lat')); ?>';
	var defaultLng = '<?php echo addslashes($mtconf->get('map_default_lng')); ?>';
	var defaultZoom = 2;
	var bounds = null; 
	var marker = [];
	var map = null;
	
	jQuery(document).ready(function(){
		
		jQuery('#btnGeocode').click(function(){
			executeGeocode();
		});
		
		var center = new google.maps.LatLng(defaultLat, defaultLng);
		var mapOptions = {
			zoom: defaultZoom,
			center: center,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		
		map = new google.maps.Map(document.getElementById("map"), mapOptions);
		bounds = new google.maps.LatLngBounds();
		
		<?php 
		$address_parts = array('address','city','state','postcode','country');
		foreach( $listings AS $listing ) { 
			echo 'addRow("'.addslashes($listing->link_id).'", "'.addslashes($listing->link_name).'", "'.addslashes($listing->address).'", "'.addslashes($listing->city).'", "'.addslashes($listing->state).'", "'.addslashes($listing->postcode).'", "'.addslashes($listing->country).'")';
			echo "\n;";
		}
		?>
		updateSaveLocationsButton();
			
		function executeGeocode() {
			var addresses = getAddresses();
			for (var i = 0, j = addresses.length; i < j; i++) {
				if( addresses[i] ) {
					var link_id = addresses[i][0];
					var address = addresses[i][1];
					var result = geocode( link_id, address )
				}
			}
		}
		
		function markdone(link_id, point) {
			jQuery('#checkbox'+link_id).attr('disabled',false);
			jQuery('#checkbox'+link_id).attr('checked',true);
			jQuery('#lat'+link_id).val(point.lat());
			jQuery('#lng'+link_id).val(point.lng());
			jQuery('#status'+link_id).html(Joomla.JText._('COM_MTREE_GEOCODER_FOUND'));
			jQuery('#status'+link_id).removeClass('notfound');
			jQuery('#status'+link_id).addClass('found');
			jQuery('#status'+link_id).css('display','block');
			placeMarker(link_id,point.lat(),point.lng());
			bounds.extend(point);
			map.setCenter(bounds.getCenter());
			updateSaveLocationsButton();
		}
		function markundone(link_id) {
			jQuery('#status'+link_id).removeClass('found');
			jQuery('#status'+link_id).addClass('notfound');
			jQuery('#status'+link_id).html(Joomla.JText._('COM_MTREE_GEOCODER_NOT_OK'));
			jQuery('#status'+link_id).css('display','block');
			updateSaveLocationsButton();
		}

		function getAddresses() {
			var addresses = [];
			jQuery('span.fulladdress').each(function(i){
				var link_id = jQuery(this).attr("id").substr(11);
				if( jQuery('#lat'+link_id).val() == '' && jQuery('#lng'+link_id).val() == '' ) {
					var address = jQuery(this).html();
					addresses[i] = [link_id,address];
				}
			});
			return addresses;
		}
		
		function geocode(link_id,address) {
			geocoder.geocode(
		    {'address':address},
		    function(results,status) {
				if (status == google.maps.GeocoderStatus.OK) {
					markdone(link_id, results[0].geometry.location);
				} else {
					markundone(link_id);
				}
		    }
		  );
		}
		});
		
		function getListings(count) {
			var link_ids = '';
			jQuery('span.fulladdress').each(function(i){
				var link_id = jQuery(this).attr("id").substr(11);
				link_ids += "&link_id[]="+link_id;
			});

			jQuery('#geocodeMessage').html('<?php echo JText::_('COM_MTREE_GEOCODE_LOADING_LISTINGS'); ?>').fadeIn('fast');
			jQuery.ajax({
			  type: "POST",
			  url: admin_site_url,
			  data: "option=com_mtree&task=geocode&task2=getlistings&tmpl=component&hide=1&format=json&count="+count+link_ids,
			  dataType: "json",
			  success: function(data){
				if(data.status=='OK'){
					if(data.data.length==0){
						jQuery('#geocodeMessage').html('<?php echo JText::_('COM_MTREE_NO_MORE_LISTINGS'); ?>').fadeIn('fast');
					} else {
						jQuery('#geocodeMessage').fadeOut();
						jQuery.each(data.data, function(key, field) {
							addRow(
								field.link_id,
								field.link_name, 
								field.address, 
								field.city, 
								field.state, 
								field.postcode, 
								field.country
								);
					  	});
					}
				}
				updateSaveLocationsButton();
			  }
			});
		}
		
		function addRow(link_id, link_name, address, city, state, postcode, country) {
			var grid = document.getElementById("grid");
			var row = document.createElement("tr");
			row.id = "row" + link_id;
			
            		var cell_1 = document.createElement("td");
			
			var cb = document.createElement( "input" );
			cb.type = "checkbox";
			cb.id = "checkbox"+link_id;
			cb.className = "linkcheckbox";
			cb.name = "link_id[]";
			cb.value = link_id;
			cb.checked = false;
			cb.disabled = true;
            		cell_1.appendChild(cb);

			var lat = document.createElement( "input" );
			lat.type = "hidden";
			lat.id = "lat"+link_id;
			lat.className = "linklat";
			lat.name = "lat["+link_id+"]";
			lat.value = "";
            		cell_1.appendChild(lat);

			var lng = document.createElement( "input" );
			lng.type = "hidden";
			lng.id = "lng"+link_id;
			lng.className = "linklng";
			lng.name = "lng["+link_id+"]";
			lng.value = "";
            		cell_1.appendChild(lng);

			var addressesName = ['address','city','state','postcode','country'];
			var addresses = [address,city,state,postcode,country];
			for (var i = 0, j = addresses.length; i < j; i++) {
				var input = document.createElement( "input" );
				input.type = "hidden";
				input.id = addressesName[i]+link_id;
				input.className = "link"+addressesName[i];
				input.name = addressesName[i]+'['+link_id+']';
				input.value = addresses[i];
	            		cell_1.appendChild(input);
				delete input;
			}
			
			var linkNameText = document.createTextNode(link_name);
			var linkNameSpan = document.createElement("span"); 
			linkNameSpan.className = "link_name"; 
			linkNameSpan.id = "link_name"+link_id; 
			linkNameSpan.title = link_name;
			linkNameSpan.appendChild(linkNameText);
            		cell_1.appendChild(linkNameSpan);
			cell_1.className = 'link_name';

			row.appendChild(cell_1);

            		var cell_2 = document.createElement("td");
			cell_2.className = 'address';
			var cellText_2 = document.createTextNode(getAddressString(address,city,state,postcode,country));

			var statusTag = document.createElement("span"); 
			statusTag.id = "status"+link_id; 
			statusTag.className = "status"; 
			statusTag.style.display = "none"; 
			statusTag.appendChild(cellText_2);
			cell_2.appendChild(statusTag);

			var spanTag = document.createElement("span"); 
			spanTag.id = "fulladdress"+link_id; 
			spanTag.className = "fulladdress"; 
			spanTag.title = getAddressString(address,city,state,postcode,country);
			spanTag.appendChild(cellText_2);
			cell_2.appendChild(spanTag);

			var divEditform = document.createElement("div");
			divEditform.id = 'editform'+link_id;
			divEditform.className = 'editform';
			cell_2.appendChild(divEditform);
			
            		row.appendChild(cell_2);

			grid.appendChild(row);
			
			bindRowEvents(link_id);
		}
		
		function saveCoord() {
			var link_ids = jQuery('.linkcheckbox').serialize();
			var lats = jQuery('.linklat').serialize();
			var lngs = jQuery('.linklng').serialize();
			jQuery.ajax({
			  type: "POST",
			  url: admin_site_url,
			  data: "option=com_mtree&task=geocode&task2=savecoord&tmpl=component&format=json&hide=1&"+link_ids+"&"+lats+"&"+lngs,
			  dataType: "json",
			  success: function(data){
				if(data.status=='OK'){
					jQuery.each(data.data, function(key, link_id) {
						jQuery('#row'+link_id).empty();
						jQuery('#row'+link_id).hide();
						marker[link_id].setVisible(false);
						delete marker[link_id];
					});
					updateSaveLocationsButton();
				}
				}
			});
		}
		
		function saveAddress(link_id,address,city,state,postcode,country) {
			jQuery.ajax({
			  type: "POST",
			  url: admin_site_url,
			  data: "option=com_mtree&task=geocode&task2=saveaddress&tmpl=component&format=json&hide=1&link_id="+link_id+"&address="+address+"&city="+city+"&state="+state+"&postcode="+postcode+"&country="+country,
			  dataType: "json",
			  success: function(data){
				// Do nothing
 			  }
		  });
		}
		
		function bindRowEvents(link_id) {
			jQuery('#grid tr#row'+link_id).hover(function(){
				jQuery(this).parent().css('cursor','hand');
			},function(){
				jQuery(this).parent().css('cursor','pointer');
			});

			jQuery('#grid tr span#fulladdress'+link_id).click(function(){

				if( typeof jQuery('#editformaddress'+link_id).val() == 'undefined' ) {
					jQuery('#editform'+link_id).hide();
					jQuery('#editform'+link_id).html(
						'<span><label for="editformaddress'+link_id+'">Address:</label><input type="text" name="address" value="'+jQuery('input#address'+link_id).val()+'" id="editformaddress'+link_id+'" size="30" /></span>'
						+'<span><label for="editformcity'+link_id+'">City:</label><input type="text" name="city" value="'+jQuery('input#city'+link_id).val()+'" id="editformcity'+link_id+'" size="30" /></span>'
						+'<span><label for="editformstate'+link_id+'">State:</label><input type="text" name="state" value="'+jQuery('input#state'+link_id).val()+'" id="editformstate'+link_id+'" size="30" /></span>'
						+'<span><label for="editformpostcode'+link_id+'">Postcode:</label><input type="text" name="postcode" value="'+jQuery('input#postcode'+link_id).val()+'" id="editformpostcode'+link_id+'" size="30" /></span>'
						+'<span><label for="editformcountry'+link_id+'">Country:</label><input type="text" name="country" value="'+jQuery('input#country'+link_id).val()+'" id="editformcountry'+link_id+'" size="30" /></span>'
						+'<button class="saveaddress btn btn-primary" id="saveaddress'+link_id+'">Save</button> <a class="editformcancel" onclick="javascript:editformcancel('+link_id+')">Cancel</a>'
						+'<br /><br />'
						);
					jQuery('#editform'+link_id).slideDown('fast');
					var addressfield = document.getElementById('editformaddress'+link_id);
					addressfield.focus();
					addressfield.select();
			
					jQuery('#saveaddress'+link_id).click(function(){
						jQuery('#editform'+link_id).slideUp('fast');
						jQuery('input#address'+link_id).val(jQuery('#editformaddress'+link_id).val());
						jQuery('input#city'+link_id).val(jQuery('#editformcity'+link_id).val());
						jQuery('input#state'+link_id).val(jQuery('#editformstate'+link_id).val());
						jQuery('input#postcode'+link_id).val(jQuery('#editformpostcode'+link_id).val());
						jQuery('input#country'+link_id).val(jQuery('#editformcountry'+link_id).val());

						jQuery('span#fulladdress'+link_id).html(
							getAddressString(
								jQuery('#editformaddress'+link_id).val(),
								jQuery('#editformcity'+link_id).val(),
								jQuery('#editformstate'+link_id).val(),
								jQuery('#editformpostcode'+link_id).val(),
								jQuery('#editformcountry'+link_id).val()
							)
						);
						if( jQuery('span#fulladdress'+link_id).hasClass('notfound') ) {
							jQuery('span#fulladdress'+link_id).removeClass('notfound');
							jQuery('#lat'+link_id).val('');
							jQuery('#lng'+link_id).val('');
						}
						saveAddress(link_id,jQuery('#editformaddress'+link_id).val(),jQuery('#editformcity'+link_id).val(),jQuery('#editformstate'+link_id).val(),jQuery('#editformpostcode'+link_id).val(),jQuery('#editformcountry'+link_id).val());
					
					});
				
					jQuery("input[id^='editform']").keypress(function(e){
						if (e.which == 13) {
							var id = jQuery(this).attr("id");
							jQuery('#saveaddress'+link_id).click();
						} 
					});
				} else {
					jQuery('#editform'+link_id).toggle('fast');
				}
				
			});
			
			jQuery('#grid tr#row'+link_id).click(function(){
					jQuery('#grid td').css('background-color','');
					jQuery(this).children('td').css('background-color','#DCE9F8');
					var lat = getLat(link_id);
					var lng = getLng(link_id);
					if( lat != '' && lng != '' ) {
						if(jQuery('#checkbox'+link_id).attr('checked')) {
							openMarkerWindow(link_id);					
						}
					} else {
						// No coordinates
					}
			});
			
			jQuery('#checkbox'+link_id).click(function(){
				updateSaveLocationsButton();
			});
		}

		function updateSaveLocationsButton() {
			var count = 0;
			jQuery('.linkcheckbox').each(function(i){
				var link_id = jQuery(this).attr("id").substr(8);
				if( 
					typeof jQuery('#checkbox'+link_id).attr('disabled') == 'undefined'
					&&
					jQuery('#checkbox'+link_id).attr('checked') == 'checked'
				) {
					count++;
				}
			});
			if( count > 0 ) {
				jQuery('#btnSave').attr('disabled',false);
			} else {
				jQuery('#btnSave').attr('disabled',true);
			}
		}
		
		function getAddressString(address,city,state,postcode,country) {
			var addresses = [address,city,state,postcode,country];
			var address = [];
			for (var i = 0, j = addresses.length; i < j; i++) {
				if( addresses[i] != '' ) {
					address.push(addresses[i]);
				}
			}
			return address.join(', ');
		}
		
		function editformcancel(link_id) {
			jQuery('#editform'+link_id).slideUp('fast');
		}
		
		function placeMarker(link_id,lat,lng) {
			var location = new google.maps.LatLng(lat, lng);
			marker[link_id] = null;
			marker[link_id] = new google.maps.Marker({
				position: location, 
				map: map, 
				draggable: true
			});
			marker[link_id].txt = jQuery('span#link_name'+link_id).html();
			marker[link_id].setPosition(location);
			
			google.maps.event.addListener(marker[link_id],"click",function() 
		   	{ 
				openMarkerWindow(link_id);
		    	});
		    	google.maps.event.addListener(marker[link_id], "dragend", function(){
				var locatedAddress = marker[link_id].getPosition();
				setLatLng(link_id,locatedAddress.lat(),locatedAddress.lng());
			});
		}
		
		function openMarkerWindow(link_id) {
			if(typeof infowindow !== 'undefined'){
				infowindow.close();
			}
			infowindow = new google.maps.InfoWindow({
			    content: marker[link_id].txt
			});
			infowindow.open(map,marker[link_id]);
		}
		function getLat(link_id) {
			return jQuery('#lat'+link_id).val();
		}
		
		function getLng(link_id) {
			return jQuery('#lng'+link_id).val();
		}
		function setLatLng(link_id,lat,lng) {
			setLat(link_id,lat);
			setLng(link_id,lng);
		}
		function setLat(link_id,lat) {
			jQuery('#lat'+link_id).val(lat);
		}
		function setLng(link_id,lng) {
			jQuery('#lng'+link_id).val(lng);
		}
		
	</script>
	
	<div class="row-fluid">
		<div class="span12">
			<fieldset>
			<legend><?php echo JText::_( 'COM_MTREE_STATUS' ) ?></legend>
				<?php echo JText::sprintf( 'COM_MTREE_TOTAL_LISTINGS_IN_DIRECTORY', $total['all'] ) ?>
				<p /><?php echo JText::sprintf( 'COM_MTREE_GEOCODED_LISTINGS', $total['geocoded'] ) ?>
				<p /><?php echo JText::sprintf( 'COM_MTREE_LISTINGS_THAT_HAVENT_BEEN_GEOCODED_BUT_HAVE_ADDRESS_INFORMATION_WITH_THEM_THESE_LISTINGS_CAN_BE_GEOCODED_TO_LOCATE_THEIR_ADDRESS_IN_MAP', $total['req_geocoding'] ) ?>
				<p /><?php echo JText::_( 'COM_MTREE_GEOCODE_INSTRUCTION' ); ?>
			</fieldset>
		</div>
	</div>

	<div class="row-fluid">
		<div class="span6" id="listings">
			<fieldset>
			<legend><?php echo JText::_( 'COM_MTREE_LISTINGS' ); ?> (<?php echo $total['req_geocoding']; ?>)</legend>
			<?php if(!empty($listings)) { ?>

			<button id="btnGeocode" class="btn btn-primary">Geocode</button>
			<button onclick="javascript:saveCoord();return false;" id="btnSave" class="btn" disabled><i class="icon-checkmark"></i> Save Locations</button>

			<div style="margin-top:5px; height:;  overflow:show; display:block;float:left;clear:none">
			<table width="100%" border=0 cellpadding=0 cellspacing=0>
				<tbody id="grid">
				</tbody>
				<tfoot><tr><td colspan="2" align="right">
					<button onclick="javascript:getListings(document.getElementById('numberOfListings').value);" class="btn" style="float:right;margin-left:10px"><i class="icon-plus-2 small"></i> Go</button>
					<select id="numberOfListings" style="float:right">
						<option value="25"><?php echo JText::sprintf('COM_MTREE_LOAD_N_LISTINGS',25); ?></option>
						<option value="50"><?php echo JText::sprintf('COM_MTREE_LOAD_N_LISTINGS',50); ?></option>
						<option value="100" selected><?php echo JText::sprintf('COM_MTREE_LOAD_N_LISTINGS',100); ?></option>
						<option value="250"><?php echo JText::sprintf('COM_MTREE_LOAD_N_LISTINGS',250); ?></option>
						<option value="500"><?php echo JText::sprintf('COM_MTREE_LOAD_N_LISTINGS',500); ?></option>
						<option value="1000"><?php echo JText::sprintf('COM_MTREE_LOAD_N_LISTINGS',1000); ?></option>
					</select>
					<span id="geocodeMessage"></span>
				</td></tr></tfoot>
			</table>
			</div>
			<?php } ?>
			</fieldset>
		</div>
		<div class="span6">
			<div class="row-fluid">
				<div class="span12" style="position:relative;height:500px;width:100%">
					<div id="map" style="margin-top:36px;width:100%;height:100%;position:absolute"></div>
				</div>
			</div>
		</div>
	</div>
	<?php
	}

	public static function incompatible_browser() {
		?>
		<h1><?php echo JText::_( 'COM_MTREE_UNSUPPORTED_BROWSER' ); ?></h1>
		<?php echo JText::_( 'COM_MTREE_THIS_FEATURE_REQUIRES_FIREFOX_3_OR_LATER_TO_OPERATE' ); ?>
		<?php
	}
}
?>