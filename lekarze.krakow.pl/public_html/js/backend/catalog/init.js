$(document).ready(function(){
	$('.datepicker').datepicker({
		dateFormat: 'yy-mm-dd'
	});
	
	$('.timepicker').timepickr({
        convention: 24
    });
});