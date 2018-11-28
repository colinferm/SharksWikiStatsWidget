<?php 

class StatsWidgetHooks {
	private static $scriptInclusions = "";

	public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
		$out->addModules( 'ext.statsforsharks.statswidget.js' );
	}
	public static function onParserSetup( Parser $parser ) {
		$parser->setHook( 'statschart', 'StatsWidgetHooks::renderTagStatsChart' );
	}
	public static function onSkinAfterBottomScripts( $skin, &$text ) {
		//echo StatsWidgetHooks::$scriptInclusions;
		$text = StatsWidgetHooks::$scriptInclusions;
		return true;
	}

	public static function renderTagStatsChart( $input, array $args, Parser $parser, PPFrame $frame ) {
		if (array_key_exists("company", $args))
			$companyName = strtolower(htmlspecialchars($args['company']));

		if (array_key_exists("type", $args))
			$chartType = strtolower(htmlspecialchars($args['type']));
		$chartTitle = "";
		$chartWidth = "100%";
		$startSeason = 1;
		$season = 0;
		$mainCast = 1;
		$categories = "";
		$dealTypes = "";
		$shark = "";
		$style = "";
		$limit = 0;
		$average = false;

		if (array_key_exists("style", $args))
			$style = htmlspecialchars($args['style']);

		if (array_key_exists('title', $args))
			$chartTitle = htmlspecialchars($args['title']);

		if (array_key_exists("width", $args))
			$chartWidth = htmlspecialchars($args['width']);

		if (array_key_exists("season", $args))
			$season = htmlspecialchars($args['season']);

		if (array_key_exists("start", $args))
			$startSeason = htmlspecialchars($args['start']);
		
		if (array_key_exists("end", $args))
			$season = htmlspecialchars($args['end']);

			if (array_key_exists("sharks", $args) || array_key_exists("shark", $args)) {
				if (array_key_exists("sharks", $args)) {
					$shark = htmlspecialchars($args['sharks']);
				} else {
					$shark = htmlspecialchars($args['shark']);
				}
	
				$shark = StatsWidgetLib::normalizeList($shark);
			}

		if (array_key_exists("limit", $args))
			$limit = htmlspecialchars($args['limit']);

		if (array_key_exists("average", $args)) {
			$averageParam = htmlspecialchars($args['average']);
			if ($averageParam == "true") $average = true;
		}

		if (array_key_exists("categories", $args) || array_key_exists("category", $args)) {
			if (array_key_exists("categories", $args)) {
				$categories = htmlspecialchars($args['categories']);
			} else {
				$categories = htmlspecialchars($args['category']);
			}

			$categories = StatsWidgetLib::normalizeList($categories);
		}

		if (array_key_exists("deal-types", $args) || array_key_exists("deal-type", $args)) {
			if (array_key_exists("deal-types", $args)) {
				$dealTypes = htmlspecialchars($args['deal-types']);
			} else {
				$dealTypes = htmlspecialchars($args['deal-type']);
			}

			$dealTypes = StatsWidgetLib::normalizeList($dealTypes);
		}

		if (array_key_exists("main-cast", $args)) {
			$castParam = htmlspecialchars($args['main-cast']);
			if ($castParam == "false") $mainCast = 0;
			//MWDebug::log("Main Cast Parameter: ".$castParam);

		}
		
		if ($chartType == 'deal-type-mix') {
			$js = StatsWidgetHooks::renderDealTypeMix($season, $mainCast, $categories, $shark);
			StatsWidgetHooks::$scriptInclusions .= $js;

		} else if ($chartType == 'season-investment-by-type') {
			if (!array_key_exists("season", $args)) $season = 0;

			$js = StatsWidgetHooks::renderSeasonInvestmentByType($season, $categories, $shark);
			StatsWidgetHooks::$scriptInclusions .= $js;

		}else if ($chartType == 'season-investment-by-category') {
			if (!array_key_exists("season", $args)) $season = 0;

			$js = StatsWidgetHooks::renderSeasonInvestmentByCategory($season, $shark, $limit);
			StatsWidgetHooks::$scriptInclusions .= $js;

		}  else if ($chartType == 'shark-season-investment') {
			if (!array_key_exists("end", $args)){
				$season = StatsWidgetLib::getLatestSeason();
			}
			$js = StatsWidgetHooks::renderSeasonBySeasonInvestmentData($startSeason, $season, $mainCast, $categories, $shark, $chartTitle);
			StatsWidgetHooks::$scriptInclusions .= $js;

		} else if ($chartType == 'shark-amt-investment') {
			$js = StatsWidgetHooks::renderSharkAmountInvestmentData($season, $mainCast, $categories, $shark);
			StatsWidgetHooks::$scriptInclusions .= $js;

		} else if ($chartType == 'shark-rel-investment') {
			//HERE
			$js = StatsWidgetHooks::renderSharkRelativeInvestmentData($startSeason, $season, $categories, $shark);
			StatsWidgetHooks::$scriptInclusions .= $js;

		} else if ($chartType == 'types-by-season') {
			$js = StatsWidgetHooks::renderDealsByInvestmentTypeData($startSeason, $season, $dealTypes);
			StatsWidgetHooks::$scriptInclusions .= $js;

		} else if ($chartType == 'categories-by-season') {
			$js = StatsWidgetHooks::renderDealsByCategoryData($startSeason, $season, $categories, $chartTitle);
			StatsWidgetHooks::$scriptInclusions .= $js;

		} else if ($chartType == 'bite-by-season') {
			$js = StatsWidgetHooks::renderBiteBySeasonData($startSeason, $season, $categories, $shark, $average, $chartTitle);
			StatsWidgetHooks::$scriptInclusions .= $js;

		} else if ($chartType == 'team-ups') {
			$js = StatsWidgetHooks::renderTeamUpData($categories, $shark, $chartTitle);
			StatsWidgetHooks::$scriptInclusions .= $js;

		} else {
			return;
		}

		$chartId = $chartType;
		if ($average) {
			$chartId .= '-avg';
		}
		if (strlen($season)) {
			$chartId .= '-'.$season;
		}

		$styles = "";
		if (strpos($chartWidth, '%')) {
			$width = substr($chartWidth, 0, strlen($chartWidth) - 1);
			if ($width <= 49) {
				$styles = "float: left;";
			}
		}

		$chart = '
			<div id="'.$chartType.'-holder" class="statschart '.$chartType.' '.$style.'">
				<canvas id="'.$chartId.'" />
			</div>
		';

		return array($chart, "markerType" => 'nowiki' );;
	}

	public static function renderDealTypeMix($season, $main_cast = 1, $categories = "", $sharks = "") {
		$results = StatsWidgetLib::investmentByShark($season, $main_cast, $categories, $sharks);
		$labels = $results['labels'];
		$data = $results['data'];

		$seasonLabel = "Season ".$season." - Deal Type Mix";
		if ($season == 0)  $seasonLabel = "All Seasons - Deal Type Mix";

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

	public static function renderSeasonInvestmentByType($season, $categories, $shark) {
		$seasonData = StatsWidgetLib::seasonInvestmentByType($season, $categories, $shark);

		$labels = $seasonData['labels'];
		$data = $seasonData['data'];

		$seasonLabel = "Season ".$season." - Deal Types";
		if ($season == 0)  $seasonLabel = "All Seasons - Deal Types";

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
		//MWDebug::log("Main Cast: ".$mainCast);
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

	public static function renderSharkAmountInvestmentData($season, $mainCast, $categories, $shark) {
		MWDebug::log("Categories: ".$categories);
		$result = StatsWidgetLib::investmentAmountsByShark($season, $mainCast, $categories, $shark);
		$labels = $result['labels'];
		$colors = $result['colors'];
		$amts = $result['amts'];
		$nums = $result['nums'];

		$chartTitle = "All Seasons - Shark Investment Totals";
		if ($season > 0) {
			$chartTitle = "Season ".$season." - Shark Investment Totals";
		}

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

	public static function renderSharkRelativeInvestmentData($startSeason, $season, $categories, $shark) {
		MWDebug::log("Categories: ".$categories);
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
							borderColor: "#ffffff",
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
				SFS.Chart.Bubble.seasonInvestmentByType(seasonBySeasonBubbleData, "shark-rel-investment-'.$season.'", "'.$chartTitle.'", "'.$categories.'");
			});
		});
		</script>
		';

		return $js;
	}

	public static function renderDealsByInvestmentTypeData($start, $end, $dealTypes) {
		//MWDebug::log("Main Cast: ".$mainCast);
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
									label: "Proposed Company Values",
									backgroundColor: SFS.Constant.colorOne,
									borderColor: SFS.Constant.colorOne,
									data: '.str_replace('"', '', json_encode($proposed)).',
									fill: false,
								},
								{
									label: "Company Worth After the Bite",
									backgroundColor: SFS.Constant.colorComp,
									borderColor: SFS.Constant.colorComp,
									data: '.str_replace('"', '', json_encode($bite)).',
									fill: false,
								},
						]
					};
					SFS.Chart.Line.biteBySeason(biteBySeasonData, "'.$divId.'", "'.$chartTitle.'");
				});
			});
		</script>
		';

		return $js;
	}

	public static function renderTeamUpData($categories, $shark, $chartTitle = "Team Ups Between Sharks") {
		$result = StatsWidgetLib::teamupsByShark($categories, $shark);
		$labels = $result['labels'];
		$colors = $result['colors'];
		$nums = $result['nums'];

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
				SFS.Chart.Pie.sharkTeamUps(teamupData, "team-ups-'.$season.'", "'.$chartTitle.'", "'.$categories.'");
			});
		});
		</script>
		';

		return $js;
	}
}

?>