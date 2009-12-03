var time_blure = 15000; // 10 sekund
$(document).ready(function(){
	$('#flash-messages li')
		.hover(function(){
			$(this).hide();
		},function(){
			$(this).show(10000);
		})
		.each(function(k,i) {
			// dzielenie dlugoego tekstu
			var s = 100;
	
			if (i.innerHTML.length > s) {
				var a1 = i.innerHTML.slice(0, s);
				var a2 = i.innerHTML.slice(s, i.innerHTML.length);
				
				a2 = $('<span/>').html(a2).hide();
				a1 = $('<span/>').html(a1);
				
				$(i).click(function(){
					if (this.show) {
						this.show = false;
						a2.hide();
					} else {
						this.show = true;
						a2.show();
					}
				});
				
				
				$(i)
					.text('')
					.append(a1)
					.append(a2);
			}
	
			// przycisk zamknij
			var close = $('<span/>')
				.addClass('close')
				.text('x')
				.click(function(){
					$(i).hide();
					return false;
				});
		
			$(i).prepend(close);
			
	//		setTimeout(function(){
	//			$(i).fadeOut('slow');
	//		}, time_blure * (k+1))
		});
});