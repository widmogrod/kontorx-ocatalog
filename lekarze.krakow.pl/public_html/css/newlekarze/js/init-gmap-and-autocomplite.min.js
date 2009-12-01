/**
 * Copyrights: P.W Promotor, Kraków
 */

/**
 * Init autocomplite 
 */
(function($){
	$('#loader')
		.ajaxStart(function(){
			$(this).show();
		})
		.ajaxStop(function(){
			$(this).hide();
		});

	$('#mapForm').submit(function(){
		showAddress($('#autocomplete-adres').val());
		return false;
	});

	$("#autocomplete-adres")
		.keydown(function() {
			$(this).addClass('loader');
		})
		.keyup(function() {
			$(this).removeClass('loader');
		});


	$("#autocomplete-adres").autocomplete({
		url: '/catalog/list/autocompleteadress',
		dataType: 'json',
		delay: 450,
		autoFill:true,
		parse: function(a) {
			var result = [];
			$(a).each(function(k,i){
				result.push({
					data: i,
					value: i.adress,
					result: i.adress
				});
			});
			return result;
		},
		formatItem: function(data, idx, total) {
			return data.adress;
		},
		selected: function(e, ui) {
			/*console.log(e, ui);*/
		}
	});
})(jQuery);

/**
 * Init GMap 
 */
var map = null;
(function($){
	if (GBrowserIsCompatible()) {
		map = new GMap2(document.getElementById("googleMaps"));
		// Scroll whell ON
		map.enableScrollWheelZoom();
		// Kraków
		map.setCenter(new GLatLng(50.0646501,19.9449799), 12);
		// Controll
		map.addControl(new GLargeMapControl());
	    map.addControl(new GMapTypeControl());

	    // init marker manager
	    mgr = new MarkerManager(map);
	    
	    // init geocoder client
	    geocoder = new GClientGeocoder();

		$.ajax({
			url: 'data/gmap.json',
			dataType: 'json',
			success: function (data, textStatus) {
				var markers = {
					premium : [],
					medium : [],
		           	standard : [],
		           	'default': []
				};

				// TODO: tutaj teoretycznie może być buba!
				var base_url = window.location.protocol.replace(/(:\/\/|:)/,'');
				base_url += '://';
				base_url += window.location.hostname;
				
				$(data).each(function(k,i){
					var latlng = new GLatLng(i.lat, i.lng);
					var type = i.catalog_promo_type_id;
					var options = {
						icon : getGIcon(type),
						title : i.name 
					};

					var marker = new GMarker(latlng, options);

					var html = '<b>'+ i.name +'</b><br/>' +
								'<a href="'+ base_url +'/lekarz/'+ i.id +'"> zobacz więcej &raquo;</a>';
					
					marker.bindInfoWindowHtml(html, {
						pixelOffset : new GSize(100, 20)
					});

					switch(type) {
						case '1': type = 'standard'; break;
						case '2': type = 'medium'; break;
						case '3': type = 'premium'; break;
						default: type = 'default'; break;
					}

					markers[type].push(marker);
				});

				// refresh marker manager
				mgr.addMarkers(markers.premium, 9);  //9
				mgr.addMarkers(markers.medium, 9);  //11
				mgr.addMarkers(markers.standard, 9);//13
				mgr.addMarkers(markers['default'], 10); //14
				mgr.refresh();
			},
			error: function (XMLHttpRequest, textStatus, errorThrown) {
				
			}
		});
	}
})(jQuery);

var iconType = []
                
/**
 * Return {@see GIcon}
 * @param integer type
 * @return GIcon
 */
function getGIcon(type) {
	switch(type) {
		case '1': type = 'standard'; break;
		case '2': type = 'medium'; break;
		case '3': type = 'premium'; break;
		default: type = 'default'; break;
	}

	if (!iconType[type]) {
		iconType[type] = new GIcon();
		iconType[type].image = 'css/newlekarze/img/gmap/marker-'+ type +'.png';
		iconType[type].iconSize = new GSize(45,45);
		iconType[type].iconAnchor = new GPoint(25,25);
		iconType[type].infoWindowAnchor = new GPoint(25,25);
	}
	return iconType[type];
}

/**
 * Show addres on GMap
 * @param string address
 * @return void
 */
function showAddress(address, zoom) {
	if (G_MAP_LAT != undefined && G_MAP_LNG != undefined) {
		if (parseInt(G_MAP_LAT) != 0 || parseInt(G_MAP_LNG) != 0) {
			var latlng = new GLatLng(G_MAP_LAT, G_MAP_LNG);
			map.setCenter(latlng, zoom);
			
			return;
		}
	}

	address = 'Kraków, ' + address;
	if (geocoder) {
		geocoder.getLatLng(
			address,
			function(point) {
				if (!point) {
					alert(address + " not found");
				} else {
					map.setCenter(point, zoom == undefined ? 14 : parseInt(zoom));
				}
			});
	}
}