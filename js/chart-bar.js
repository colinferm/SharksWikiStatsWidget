SFS.Chart.Bar.investmentsByShark = function(data, id, chartTitle, categories) {
	if (categories) {
		var formatted = SFS.Utils.formatCategoryNames(categories);
		chartTitle += " - " + formatted;
	}
    var bySharkCTX = document.getElementById(id).getContext("2d");
    window.investmentBySharkChart = new Chart(bySharkCTX, {
        type: "bar",
        data: data,
        options: {
            title:{
                display: true,
                text: chartTitle
            },
            tooltips: {
                mode: "index",
                intersect: false
            },
            responsive: true,
            scales: {
                xAxes: [{
                	stacked: true,
                	ticks: {
	                	source: 'labels',
	                	beginAtZero: true,
	                	autoSkip: false
                	}
                }],
                yAxes: [{
                	stacked: true
                }]
            },
            categoryPercentage: 1.0,
            barPercentage: 1.0,
            hover: {
                onHover: SFS.Utils.Plugins.hoverHandler
            },
            onClick: SFS.Utils.Plugins.clickHandler
        }
    });
}