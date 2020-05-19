<?php
class StatsWidgetRender {
	public static function renderDealTypeMix($season, $main_cast = 1, $categories = "", $sharks = "", $chartTitle = "") {
		$results = StatsWidgetLib::investmentByShark($season, $main_cast, $categories, $sharks);
		$labels = $results['labels'];
		$data = $results['data'];

		$seasonLabel = "Season ".$season." - Deal Type Mix";
		if ($season == 0)  $seasonLabel = "All Seasons - Deal Type Mix";
		if (strlen($chartTitle)) {
			$seasonLabel = $chartTitle;
			$categories = "";
		}

		$js = '
		<script>
			(window.RLQ=window.RLQ||[]).push(function() {
				mw.loader.using(["ext.statsforsharks.statswidget.js"]).done(function() {
					var investmentBySharkData = {
						labels: '.json_encode($labels).',
						datasets: '.json_encode($data).'
					};
					SFS.Chart.Bar.investmentsByShark(investmentBySharkData, "deal-type-mix-'.$season.'", "'.$seasonLabel.'","'.$categories.'");
				});
			});
		</script>
		';

		return $js;
	}

	public static function renderSeasonInvestmentByType($season, $categories, $shark, $chartTitle = "") {
		$seasonData = StatsWidgetLib::seasonInvestmentByType($season, $categories, $shark, $chartTitle);

		$labels = $seasonData['labels'];
		$data = $seasonData['data'];

		$seasonLabel = "Season ".$season." - Deal Types";
		if ($season == 0)  $seasonLabel = "All Seasons - Deal Types";
		if (strlen($chartTitle)) {
			$seasonLabel = $chartTitle;
			$categories = "";
		}

		$js = '
		<script>
			(window.RLQ=window.RLQ||[]).push(function() {
				mw.debug = true
				mw.loader.using(["ext.statsforsharks.statswidget.js"]).done(function() {
					var data = '.json_encode($data).';
					var backgrounds = new Array();
					for (var i = 0; i < data.length; i++) {
						backgrounds.push(SFS.Constant.siteColors[i]);
					}
					var dealTypeData = {
						labels: '.json_encode($labels).',
						datasets: [{
							data: data,
							backgroundColor: backgrounds,
							label: "Season '.$season.' - Investment by Deal Type"
						}]
					};
					SFS.Chart.Pie.seasonInvestmentByType(dealTypeData, "season-investment-by-type-'.$season.'", "'.$seasonLabel.'", "'.$categories.'");
				});
			});
		</script>';

		return $js;
	}

	public static function renderSeasonInvestmentByCategory($season, $shark, $limit) {
		$result = StatsWidgetLib::seasonInvestmentByCategory($season, $shark, $limit);

		$labels = $result['labels'];
		$numData = $result['numData'];
		$amtData = $result['amtData'];

		$seasonLabel = "Season ".$season." - Investment Categories";
		if ($season == 0)  $seasonLabel = "All Seasons - Investment Categories";
		if ($limit > 0) {
			$seasonLabel = "";
			if (strlen($shark)) {
				$seasonLabel = str_replace("'", "", $shark)."'s ";
			}
			$seasonLabel .= "Top ".$limit." Investment Categories";
		}

		$js = '
		<script>
			(window.RLQ=window.RLQ||[]).push(function() {
				mw.debug = true
				mw.loader.using(["ext.statsforsharks.statswidget.js"]).done(function() {
					var numData = '.json_encode($numData).';
					var backgrounds = new Array();
					for (var i = 0; i < numData.length; i++) {
						backgrounds.push(SFS.Constant.siteColors[i]);
					}
					var dealTypeData = {
						labels: '.json_encode($labels).',
						datasets: [{
							data: '.json_encode($amtData).',
							backgroundColor: backgrounds,
							label: "amount"
						},{
							data: numData,
							backgroundColor: backgrounds,
							label: "number"
						}]
					};
					SFS.Chart.Pie.seasonInvestmentByType(dealTypeData, "season-investment-by-category-'.$season.'", "'.$seasonLabel.'");
				});
			});
		</script>';

		return $js;
	}

