var SFS = SFS || {
    Chart: {
        Bar: {},
        Bubble: {},
        Line: {},
        Pie: {}
    },
    Constant: {
        colorOne: 'rgba( 87,151,193,1)',
        colorTwo: 'rgba(178,214,238,1)',
        colorThree: 'rgba(127,182,217,1)',
        colorFour: 'rgba( 56,123,166,1)',
        colorFive: 'rgba( 31,103,149,1)',
        colorSecondaryOne: 'rgba(101,106,202,1)',
        colorSecondaryTwo: 'rgba( 71,77,180,1)',
        colorSecondaryThree: 'rgba( 45,51,162,1)',
        colorSecondaryFour: 'rgba( 138,143,223,1)',
        colorSecondaryFive: 'rgba( 185,187,241,1)',
        colorComp: 'rgba(255,189,107,1)',
        colorCompTwo: 'rgba(255,176,76,1)',
        colorCompThree: 'rgba(255,205,142,1)',
        siteColors: [
            'rgba( 87,151,193,1)', 'rgba(178,214,238,1)', 'rgba(127,182,217,1)', 'rgba( 56,123,166,1)', 'rgba( 31,103,149,1)',
            'rgba(101,106,202,1)', 'rgba( 71,77,180,1)', 'rgba( 45,51,162,1)', 'rgba( 138,143,223,1)', 'rgba( 185,187,241,1)',
            'rgba(255,189,107,1)', 'rgba(255,176,76,1)', 'rgba(255,205,142,1)','rgba(0,0,0,1)','rgba(0,0,0,0.75)'
        ]
    },
    Utils: {
        Plugins: {
            noData: function(chart) {
                if (!SFS.Utils.hasData(chart.data.datasets)) {
                    var ctx = chart.chart.ctx;
                    var width = chart.chart.width;
                    var height = chart.chart.height
                    //chart.clear();
                    
                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.font = "16px normal 'Helvetica Nueue'";
                    if (chart.options.noDataText) {
                        ctx.fillText(chart.options.noDataText, width / 2, height / 2);    
                    } else {
                        ctx.fillText('No data to display', width / 2, height / 2);
                    }
                    ctx.restore();
                }
            }
        },
        hasData: function(datasets) {
          if (datasets == undefined || datasets.length == 0) return false
          for (var i = 0; i < datasets.length; i++) {
              var dataset = datasets[i];
              if (dataset.data.length > 0) return true;
          }
          return false;
        },
        formatCategoryNames: function(categories) {
            var noQuotes = categories.replace(/"/g, '');
            var noQuotes = categories.replace(/'/g, '');
            var cats = noQuotes.split(',');
            var catLen = cats.length;
            var text = "Categories: ";
            if (catLen == 1) {
                text = "Category: ";
            }
            for (var i = 0; i < catLen; i++) {
                var cat = cats[i].toLowerCase();
                cat = cat.charAt(0).toUpperCase() + cat.substr(1);
                if (catLen >= 2 && i == catLen - 1) {
                    text += "& " + cat;
                } else if (i == catLen - 2 || catLen == 1) {
                    text += cat + " ";
                } else {
                    text += cat + ", ";
                }
            }
            return text;
        },

        SeasonInvestmentByTypeBubbleToolTips: function(tooltip) {
            if (!tooltip || !tooltip.dataPoints) {
                return;
            }
            
            var chart = window.investmentPerSeasonBubble.chart;
            //console.log(chart);
            var dataSetIndex = tooltip.dataPoints[0].datasetIndex;
            var dataIndex = tooltip.dataPoints[0].index;
            var label = chart.config.data.datasets[dataSetIndex].label;
            var data = chart.config.data.datasets[dataSetIndex].data[dataIndex];
            //tooltip.body[0].lines[0] = label;
            tooltip.body[0].lines.pop();
            tooltip.title.push(label);
            tooltip.displayColors = false;
            tooltip.footer.push("Total: $" + data.amt.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            var categories = data.categories;
            var catLen = categories.length;
            
            for (var i = 0 ; i < catLen; i++) {
                var cat = categories[i];
                if (cat.category && cat.total) {
                    var line = cat.category + ": $"+ cat.total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    tooltip.body[0].lines.push(line);
                }
            }
            var height = tooltip.body[0].lines.length * (tooltip.bodyFontSize + 4)
                + (tooltip.titleFontSize + tooltip.titleMarginBottom + tooltip.titleMarginBottom)
                + (tooltip.footerFontSize + tooltip.footerMarginTop + tooltip.footerSpacing);
            tooltip.height = height;
            tooltip.width = 160;
            //console.log(tooltip);
            //console.log(data);
        },

        MoneyToolTips: function(tooltipItem, data){
            var dataset = data.datasets[tooltipItem.datasetIndex];
            var dataitem = dataset.data[tooltipItem.index];
            var label = dataset.label;
            //console.log(dataitem.toString());
            return  label + ': $' + dataitem.toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        
        MoneyTicks: function(value, index, values) {
            //console.log(value);
            return  ' $' + value.toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    }
};
window.SFS = SFS;
Chart.defaults.global.title.fontFamily = "'Archivo Black', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
Chart.defaults.global.tooltips.titleFontFamily = "'Archivo Black', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
Chart.plugins.register({
    afterDraw: SFS.Utils.Plugins.noData
});
