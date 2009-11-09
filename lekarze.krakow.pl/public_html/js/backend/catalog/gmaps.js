/**
 * Google Maps, jQuery dialog + selecting lat lang! 
 */
jQuery(document).ready(function(){
	var lngDiv = jQuery('#lng');
	var latDiv = jQuery('#lat');
	var mapDiv = null;
	
	var setLatlng = function(latlng) {
		lngDiv.val(latlng.lng());
		latDiv.val(latlng.lat());
	}

	var getLatlng = function() {
		var lat = latDiv.val();
		var lng = lngDiv.val();

		if (parseInt(lat) == 0 || parseInt(lng) == 0) {
			// wspolrzedne Krakowa
			lat = 50.03950183762877;
			lng = 19.9072265625;
		}

		return new GLatLng(lat, lng);
	}

	var focusGMap = function focusGMap() {
		if (GBrowserIsCompatible()) {
			jQuery('#dialog').dialog('open');
			

			var map = new GMap2(document.getElementById("map"));
			map.addControl(new GSmallMapControl());
	        map.addControl(new GMapTypeControl());

			var center = getLatlng();
			map.setCenter(center, 13);

			var marker = new GMarker(center, {draggable: true});
			
			GEvent.addListener(marker,'dragend',setLatlng);
			
			GEvent.addListener(marker,'dblclick',setLatlng);
	
			map.addOverlay(marker);
	        
	        // geolokalizacja
	        var geocoder = new GClientGeocoder();
	        jQuery('#geolocal').click(function(){
	        	var address = jQuery('#adress').val();
	        	address = address == undefined ? '' : address;
	        	var address = jQuery('#geo-adress-city').val() + ', ' + address;

	        	if (geocoder) {
	        		geocoder.getLatLng(
	        			address,
	        			function(latlng) {
	        				if (!latlng) {
	        					alert(address + ', nie znaleziono!');
	        				} else {
	        					// usuwamy stary marker
	        					map.removeOverlay(marker);
	        					// centrujemy mape
	        					map.setCenter(latlng, 13);
	        					// ustaw parker o nowej pozycji
	        					marker.setLatLng(latlng);
	        					setLatlng(latlng);
	        					map.addOverlay(marker);
	        					// okienko z adresem!
	        					marker.openInfoWindowHtml(address);
	        				}
	        			}
	        		);
	        	}
	        });	        	
		}
	}

	lngDiv.click(focusGMap);
	latDiv.click(focusGMap);
	
	// dodanie potrzebnego div'a do <body/>
	jQuery('body').append('<div id="dialog" style="display:none">'+
		'<div id="map" style="width:550px;height:370px;"></div>'+
		'<div style="clear:left;">'+
			'<input id="geo-adress-city" type="text" value="Kraków" />'+
			'<input id="geolocal" type="button" value="Zlokalizuj adres!" />'+
			'<p class="small bottom">Ogranicz obszar lokalizacji do w/w miasta!</p>'+
		'</div></div>');
	
	jQuery('#dialog').dialog({
		'modal':false,
		'width':600,
		'height':510,
	}).dialog('close');
});