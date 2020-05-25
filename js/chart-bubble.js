SFS.Chart.Bubble.seasonInvestmentByType = function(data, id, chartTitle, categories, verticalTicks) {
	if (verticalTicks == undefined) verticalTicks = 0;
	//console.log(data.datasets);
	for (var i = 0; i < data.datasets.length; i++) {
		for (var ii = 0; ii < data.datasets[i].data.length; ii++) {
			verticalTicks = Math.max(verticalTicks, data.datasets[i].data[ii].y);
		}
		var label = data.datasets[i].label;
		//console.log("Shark " + label + " has max " + verticalTicks + " ticks.");
	}
	//console.log("Max ticks: " + verticalTicks);
	var tickSteps = 1;
	if (verticalTicks > 10) tickSteps = 5;
	verticalTicks = Math.round((verticalTicks * 0.2) + verticalTicks);
	//console.log("Setting ticks: " + verticalTicks);

	var bubbleCTX = document.getElementById(id).getContext("2d");
	window.investmentPerSeasonBubble = new Chart(bubbleCTX, {
		type: 'bubble',
		data: data,
		options: {
			title: {
				display:true,
				text: chartTitle,
			},
			tooltips: {
				custom: SFS.Utils.SeasonInvestmentByTypeBubbleToolTips,
			},
			hover: {
				onHover: SFS.Utils.Plugins.hoverHandler,
				mode: 'nearest',
				intersect: true
			},
			scales: {
				xAxes: [{
					stacked: false,
						scaleLabel: {
						labelString: 'Seasons',
						display: false
					},
					ticks: {
						stepSize: 1,
						callback: function(value, index, values) {
							return "Season " + value;
						}
					}
				}],
				yAxes: [{
					stacked: false,
					scaleLabel: {
						labelString: '# of Deals',
						display: true
					},
					ticks: {
						stepSize: tickSteps,
						suggestedMax: verticalTicks
					}
				}]
			},
			onClick: SFS.Utils.Plugins.clickHandler
		},
	});
}

