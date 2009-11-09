// biblioteka wymagana!
var loader = new JLoader();

function JGoogle() {
	var self = this;

	this.load = function(site,date) {
		this.loadKeywords(site,date);
	};

	this.loadKeywords = function(site,date) {
		var data = loader.load('/stats/google/stats/date/' + date + '/site_id/' + site + '/format/json');
		self.renderKeywords(data);
	};

	this.renderKeywords = function(data) {
		var options = {
			xaxis: {
				mode: "time",
				timeformat: "%d %b",
				monthNames: ["Styczeń", "Luty", "Marzec", "Kwiecień", "Maj", "Czerwiec", "Lipiec", "Sierpień", "Wrzesień", "Październik", "Listopad", "Grudzień"]
			},
			selection: {
				mode: "x"
			},
			lines: {
				show: true,
				fill: false
			},
		    points: {
		    	show: true,
		    	fill: false
		    },
			legend: {
				"width":"100px"
			},
			grid: {
				clickable: true
			}
		};
		
		$.plot(
			$("#placeholder"),
			data,
			options
		);
		
		$("#placeholder").bind("plotclick", function (e, pos) {
	        // the values are in pos.x and pos.y
	        $("#result").html('<b>' + pos.y.toFixed(0) + '</b>');
	    });
		
	};
};