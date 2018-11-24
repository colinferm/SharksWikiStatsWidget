SFS.Chart.Pie.seasonInvestmentByType = function(data, id, chartTitle, categories) {
    if (categories) {
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
                        var dataitem = dataset.data[tooltipItem.index];
                        var label = data.labels[tooltipItem.index];
                        //console.log(dataset);
                        if (dataset.label == 'amount') {
                            return  label + ': $' + dataitem.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        }
                        return label + ': ' + dataitem.toString();
                    }
                }
            }
        },
        data: data
    });
}

SFS.Chart.Pie.sharkInvestmentTotals = function(investmentAmountData, id, chartTitle, categories) {
    if (categories) {
		var formatted = SFS.Utils.formatCategoryNames(categories);
		chartTitle += " - " + formatted;
    }
    
    var byAmtCTX = document.getElementById(id).getContext("2d");
    window.investAmtPieChart = new Chart(byAmtCTX, {
         type: 'doughnut',
         options: {
             title:{
                 display:true,
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
                        
                        if (tooltipItem.datasetIndex == 0) {
                            return  label + ': $' + dataitem.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        } else {
                            return label + ': ' + dataitem.toString();
                        }
                     }
                 }
             }
         },
         data: investmentAmountData
    });
}