SFS.Chart.Bubble.seasonInvestmentByType = function(data, id, chartTitle, categories) {
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
						suggestedMax: 30
					}
				}]
			}
		},
	});
}

