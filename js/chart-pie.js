SFS.Chart.Pie.seasonInvestmentByType = function(data, id, chartTitle, categories) {
	if (!chartTitle && categories) {
		var formatted = SFS.Utils.formatCategoryNames(categories);
		chartTitle += " - " + formatted;
	}
    var byTypeCTX = document.getElementById(id).getContext("2d");
    window.dealTypePieChart = new Chart(byTypeCTX, {
        type: 'doughnut',
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
                        //console.log(dataset);
                        if (dataset.label == 'amount') {
                            var dataItemNumber = Number(dataItem);
                            return  label + ': $' + dataItemNumber.toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        }
                        return label + ': ' + dataItem.toString();
                    }
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
    
    var byAmtCTX = document.getElementById(id).getContext("2d");
    window.investAmtPieChart = new Chart(byAmtCTX, {
         type: 'doughnut',
         options: {
             title:{
                 display: true,
                 text: chartTitle
             },
             noDataText: "Not enough deals in the dataset create a chart",
             responsive: true,
             tooltips: {
                 callbacks: {
                     label: function(tooltipItem, data){
                        var dataset = data.datasets[tooltipItem.datasetIndex];
                        var dataItem = dataset.data[tooltipItem.index];
                        var label = data.labels[tooltipItem.index];
                        
                        if (tooltipItem.datasetIndex == 0) {
                            var dataItemNumber = Number(dataItem);
                            return  label + ': $' + dataItemNumber.toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        } else {
                            return label + ': ' + dataItem.toString();
                        }
                     }
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
    
    var byAmtCTX = document.getElementById(id).getContext("2d");
    window.investAmtPieChart = new Chart(byAmtCTX, {
         type: 'doughnut',
         options: {
             title:{
                 display: true,
                 text: chartTitle
             },
             noDataText: "Not enough deals in the dataset create a chart",
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
             hover: {
                onHover: SFS.Utils.Plugins.hoverHandler
            },
            onClick: SFS.Utils.Plugins.clickHandler
         },
         data: teampUpData
    });
}