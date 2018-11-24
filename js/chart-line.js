SFS.Chart.Line.investmentBySeason = function(data, id, chartTitle, categories) {
    /*
	if (categories) {
		var formatted = SFS.Utils.formatCategoryNames(categories);
		chartTitle += " - " + formatted;
    }
    */
    var perSeasonCTX = document.getElementById(id).getContext("2d");
    window.investmentPerSeasonChart = new Chart(perSeasonCTX, {
        type: 'line',
        data: data,
        options: {
            title: {
                display:true,
                text: chartTitle
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                itemSort: function(a, b) {
                    if (a.yLabel > b.yLabel) {
                        return -1;
                    } else if (b.yLabel > a.yLabel) {
                        return 1
                    }
                    return 0;
                },
                callbacks: {
                    label: SFS.Utils.MoneyToolTips
                }
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            responsive: true,
            scales: {
                xAxes: [{
                    stacked: false
                }],
                yAxes: [{
                    stacked: false,
                    scaleLabel: {
                        labelString: 'Amount Invested',
                        display: true
                    },
                    ticks: {
                        callback: SFS.Utils.MoneyTicks
                    }
                }]
            }
        }
    });
}

SFS.Chart.Line.dealsByType = function(data, id, chartTitle) {
    var perSeasonCTX = document.getElementById(id).getContext("2d");
    window.investmentPerSeasonChart = new Chart(perSeasonCTX, {
        type: 'line',
        data: data,
        options: {
            title: {
                display: true,
                text:"Deals By Investment Type"
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                itemSort: function(a, b) {
                    if (a.yLabel > b.yLabel) {
                        return -1;
                    } else if (b.yLabel > a.yLabel) {
                        return 1
                    }
                    return 0;
                }
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            responsive: true,
            scales: {
                xAxes: [{
                    stacked: false
                }],
                yAxes: [{
                    stacked: false,
                    scaleLabel: {
                        labelString: 'Number of Deals',
                        display: true
                    }
                }]
            }
        }
    });
}

SFS.Chart.Line.dealsByCategory = function(data, id, chartTitle) {
    var perSeasonCTX = document.getElementById(id).getContext("2d");
    window.investmentPerSeasonChart = new Chart(perSeasonCTX, {
        type: 'line',
        data: data,
        options: {
            title: {
                display: true,
                text: chartTitle
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                itemSort: function(a, b) {
                    if (a.yLabel > b.yLabel) {
                        return -1;
                    } else if (b.yLabel > a.yLabel) {
                        return 1
                    }
                    return 0;
                }
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            responsive: true,
            scales: {
                xAxes: [{
                    stacked: false
                }],
                yAxes: [{
                    stacked: false,
                    scaleLabel: {
                        labelString: 'Number of Companies',
                        display: true
                    }
                }]
            }
        }
    });
}

SFS.Chart.Line.biteBySeason = function(data, id, chartTitle) {
    var biteBySeasonCTX = document.getElementById(id).getContext("2d");
    window.biteBySeasonChart = new Chart(biteBySeasonCTX, {
        type: 'line',
        data: data,
        options: {
            title: {
                display: true,
                text: chartTitle
            },
            tooltips: {
                mode: 'index',
                intersect: false,
                itemSort: function(a, b) {
                    if (a.yLabel > b.yLabel) {
                        return -1;
                    } else if (b.yLabel > a.yLabel) {
                        return 1
                    }
                    return 0;
                },
                callbacks: {
                    label: SFS.Utils.MoneyToolTips
                }
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            responsive: true,
            scales: {
                xAxes: [{
                    stacked: false
                }],
                yAxes: [{
                    stacked: false,
                    scaleLabel: {
                        labelString: 'Total Capitalization Value',
                        display: true
                    },
                    ticks: {
                        callback: SFS.Utils.MoneyTicks
                    }
                }]
            }
        }
    });
}
Chart.defaults.line.spanGaps = true;