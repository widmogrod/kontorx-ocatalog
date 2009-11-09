$(document).ready(function(){
	// działanie AJAX
	$('#loader')
		.ajaxStart(function(){$(this).show()})
		.ajaxStop(function(){$(this).hide()});
});

/**
 * JLoader
 *
 * Ułatwia pobieranie danych via AJAX
 */
function JLoader (){
	this.load = function(url) {
		var data = {};
		$.ajax({
			'url': 		url,
			'async':		false,
			'data': 		{'format':'json'},
			'dataType': 	'json',
			'success':	function(json){
				data = json;
				if (data.success != 'undefined') {
					if (data.success == true &&
							data.rowset typeof 'object') {
						data = data.rowset;
					} else {
						data = {};
					}
				}
			}
		});
		return data;
	};
};