	public static function renderSeasonBySeasonInvestmentData($start, $end, $mainCast, $categories = "", $shark = "", $chartTitle = "") {
		$compiledData = StatsWidgetLib::seasonBySeasonInvestments($start, $end, $mainCast, $categories, $shark);
		$seasons = $compiledData['seasonLabels'];
		$sharkData = $compiledData['sharkData'];

		if (!strlen($chartTitle)) {
			$chartTitle = "Season by Season Investment Totals";
		}

		$js = '
		<script>
			(window.RLQ=window.RLQ||[]).push(function() {
				mw.debug = true
				mw.loader.using(["ext.statsforsharks.statswidget.js"]).done(function() {
					var seasonBySeasonInvestmentData = {
						labels: '.json_encode($seasons).',
						datasets: [
							';
							$sharkCount = 0;
							foreach ($sharkData as $shark => $amounts) :
								$js .= '
								{
									label: "'.$shark.'",
									backgroundColor: SFS.Constant.siteColors['.$sharkCount.'],
									borderColor: SFS.Constant.siteColors['.$sharkCount.'],
									data: '.str_replace('"', '', json_encode($amounts)).',
									fill: false,
								},
								';
								$sharkCount++;
							endforeach;
						$js .= '
						]
					};
					SFS.Chart.Line.investmentBySeason(seasonBySeasonInvestmentData, "shark-season-investment-'.$end.'", "'.$chartTitle.'", "'.$categories.'");
				});
			});
		</script>
		';

		return $js;
	}

	public static function renderSharkAmountInvestmentData($season, $mainCast, $categories, $shark, $chartTitle = "") {
		MWDebug::log("Categories: ".implode(",", $categories));
		$result = StatsWidgetLib::investmentAmountsByShark($season, $mainCast, $categories, $shark);
		$labels = $result['labels'];
		$colors = $result['colors'];
		$amts = $result['amts'];
		$nums = $result['nums'];

		if (!strlen($chartTitle)) {
			$chartTitle = "All Seasons - Shark Investment Totals";
			if ($season > 0) {
				$chartTitle = "Season ".$season." - Shark Investment Totals";
			}
		}
		//$categories = "";

		$js = '
		<script>
		(window.RLQ=window.RLQ||[]).push(function() {
			mw.debug = true
			mw.loader.using(["ext.statsforsharks.statswidget.js"]).done(function() {
				var investmentAmountData = {
					labels: '.json_encode($labels).',
					datasets: [{
						data: '.json_encode($amts).',
						backgroundColor: '.json_encode($colors).',
						label: "Investment by Deal Type"
					},
					{
						data: '.json_encode($nums).',
						backgroundColor: '.json_encode($colors).',
						label: "Investment by Numbers"
					},
					]
				};
				SFS.Chart.Pie.sharkInvestmentTotals(investmentAmountData, "shark-amt-investment-'.$season.'", "'.$chartTitle.'", "'.$categories.'");
			});
		});
		</script>
		';

		return $js;
	}

