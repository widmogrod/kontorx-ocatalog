function ucwords( str ) {
	str = str.toLowerCase(); 
    return (str+'').replace(/^(.)|\s(.)/g, function ( jQuery1 ) { return jQuery1.toUpperCase ( ); } );
}

var meta_title, meta_description, meta_keywords;

function setTitle(value) {
	document.getElementById('meta_title').value = value;
}

function addDescription(value) {
	var el = document.getElementById('meta_description');
	value = el.value + ', ' + value;
	el.value = value.replace(/^[\-,\s]+/,'');
}

function addKeywords(value) {
	var el = document.getElementById('meta_keywords');
	value = el.value + ', ' + value;
	el.value = value.replace(/^[\-,\s]+/,'');

//	console.log(value, meta_keywords);
//	value =  value + ',' + meta_keywords.val();
//	jQuery(meta_keywords).val(value.replace(/\s/g,','));
}

/**
 * Wypełnianie pol meta_tile etc.
 */
jQuery(document).ready(function(){
	meta_title 		= jQuery('#meta_title');
	meta_description= jQuery('#meta_description');
	meta_keywords 	= jQuery('#meta_keywords');

	var init = jQuery('<input type="button"/>')
		.val('Wypełnij pola!')
		.addClass('action add')
		.click(function(){
			// czyszczenie
			document.getElementById('meta_title').value = '';
			document.getElementById('meta_description').value = '';
			document.getElementById('meta_keywords').value = '';

			var value;
			valueName = jQuery('#name').val();
			valueName = ucwords(valueName);
			
			setTitle(valueName);
			addKeywords(valueName);
			addDescription(valueName);
			
			jQuery('#name').val(valueName);

			valueAdress = jQuery('#adress').val();
			
			addDescription(valueAdress);
			addKeywords(valueAdress);
			
			valueDistricts = jQuery('#catalog_district_id :selected').text();

			addKeywords(valueDistricts);
			addDescription(valueDistricts);
			
		});

	// dodaje odpowiedni przycisk w formularzu!
	jQuery('#fieldset-pozycjonowanie').prepend(init);
});