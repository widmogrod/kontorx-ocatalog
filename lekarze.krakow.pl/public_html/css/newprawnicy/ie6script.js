/* 
	(c) All rights reserved 
*/
(function(){
	DD_belatedPNG.fix('.logo, #topNav a, .row h4, .mapRank, .prev a, .next a');
	$('#content li').hover(function(){
		$(this).addClass('liHover');
	}, function(){
		$(this).removeClass('liHover');
	});
})(jQuery);