	public static function renderSharkRelativeInvestmentData($startSeason, $season, $categories, $shark, $vTicks = 0) {
		$result = StatsWidgetLib::relativeInvestmentByShark($startSeason, $season, $categories, $shark);

		$chartTitle = "All Seasons - Shark Investment Totals";
		if ($season > 0) {
			$chartTitle = "Season ".$season." - Shark Investment Totals";
		}

		$js = '
		<script>
		(window.RLQ=window.RLQ||[]).push(function() {
			mw.debug = true
			mw.loader.using(["ext.statsforsharks.statswidget.js"]).done(function() {
				var seasonBySeasonBubbleData = {
					datasets: [';
					foreach($result as &$shark) {
						$js .= '
							{
							label: "'.$shark['label'].'",
							backgroundColor: "'.$shark['color'].'",
							borderColor: SFS.Constant.Colors.White,
							borderWidth: 1,
							data: [';
							foreach($shark['data'] as &$s) {
								$js .= '{
									x: '.$s['season'].',
									y: '.$s['num_deals'].',
									r: '.$s['radius'].',
									amt: '.$s['amt_invested'].',
									categories: '.json_encode($s['categories']).'
								},';
							}
							$js .= ']';
						$js .= '},';
					}
					$js .= ']
				}
				SFS.Chart.Bubble.seasonInvestmentByType(seasonBySeasonBubbleData, "shark-rel-investment-'.$season.'", "'.$chartTitle.'", "'.$categories.'", "'.$vTicks.'");
			});
		});
		</script>
		';

		return $js;
	}

	public static function renderDealsByInvestmentTypeData($start, $end, $dealTypes) {
		$endSeason = $end;
		if ($end == 0) {
			$endSeason = StatsWidgetLib::getLatestSeason();
		}
		$data = StatsWidgetLib::dealsByInvestmentType($start, $endSeason, $dealTypes);
		$seasonLabels = StatsWidgetLib::seasonLabels($start, $endSeason);

		$js = '
		<script>
			(window.RLQ=window.RLQ||[]).push(function() {
				mw.debug = true
				mw.loader.using(["ext.statsforsharks.statswidget.js"]).done(function() {
					var dealsByInvestmentTypeData = {
						labels: '.json_encode($seasonLabels).',
						datasets: [
							';
							$typeCount = 0;
							foreach ($data as $type => $counts) :
								$js .= '
								{
									label: "'.StatsWidgetLib::dealTypeLabel($type).'",
									backgroundColor: SFS.Constant.siteColors['.$typeCount.'],
									borderColor: SFS.Constant.siteColors['.$typeCount.'],
									data: '.str_replace('"', '', json_encode($counts)).',
									spanGaps: true,
									fill: false,
								},
								';
								$typeCount++;
							endforeach;
						$js .= '
						]
					};
					SFS.Chart.Line.dealsByType(dealsByInvestmentTypeData, "types-by-season-'.$end.'", "Season '.$end.' - Deal Types");
				});
			});
		</script>
		';

		return $js;
	}

	public static function renderDealsByCategoryData($start, $end, $categories = "", $chartTitle = "") {
		$endSeason = $end;
		if ($end == 0) {
            $endSeason = StatsWidgetLib::getLatestSeason();
		}
		if (!strlen($chartTitle)) {
			$chartTitle = "Top 5 Company Categories";
			if (strlen($categories)) {
				$chartTitle = "Company Categories";
			}
		}
		$data = StatsWidgetLib::dealsByCategory($start, $endSeason, $categories);
		$seasonLabels = StatsWidgetLib::seasonLabels($start, $endSeason);

		$js = '
		<script>
			(window.RLQ=window.RLQ||[]).push(function() {
				mw.debug = true
				mw.loader.using(["ext.statsforsharks.statswidget.js"]).done(function() {
					var dealsByCategoryData = {
						labels: '.json_encode($seasonLabels).',
						datasets: [
							';
							$catCount = 0;
							foreach ($data as $cat => $counts) :
								$js .= '
								{
									label: "'.StatsWidgetLib::categoryLabel($cat).'",
									backgroundColor: SFS.Constant.siteColors['.$catCount.'],
									borderColor: SFS.Constant.siteColors['.$catCount.'],
									data: '.str_replace('"', '', json_encode($counts)).',
									fill: false,
									';
									if (strlen($categories) && strpos($categories, $cat) === FALSE) {
										$js .= 'hidden: true,';
									}
								$js .= '},
								';
								$catCount++;
							endforeach;
						$js .= '
						]
					};
					SFS.Chart.Line.dealsByCategory(dealsByCategoryData, "categories-by-season-'.$end.'", "'.$chartTitle.'");
				});
			});
		</script>
		';

		return $js;
	}

	public static function renderBiteBySeasonData($startSeason, $endSeason, $categories, $shark, $average, $chartTitle) {
		$data = StatsWidgetLib::biteBySeason($startSeason, $endSeason, $categories, $shark, $average);

		$labels = $data['labels'];
		$proposed = $data['proposed'];
		$bite = $data['bite'];

		$divId = 'bite-by-season-'.$endSeason;
		if ($average) {
			$divId = 'bite-by-season-avg-'.$endSeason;
		}

		if (!strlen($chartTitle)) {
			$chartTitle = "";
			if ($average) {
				$chartTitle = "Average Company Valuations vs. Shark Valuations";
			} else {
				$chartTitle = "Company Valuations vs. Shark Valuations";
			}
		}

		$js = '
		<script>
			(window.RLQ=window.RLQ||[]).push(function() {
				mw.debug = true
				mw.loader.using(["ext.statsforsharks.statswidget.js"]).done(function() {
					var biteBySeasonData = {
						labels: '.json_encode($labels).',
						datasets: [
							{
								label: "Company Worth After the Bite",
								backgroundColor: Color(SFS.Constant.Colors.Complimentary).alpha(0.8).rgbString(),
								borderColor: SFS.Constant.Colors.ComplimentaryThree,
								data: '.str_replace('"', '', json_encode($bite)).',
								fill: true,
								pointBorderColor: SFS.Constant.Colors.White,
								pointRadius: 5,
								pointHoverRadius: 10
							},
							{
								label: "Proposed Company Values",
								backgroundColor: Color(SFS.Constant.Colors.Primary).alpha(0.8).rgbString(),
								borderColor: SFS.Constant.Colors.PrimaryThree,
								data: '.str_replace('"', '', json_encode($proposed)).',
								fill: true,
								pointBorderColor: SFS.Constant.Colors.White,
								pointRadius: 5,
								pointHoverRadius: 10
							}
						]
					};
					SFS.Chart.Line.biteBySeason(biteBySeasonData, "'.$divId.'", "'.$chartTitle.'", '.$average.');
				});
			});
		</script>
		';

		return $js;
	}

	public static function renderTeamUpData($categories, $shark, $limit, $chartTitle = "Team Ups Between Sharks") {
		$result = StatsWidgetLib::teamupsByShark($categories, $shark, $limit);
		$labels = $result['labels'];
		$colors = $result['colors'];
		$nums = $result['nums'];
		$chart_name = "team-ups-".StatsWidgetHooks::$teamCounts."-0";

		$season = 0;
		if (strlen($shark)) {
			$seasonLabel = str_replace("'", "", $shark)."'s Team Ups With Other Sharks";
			$chartTitle = $seasonLabel;
		}

		$js = '
		<script>
		(window.RLQ=window.RLQ||[]).push(function() {
			mw.debug = true
			mw.loader.using(["ext.statsforsharks.statswidget.js"]).done(function() {
				var teamupData = {
					labels: '.json_encode($labels).',
					datasets: [{
						data: '.json_encode($nums).',
						backgroundColor: '.json_encode($colors).',
						label: "Teamups With Other Sharks"
					}]
				};
				SFS.Chart.Pie.sharkTeamUps(teamupData, "'.$chart_name.'", "'.$chartTitle.'", "'.$categories.'");
			});
		});
		</script>
		';

		return $js;
	}

	public static function renderBiggestBiteChart($categories, $shark, $season, $chartTitle = "Biggest Bites") {
		$output = "
			<div class='biggest-bite'>
				<div class='biggest-bite-header'><h2>".$chartTitle."</h2></div>
				<div class='biggest-bite-data-container'>
					<div class='biggest-bite-data-row'>
						<div class='biggest-bite-shark'>
							<img src='/extensions/StatsWidget/img/square-barbara.jpg'>
						</div>
						<div class='biggest-bite-graph-container'>
							<div class='biggest-bite-graph' style='width: 75%;'><span class='bite-text'>$1,750,000 in value bitten</span></div>
						</div>
					</div>
					<div class='biggest-bite-data-row'>
						<div class='biggest-bite-shark'>
							<img src='/extensions/StatsWidget/img/square-daymond.jpg'>
						</div>
						<div class='biggest-bite-graph-container'>
							<div class='biggest-bite-graph' style='width: 55%;'><span class='bite-text'>$1,750,000 in value bitten</span></div>
						</div>
					</div>
					<div class='biggest-bite-data-row'>
						<div class='biggest-bite-shark'>
							<img src='/extensions/StatsWidget/img/square-kevin.jpg'>
						</div>
						<div class='biggest-bite-graph-container'>
							<div class='biggest-bite-graph' style='width: 90%;'><span class='bite-text'>$1,750,000 in value bitten</span></div>
						</div>
					</div>
					<div class='biggest-bite-data-row'>
						<div class='biggest-bite-shark'>
							<img src='/extensions/StatsWidget/img/square-lori.jpg'>
						</div>
						<div class='biggest-bite-graph-container'>
							<div class='biggest-bite-graph' style='width: 85%;'><span class='bite-text'>$1,750,000 in value bitten</span></div>
						</div>
					</div>
					<div class='biggest-bite-data-row'>
						<div class='biggest-bite-shark'>
							<img src='/extensions/StatsWidget/img/square-mark.jpg'>
						</div>
						<div class='biggest-bite-graph-container'>
							<div class='biggest-bite-graph' style='width: 65%;'><span class='bite-text'>$1,750,000 in value bitten</span></div>
						</div>
					</div>
					<div class='biggest-bite-data-row'>
						<div class='biggest-bite-shark'>
							<img src='/extensions/StatsWidget/img/square-robert.jpg'>
						</div>
						<div class='biggest-bite-graph-container'>
							<div class='biggest-bite-graph' style='width: 25%;'><span class='bite-text'>$1,750,000 in value bitten</span></div>
						</div>
					</div>
				</div>
			</div>
		";
		return $output;
	}
	
	public static function renderAppearancesChart($shark, $season, $chartTitle, $mainCast) {
		$data = StatsWidgetLib::getSharkMoneyAppearances($season, $shark, $mainCast);
		$output = "
			<div class='appearances-table'>
				<div class='appearances-table-header'><h2>".$chartTitle."</h2></div>
				<div class='appearances-data-container'>
		";
		foreach ($data as $item) {
			$code = strtolower($item['shark']);
			$shark = $item['name'];
			$apps = $item['appearances'];
			$eps = $item['episodes'];
			$percent = ($apps / $eps) * 100;
			$investments = $item['investments'];
			$investments = number_format($investments);
			
			if ($season > 0) {
				$chartText = "{$shark}: {$apps} / {$eps} eps";
			} else {
				$chartText = "{$apps} / {$eps} eps";
				$season_num = $item['season_num'];
				$code = "season_num";
			}
			
			
			$output .= "
					<div class='appearances-data-row'>
						<div class='appearances-shark {$code}'>{$season_num}</div>
						<div class='appearances-graph-container'>
							<div class='appearances-graph' style='width: {$percent}%;'><span class='appearances-text'>{$chartText}</span></div>
						</div>
						<div class='appearances-money'>${$investments}</div>
					</div>
						";
		}
		$output .= "
				</div>
			</div>
		";
		
		return $output;
	}
}
?>