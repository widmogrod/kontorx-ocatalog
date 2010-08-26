/**
 * Filtrowanie elementów pola select
 *
 * TODO: + dodac filtrowanie nierozpoznające wielkość znaków
 *
 * @author Gabriel, widmogrod@gmail.com
 * @version 0.2
 * @license LGPL
 */
(function($) {
	$.fn.selectFilter = function(settings) {
		var config = {
			'length': 50, 	// ilość elementów "option", od której rozpocząć filtrowanie elementu "select"
			'size':10		// wielkość pola "select" (widoczne elementy)
		};
 
		if (settings) $.extend(config, settings);

		var _filter = function _filter(event){
			var search = $(this).val()
			
			if (search == '') {
				$(event.data.select).find('option').show();
			} else {
				$(event.data.select).find('option').hide();
				$(event.data.select).find('option:contains('+ search +')').show();
			}
		};
 
		this.each(function(k, item) {
			// tylko elementy select
			if (!$(item).is('select'))
				return;

			// tylko jeżeli liczba opcji wyboru jest większa niż
			if ($(item).find('option').size() < config.length)
				return;
				
			// ustaw widoczne elementy tylko wtedy gdy nie zdefiniowano
			if (!$(item).attr('size'))
				$(item).attr('size', config.size)

			// element owijajacy
			var wrapper = $('<div/>');
			wrapper.addClass('kx_searchFilter');
			
			
			// pole tekstowe filtrujące
			var element = $('<input type="text" />');
			element.addClass('kx_searchFilter_search');
			element.bind('keyup', {select:item}, _filter);
			
			$(item).addClass('kx_searchFilter_list');

			$(item).wrap(wrapper);
			$(item).before(element);
		});
 
		return this;
 
	};

 })(jQuery);
