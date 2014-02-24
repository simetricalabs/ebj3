function mapinitialize() {
	if( linkValLng != 0 && linkValLat != 0 ) {
		var mapLatlng = new google.maps.LatLng(linkValLat, linkValLng);
		var zoom = linkValZoom;
	} else {
		var mapLatlng = new google.maps.LatLng(defaultLat, defaultLng);
		var zoom = defaultZoom;
	}

	var mapOptions = {
		zoom: parseInt(zoom),
		center: mapLatlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	map = new google.maps.Map(document.getElementById("map"), mapOptions);
	marker = new google.maps.Marker({
		position: mapLatlng, 
		map: map, 
		draggable: true
	});
	
	geocoder = new google.maps.Geocoder();

	google.maps.event.addListener(marker, "dragend", function(){updateField(marker);});
    google.maps.event.addListener(map, "zoom_changed", function(){updateField(marker);});
    google.maps.event.addListener(marker, "dragstart", function(){closeinfowindow();});
    google.maps.event.addListener(marker, "dragstart", function(){closeinfowindow();});
}
function closeinfowindow() {
	if(typeof infowindow !== 'undefined' && infowindow !== null) {
		infowindow.close()
	}
}
function showAddress(address) {
  geocoder.geocode(
    {'address':address},
    function(results,status) {
		jQuery('#locateButton').val(Joomla.JText._('COM_MTREE_LOCATE_IN_MAP'));
		jQuery('#locateButton').attr('disabled',false);

		if (status == google.maps.GeocoderStatus.OK) {
			map.setCenter(results[0].geometry.location);
			map.setZoom(13);
			marker.setPosition(results[0].geometry.location);
			
			infowindow = new google.maps.InfoWindow({
			    content: address
			});
			infowindow.open(map,marker);
			updateField(marker);
	        
		} else {
			alert(Joomla.JText._('COM_MTREE_GEOCODER_NOT_OK') + address);
		}
    }
  );
}			
function locateInMap() {
	jQuery('#locateButton').val(Joomla.JText._('COM_MTREE_LOCATING'));
	jQuery('#locateButton').attr('disabled',true);
	jQuery('#locateButton').css('font-weight','normal');
	showAddress(getAddress());
}
function getAddress() {
	var city;
	var state;
	var country;
	var postcode;
	if(typeof(jQuery('#cf7').val()) != 'undefined' && jQuery('#cf7').val() != ''){country=jQuery('#cf7').val();}
	else {country = defaultCountry;}
	if(typeof(jQuery('#cf6').val()) != 'undefined' && jQuery('#cf6').val() != ''){state=jQuery('#cf6').val();}
	else {state = defaultState;}
	if(typeof(jQuery('#cf5').val()) != 'undefined' && jQuery('#cf5').val() != ''){city=jQuery('#cf5').val();}
	else {city = defaultCity;}

	if(typeof(jQuery('#cf8').val()) == 'undefined') {
		postcode = '';
	} else {
		postcode = jQuery('#cf8').val();
	}
	var address = new Array(jQuery('#cf4').val(),city,state,postcode,country);
	var val = null;
	for(var i=0;i<address.length;i++){
		if(address[i] != '') {
			if(val == null) {
				val = address[i];
			} else {
				val += ', ' + address[i];
			}
		}
	}
	return val;
}
function updateMapAddress() {
	jQuery('#map-msg').html(getAddress());
	if(jQuery('#cf4').val() == '' && jQuery('#cf5').val() == '' && jQuery('#cf6').val() == '' && jQuery('#cf7').val() == '' && jQuery('#cf8').val() == '') {
		jQuery('#locateButton').css('font-weight','normal');
		jQuery('#locateButton').attr('disabled',true);
	} else {
		jQuery('#locateButton').css('font-weight','bold');
		jQuery('#locateButton').attr('disabled',false);
	}
}
function updateField(marker) {
	var locatedAddress = marker.getPosition();
	jQuery('#lat').val(locatedAddress.lat());
	jQuery('#lng').val(locatedAddress.lng());
	jQuery('#zoom').val(marker.getMap().getZoom());
}
jQuery(document).unload(function(){GUnload();});
jQuery(document).ready(function(){
	updateMapAddress();
	jQuery('#locateButton').css('font-weight','normal');
	if(linkValLat == 0 || linkValLng == 0) {
		jQuery('#map-msg').html(Joomla.JText._('COM_MTREE_ENTER_AN_ADDRESS_AND_PRESS_LOCATE_IN_MAP_OR_MOVE_THE_RED_MARKER_TO_THE_LOCATION_IN_THE_MAP_BELOW'));
	}
	mapinitialize();
	jQuery('#cf4,#cf5,#cf6,#cf7,#cf8').change(function(){
		updateMapAddress();
	});
	jQuery('#cf4,#cf5,#cf6,#cf7,#cf8').keyup(function(){
		updateMapAddress();
	});
    jQuery('a[data-toggle="tab"]').on('shown', function (e) {
        if(e.target.hash=='#listing-map')
        {
            google.maps.event.trigger(map, "resize");
        }
    });

});