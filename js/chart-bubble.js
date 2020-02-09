SFS.Chart.Bubble.seasonInvestmentByType = function(data, id, chartTitle, categories, verticalTicks) {
	if (verticalTicks == undefined || verticalTicks == 0) verticalTicks = 30;
	var tickSteps = 5;
	if (verticalTicks < 10) tickSteps = 1;
	console.log("Ticks: " + verticalTicks + ", Steps: " + tickSteps);

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

