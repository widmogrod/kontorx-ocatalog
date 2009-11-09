// biblioteka wymagana!
var loader = new JLoader();

function JStats() {
	var self = this;

	this.load = function(date) {
		this.loadVisits(date);
		this.loadBrowser(date);
		this.loadPage(date);
	};

	this.loadVisits = function(date) {
		var data = loader.load('/stats/stats/list/date/' + date);
		self.renderVisits(data);
	};

	this.renderVisits = function(data) {
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
	};

	this.loadBrowser = function(date) {
		var data = loader.load('/stats/stats/listBrowserVisit/date/' + date);
		self.renderBrowser(data);
	};
	
	this.renderBrowser = function(json) {
		var table = $('#stat-browser-visit tbody');
		$(json).each(function(k,i){
			var tr = "<tr>"+
				"<td>"+i.browser+"</td>" +
				"<td>"+i.count+"</td>" +
			"</tr>";
			table.append(tr);
		});
	};

	this.loadPage = function(date) {
		var data = loader.load('/stats/stats/listPageVisit/date/' + date);
		self.renderPage(data);
	};
	
	this.renderPage = function(json) {
		var table = $('#stat-page-visit tbody');
		$(json).each(function(k,i){
			var tr = "<tr>"+
				"<td><a href=\""+i.url+"\">/"+i.url+"</a></td>" +
				"<td>"+i.count+"</td>" +
			"</tr>";
			table.append(tr);
		});
	};
};