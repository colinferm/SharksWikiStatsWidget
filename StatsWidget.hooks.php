<?php 

class StatsWidgetHooks {
	private static $scriptInclusions = "";
	public static $teamCounts = 0;

	public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {
		$out->addModules('ext.statsforsharks.statswidget.css');
		$out->addModules('ext.statsforsharks.statswidget.js');
	}
	public static function onParserSetup( Parser $parser ) {
		$parser->setHook('statschart', 'StatsWidgetHooks::renderTagStatsChart');
	}
	public static function onSkinAfterBottomScripts( $skin, &$text ) {
		//echo StatsWidgetHooks::$scriptInclusions;
		//$text = StatsWidgetHooks::$scriptInclusions;
		return true;
	}

	public static function renderTagStatsChart( $input, array $args, Parser $parser, PPFrame $frame ) {
		$js = "";
		if (array_key_exists("company", $args))
			$companyName = strtolower(htmlspecialchars($args['company']));

		if (array_key_exists("type", $args))
			$chartType = strtolower(htmlspecialchars($args['type']));
		$chartTitle = "";
		$chartWidth = "100%";
		$startSeason = 1;
		$season = 0;
		$mainCast = 1;
		$categoriesRaw = "";
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
				$categoriesRaw = htmlspecialchars($args['categories']);
			} else {
				$categoriesRaw = htmlspecialchars($args['category']);
			}

			$categories = StatsWidgetLib::normalizeList($categoriesRaw);
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
			$js = StatsWidgetRender::renderDealTypeMix($season, $mainCast, $categories, $shark);
			StatsWidgetHooks::$scriptInclusions .= $js;

		} else if ($chartType == 'season-investment-by-type') {
			if (!array_key_exists("season", $args)) $season = 0;
			if ($categoriesRaw == "RESTAURANT") {
				$categories = StatsWidgetLib::normalizeList("SERVICE,FOOD,RETAIL");
			}

			$js = StatsWidgetRender::renderSeasonInvestmentByType($season, $categories, $shark, $chartTitle);
			StatsWidgetHooks::$scriptInclusions .= $js;

		}else if ($chartType == 'season-investment-by-category') {
			if (!array_key_exists("season", $args)) $season = 0;

			$js = StatsWidgetRender::renderSeasonInvestmentByCategory($season, $shark, $limit);
			StatsWidgetHooks::$scriptInclusions .= $js;

		}  else if ($chartType == 'shark-season-investment') {
			if (!array_key_exists("end", $args)){
				$season = StatsWidgetLib::getLatestSeason();
			}
			if ($categoriesRaw == "RESTAURANT") {
				$categories = StatsWidgetLib::normalizeList("SERVICE,FOOD,RETAIL");
			}
			$js = StatsWidgetRender::renderSeasonBySeasonInvestmentData($startSeason, $season, $mainCast, $categories, $shark, $chartTitle);
			StatsWidgetHooks::$scriptInclusions .= $js;

		} else if ($chartType == 'shark-amt-investment') {
			if ($categoriesRaw == "RESTAURANT") {
				$categories = StatsWidgetLib::normalizeList("SERVICE,FOOD,RETAIL");
			}
			$js = StatsWidgetRender::renderSharkAmountInvestmentData($season, $mainCast, $categories, $shark, $chartTitle);
			StatsWidgetHooks::$scriptInclusions .= $js;

		} else if ($chartType == 'shark-rel-investment') {
			//HERE
			$js = StatsWidgetRender::renderSharkRelativeInvestmentData($startSeason, $season, $categories, $shark);
			StatsWidgetHooks::$scriptInclusions .= $js;

		} else if ($chartType == 'types-by-season') {
			$js = StatsWidgetRender::renderDealsByInvestmentTypeData($startSeason, $season, $dealTypes);
			StatsWidgetHooks::$scriptInclusions .= $js;

		} else if ($chartType == 'categories-by-season') {
			$js = StatsWidgetRender::renderDealsByCategoryData($startSeason, $season, $categories, $chartTitle);
			StatsWidgetHooks::$scriptInclusions .= $js;

		} else if ($chartType == 'bite-by-season') {
			$js = StatsWidgetRender::renderBiteBySeasonData($startSeason, $season, $categories, $shark, $average, $chartTitle);
			StatsWidgetHooks::$scriptInclusions .= $js;

		} else if ($chartType == 'team-ups') {
			$chartType .= "-".StatsWidgetHooks::$teamCounts;
			$js = StatsWidgetRender::renderTeamUpData($categories, $shark, $limit, $chartTitle);
			StatsWidgetHooks::$scriptInclusions .= $js;
			StatsWidgetHooks::$teamCounts++;

		} else if ($chartType != 'biggest-bites') {
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

		if ($chartType == 'biggest-bites') {
			$chart = StatsWidgetRender::renderBiggestBiteChart($categories, $shark, $season, $chartTitle);
			return array($chart, "markerType" => 'nowiki' );;
		} else {
			$chart = '
				<div id="'.$chartType.'-holder" class="statschart '.$chartType.' '.$style.'">
					<canvas id="'.$chartId.'" />
				</div>
			'.$js;
			return array($chart, "markerType" => 'nowiki' );;
		}
	}
}

?>