var Color = net.brehaut.Color;
var SFS = SFS || {
    Chart: {
        Bar: {},
        Bubble: {},
        Line: {},
        Pie: {},
    },
    Options: {
        showAllToolTips: false
    },
    Constant: {
        Colors: {
            White: 'rgb(255, 255, 255)',
            Black: 'rgb(0, 0, 0)',
            Primary: 'rgba(87,151,193,1)',
            PrimaryTwo: 'rgba(178,214,238,1)',
            PrimaryThree: 'rgba(127,182,217,1)',
            PrimaryFour: 'rgba( 56,123,166,1)',
            PrimaryFive: 'rgba( 31,103,149,1)',
            Secondary: 'rgba(101,106,202,1)',
            SecondaryTwo: 'rgba( 71,77,180,1)',
            SecondaryThree: 'rgba( 45,51,162,1)',
            SecondaryFour: 'rgba( 138,143,223,1)',
            SecondaryFive: 'rgba( 185,187,241,1)',
            Complimentary: 'rgba(255,189,107,1)',
            ComplimentaryTwo: 'rgba(255,176,76,1)',
            ComplimentaryThree: 'rgba(255,205,142,1)',
        },
        siteColors: [
            'rgba( 87,151,193,1)', 'rgba(178,214,238,1)', 'rgba(127,182,217,1)', 'rgba( 56,123,166,1)', 'rgba( 31,103,149,1)',
            'rgba(101,106,202,1)', 'rgba( 71,77,180,1)', 'rgba( 45,51,162,1)', 'rgba( 138,143,223,1)', 'rgba( 185,187,241,1)',
            'rgba(255,189,107,1)', 'rgba(255,176,76,1)', 'rgba(255,205,142,1)','rgba(0,0,0,1)','rgba(0,0,0,0.75)'
        ],
        colorWhite: 'rgb(255, 255, 255)'
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
            },
            
            hoverHandler: function(evt, elm) {
            	if (gtag) {
                    var chartConfig = this.chart.config;
                    var title = chartConfig.options.title.text;
                    var chartType = chartConfig.type;
                    //console.log("clicked on: " + chartType);
                    //console.log("Title: " + title);
                    gtag('event', 'chart_hover', {
                        'event_category': chartType,
                        'event_label': title
                    });
            	}
            },
            
            clickHandler: function(evt, elm) {
            	if (gtag) {
                    var chartConfig = this.chart.config;
                    var title = chartConfig.options.title.text;
                    var chartType = chartConfig.type;
                    //console.log("clicked on: " + chartType);
                    //console.log("Title: " + title);
                    gtag('event', 'chart_click', {
                        'event_category': chartType,
                        'event_label': title
                    });
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
            var safeAmt = Number(data.amt);
            tooltip.body[0].lines.pop();
            tooltip.title.push(label);
            tooltip.displayColors = false;
            tooltip.footer.push("Total: $" + safeAmt.toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
            var categories = data.categories;
            var catLen = categories.length;
            
            for (var i = 0 ; i < catLen; i++) {
                var cat = categories[i];
                if (cat.category && cat.total) {
                    var safeTotal = Number(cat.total);
                    var line = cat.category + ": $"+ safeTotal.toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
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
            var safeValue = Number(dataitem);
            return  label + ': $' + safeValue.toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        
        MoneyTicks: function(value, index, values) {
            //console.log(value);
            var safeValue = Number(value);
            return  ' $' + safeValue.toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    }
};
window.SFS = SFS;
Chart.defaults.global.title.fontFamily = "'Archivo Black', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
Chart.defaults.global.tooltips.titleFontFamily = "'Archivo Black', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
Chart.defaults.global.firedHover = false;
Chart.defaults.global.hoverEventTime = Date.now();
Chart.plugins.register({
    afterDraw: SFS.Utils.Plugins.noData
});
