SFS.Chart.Pie.seasonInvestmentByType = function(data, id, chartTitle, categories) {
	if (!chartTitle && categories) {
		var formatted = SFS.Utils.formatCategoryNames(categories);
		chartTitle += " - " + formatted;
	}
	
	data.datasets[1].datalabels = {
		anchor: 'center'
	}
	
	var byTypeCTX = document.getElementById(id).getContext("2d");
	byTypeCTX.canvas.height = byTypeCTX.canvas.width;
	window.dealTypePieChart = new Chart(byTypeCTX, {
		type: 'doughnut',
		plugins: [ChartDataLabels],
		options: {
			title:{
				display: true,
				text: chartTitle
			},
			responsive: true,
			tooltips: {
				callbacks: {
					label: function(tooltipItem, data){
						var dataset = data.datasets[tooltipItem.datasetIndex];
						var dataItem = dataset.data[tooltipItem.index];
						var label = data.labels[tooltipItem.index];
						if (dataset.label == 'amount') {
							return  label + ': $' + SFS.Utils.formatMoneyValue(dataItem);
						}
						return label + ': ' + dataItem.toString();
					}
				}
			},
			plugins: {
				datalabels: {
					backgroundColor: function(context) {
						return context.dataset.backgroundColor;
					},
					borderColor: 'white',
					borderRadius: 25,
					borderWidth: 2,
					color: 'white',
					display: function(context) {
						var dataset = context.dataset;
						var count = dataset.data.length;
						var value = dataset.data[context.dataIndex];
						return value >= count;
					},
					font: {
						weight: 'bold'
					},
					formatter: function(value, context) {
						console.log("seasonInvestmentByType");
						if (context.datasetIndex == 0) {
							return '$' + SFS.Utils.formatMoneyValue(value);
						}
						return value;
					},
					anchor: 'end'
				}
			},
			hover: {
				onHover: SFS.Utils.Plugins.hoverHandler
			},
			onClick: SFS.Utils.Plugins.clickHandler
		},
		data: data
	});
}

SFS.Chart.Pie.sharkInvestmentTotals = function(investmentAmountData, id, chartTitle, categories) {
	if (!chartTitle && categories) {
		var formatted = SFS.Utils.formatCategoryNames(categories);
		chartTitle += " - " + formatted;
	}
	
	//console.log(investmentAmountData);
	investmentAmountData.datasets[1].datalabels = {
		anchor: 'center'
	}
	
	var byAmtCTX = document.getElementById(id).getContext("2d");
	byAmtCTX.canvas.height = byAmtCTX.canvas.width;
	window.investAmtPieChart = new Chart(byAmtCTX, {
		type: 'doughnut',
		plugins: [ChartDataLabels],
		maintainAspectRatio: false,
		options: {
			title:{
				display: true,
				text: chartTitle
			},
			noDataText: "Not enough deals in this dataset yet to create a chart",
			responsive: true,
			tooltips: {
				callbacks: {
					label: function(tooltipItem, data){
						var dataset = data.datasets[tooltipItem.datasetIndex];
						var dataItem = dataset.data[tooltipItem.index];
						var label = data.labels[tooltipItem.index];
						
						if (tooltipItem.datasetIndex == 0) {
							return  label + ': $' + SFS.Utils.formatMoneyValue(dataItem);
						} else {
							return label + ': ' + dataItem.toString();
						}
					}
				}
			},
			plugins: {
				datalabels: {
					backgroundColor: function(context) {
						return context.dataset.backgroundColor;
					},
					borderColor: 'white',
					borderRadius: 25,
					borderWidth: 2,
					color: 'white',
					display: function(context) {
						//console.log(context);
						var dataset = context.dataset;
						var count = dataset.data.length;
						var value = dataset.data[context.dataIndex];
						var sum = SFS.Utils.sumArray(context.dataset.data);
						if (context.datasetIndex == 1) {
							var avg = (sum / count) * 0.5;
							return value >= avg;
							
						} else {
							var avg = (sum / count) * 0.5;
							console.log("Sum: " + sum + ", Avg: " + avg + ", Value: " + value);
							return value >= avg;
						}
					},
					font: {
						weight: 'bold'
					},
					formatter: function(value, context) {
						console.log("sharkInvestmentTotals");
						if (context.datasetIndex == 0) {
							return '$' + SFS.Utils.formatMoneyValue(value);
						}
						return value;
					},
					anchor: 'end'
				}
			},
			hover: {
				onHover: SFS.Utils.Plugins.hoverHandler
			},
			onClick: SFS.Utils.Plugins.clickHandler
		},
		data: investmentAmountData
	});
}

SFS.Chart.Pie.sharkTeamUps = function(teampUpData, id, chartTitle, categories) {
	if (categories) {
		var formatted = SFS.Utils.formatCategoryNames(categories);
		chartTitle += " - " + formatted;
	}
    
	var teamUpCTX = document.getElementById(id).getContext("2d");
	teamUpCTX.canvas.height = teamUpCTX.canvas.width;
	window.teamupsPieChart = new Chart(teamUpCTX, {
		type: 'doughnut',
		plugins: [ChartDataLabels],
			options: {
				title:{
					display: true,
					text: chartTitle
				},
				noDataText: "Not enough deals in this dataset yet to create a chart",
				responsive: true,
				tooltips: {
					callbacks: {
						label: function(tooltipItem, data){
							var dataset = data.datasets[tooltipItem.datasetIndex];
							var dataitem = dataset.data[tooltipItem.index];
							var label = data.labels[tooltipItem.index];
							return label + ': ' + dataitem.toString();
						}
					}
				},
				plugins: {
					datalabels: {
						backgroundColor: function(context) {
							return context.dataset.backgroundColor;
						},
						borderColor: 'white',
						borderRadius: 25,
						borderWidth: 2,
						color: 'white',
						display: function(context) {
							console.log("sharkTeamUps");
							var dataset = context.dataset;
							var count = dataset.data.length;
							var value = dataset.data[context.dataIndex];
							return value > count * 1.5;
						},
						font: {
							weight: 'bold'
						},
						formatter: Math.round
					}
				},
				hover: {
					onHover: SFS.Utils.Plugins.hoverHandler
				},
				onClick: SFS.Utils.Plugins.clickHandler
			},
		data: teampUpData
	});
}
