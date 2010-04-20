/* 
	(c) All rights reserved 
*/

$(document).ready(function(){

	$('#nav a').hover(function(){
		$(this).fadeTo(0, 0, function(){
			$(this).fadeTo("slow", 1);
		});
	});
	
});
