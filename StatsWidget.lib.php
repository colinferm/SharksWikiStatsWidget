<?php
class StatsWidgetLib {
    public static $SITE_COLORS = [
        'rgba( 87,151,193,1)', 'rgba(178,214,238,1)', 'rgba(127,182,217,1)', 'rgba( 56,123,166,1)', 'rgba( 31,103,149,1)',
        'rgba(101,106,202,1)', 'rgba( 71,77,180,1)', 'rgba( 45,51,162,1)', 'rgba( 138,143,223,1)', 'rgba( 185,187,241,1)',
        'rgba(255,189,107,1)', 'rgba(255,176,76,1)', 'rgba(255,205,142,1)', 'rgb(105,105,105)', 'rgb(128,128,128)',
        'rgb(169,169,169)', 'rgb(192,192,192)', 'rgb(211,211,211)', 'rgb(220,220,220)'
        ];

    public static function getLatestSeason() {
        $db = wfGetDB(DB_REPLICA);

        $maxSeasonQuery = "SELECT MAX(s.id) AS latest_season FROM sfs_season s LIMIT 1";
        $maxResult = $db->query($maxSeasonQuery, 'StatsWidgetLib::getLatestSeason');
        $season = 0;
        foreach($maxResult as $m) {
            $season = max($season, $m->latest_season);
        }
        return $season;
    }

    public static function getShark($shark) {
        $db = wfGetDB(DB_REPLICA);
        $clean = StatsWidgetLib::cleanShark($shark);

        $sharkQuery = "SELECT full_name FROM sfs_sharks WHERE shark = '$clean' LIMIT 1";
        $sharkResult = $db->query($sharkQuery, 'StatsWidgetLib::getShark');
        foreach($sharkResult as $s) {
            return $s->full_name;
        }
    }

    public static function getSharkId($shark) {
        $db = wfGetDB(DB_REPLICA);
        $clean = StatsWidgetLib::cleanShark($shark);

        $sharkQuery = "SELECT id FROM sfs_sharks WHERE shark = '$clean' LIMIT 1";
        $sharkResult = $db->query($sharkQuery, 'StatsWidgetLib::getSharkId');
        foreach($sharkResult as $s) {
            return $s->id;
        }
    }

    public static function investmentByShark($season = 0, $mainCast = 1, $categories = "", $addSharks = "") {
        $db = wfGetDB(DB_REPLICA);

        $sharkQuery = "SELECT DISTINCT s.id, s.shark, s.full_name FROM sfs_sharks s, sfs_episode_shark_map m, sfs_episodes e ".
                    "WHERE s.id = m.shark_id AND m.episode_id = e.id ";
        if ($mainCast == 1) {
            $sharkQuery .= "AND s.main_cast = ".$mainCast." ";
        }
        if (strlen($addSharks)) {
            $sharkQuery .= "OR s.id IN (SELECT id FROM sfs_sharks WHERE shark IN (".$addSharks.")) ";
        }
        if ($season > 0) {
            $sharkQuery .= "AND e.season_id = ".$season." ";
        }
        $sharkQuery .= "ORDER BY s.main_cast DESC, s.full_name ASC";
        $sharkResults = $db->query($sharkQuery, 'StatsWidgetLib::dealTypeMix');

        $dealTypes = "SELECT DISTINCT d.deal_type FROM sfs_deal d, sfs_episodes e WHERE d.deal_type != 'NONE' AND d.episode_id = e.id ";
        if ($season > 0) {
            $dealTypes .= "AND e.season_id = ".$season. " ";
        }
        $dealTypes .= "ORDER BY d.deal_type ASC";
        $dealResults = $db->query($dealTypes, 'StatsWidgetLib::dealTypeMix');

        $sharks = [];
        $sharkCount = 0;
        foreach ($sharkResults as $shark) {
            $sharks[] = $shark->full_name;
            $sharkCount++;
        }

        $data = [];
        $count = 0;
        foreach ($dealResults as $dealType) {
            $sharkData = [];
            foreach ($sharkResults as $shark) {
                $sharkData[$shark->shark] = 0;
            }

            $typeQuery = "SELECT s.shark, COUNT(d.id) AS num_deals, d.deal_type ".
                        "FROM sfs_sharks s ".
                        "INNER JOIN sfs_shark_deal_map sdm ON (sdm.shark_id = s.id) ".
                        "INNER JOIN sfs_deal AS d ON (d.id = sdm.deal_id) ".
                        "INNER JOIN sfs_episodes AS e ON (d.episode_id = e.id) ".
                        "WHERE d.deal_type = '".$dealType->deal_type."' ";
            
            if (strlen($categories)) {
                $typeQuery .= "AND d.category IN (".$categories.") ";
            }

            if (strlen($addSharks)) {
                $typeQuery .= "AND (s.main_cast = ".$mainCast." OR s.id IN (SELECT id FROM sfs_sharks WHERE shark IN (".$addSharks."))) ";
                
            } else if ($mainCast == 1) {
                $typeQuery .= "AND s.main_cast = ".$mainCast." ";
            }

            if ($season > 0) {
                $typeQuery .=  "AND e.season_id = ".$season." ";
            }

            $typeQuery .= "GROUP BY s.shark ".
                        "ORDER BY s.shark ASC";

            $typeResults = $db->query($typeQuery, 'StatsWidgetLib::dealTypeMix');

            foreach ($typeResults as $type) {
                $sharkData[$type->shark] = $type->num_deals;
            }

            $color = StatsWidgetLib::$SITE_COLORS[$count];
            if ($dealType->deal_type == 'SALE') $color = StatsWidgetLib::$SITE_COLORS[11];
            if ($dealType->deal_type == 'IP') $color = StatsWidgetLib::$SITE_COLORS[12];

            $dealTypeLabel = StatsWidgetLib::dealTypeLabel($dealType->deal_type);

            $data[] = array(
                'label' => $dealTypeLabel,
                'backgroundColor' => $color,
                'data' => array_values($sharkData)
            );
            $count++;
        }
        return array('labels' => $sharks, 'data' => $data);
    }

    public static function seasonInvestmentByType($season = 0, $categories = "", $shark = "") {
        $safeSeason = (int) $season;
        
        MWDebug::log("seasonInvestmentByType: Season: ".$season);

        $db = wfGetDB(DB_REPLICA);

        $investmentQuery = "SELECT count(DISTINCT d.id) AS num_deals, d.deal_type ".
                    "FROM sfs_deal d ".
                    "JOIN sfs_episodes e ON (d.episode_id = e.id) ".
                    "LEFT JOIN sfs_shark_deal_map m ON (d.id = m.deal_id) ".
                    "LEFT JOIN sfs_sharks s ON (m.shark_id = s.id) ".
                    "WHERE 1 = 1 ";

        if ($season > 0) {
            $investmentQuery .= "AND e.season_id = ".$safeSeason." ";
        }
        /*
        if (strlen($categories)) {
            $investmentQuery .= "AND d.category IN (".$categories.") ";
        }
        if ($season == 0 || (strlen($categories) && strpos($categories, ","))) {
            //$investmentQuery .= "AND d.deal_type != 'NONE' ";
        }
        */
        $investmentQuery .= StatsWidgetLib::builCategoryQuery($categories);
        if (strlen($shark)) {
            $investmentQuery .= "AND s.shark IN (".$shark.") ";
        }
        $investmentQuery .= "GROUP BY d.deal_type ".
                    "ORDER BY num_deals DESC";

        $totalDealsResult = $db->query($investmentQuery, 'StatsWidgetLib::seasonInvestmentByType');

        $labels = array();
        $backgrounds = array();
        $data = array();

        $i = 0;
        foreach ($totalDealsResult as $obj) {
            $data[] = $obj->num_deals;
            $labels[] = StatsWidgetLib::dealTypeLabel($obj->deal_type);
            $i++;
        }
        return array('labels' => $labels, 'data' => $data);
    }

    public static function seasonInvestmentByCategory($season = 0, $shark = "", $limit = 0) {
        $safeSeason = (int) $season;
        
        MWDebug::log("seasonInvestmentByCategory: Season: ".$season);

        $db = wfGetDB(DB_REPLICA);

        $investmentNumQuery = "SELECT count(d.id) AS num_deals, SUM(m.deal_amt) AS deals_amt, d.category ".
                    "FROM sfs_deal d, sfs_episodes e, sfs_shark_deal_map m, sfs_sharks s ".
                    "WHERE d.episode_id = e.id ".
                    "AND d.id = m.deal_id AND m.shark_id = s.id ";

        if ($season > 0) {
            $investmentNumQuery .= "AND e.season_id = ".$safeSeason." ";
        }
        if (strlen($shark)) {
            $investmentNumQuery .= "AND s.shark IN (".$shark.") ";
        }
        $investmentNumQuery .= "GROUP BY d.category ".
                    "ORDER BY deals_amt DESC, num_deals ";
            
        if ($limit > 0) {
            $investmentNumQuery .= "LIMIT ".$limit;
        }

        $investmentNumResult = $db->query($investmentNumQuery, 'StatsWidgetLib::seasonInvestmentByCategory');

        $labels = array();
        $backgrounds = array();
        $numData = array();
        $amtData = array();

        $i = 0;
        foreach ($investmentNumResult as $obj) {
            $numData[] = $obj->num_deals;
            $amtData[] = $obj->deals_amt;
            $labels[] = ucfirst(strtolower($obj->category));
            $i++;
        }

        /*
        $investmentAmtQuery = "SELECT SUM(m.deal_amt) AS deals_amt, d.category ".
                    "FROM sfs_deal d, sfs_episodes e, sfs_shark_deal_map m, sfs_sharks s ".
                    "WHERE d.episode_id = e.id ".
                    "AND d.id = m.deal_id AND m.shark_id = s.id ";

        if ($season > 0) {
            $investmentAmtQuery .= "AND e.season_id = ".$safeSeason." ";
        }
        if (strlen($shark)) {
            $investmentAmtQuery .= "AND s.shark = '".$shark."' ";
        }
        $investmentAmtQuery .= "GROUP BY d.category ".
                    "ORDER BY d.category DESC";

        $investmentAmtResult = $db->query($investmentAmtQuery, 'StatsWidgetLib::seasonInvestmentByCategory');
        foreach ($investmentAmtResult as $obj) {
            $amtData[] = $obj->deals_amt;
        }
        */

        return array('labels' => $labels, 'numData' => $numData, 'amtData' => $amtData);
    }

    public static function seasonBySeasonInvestments($startSeason = 1, $endSeason = 0, $mainCast = 1, $categories = "", $sharkExtra = "") {
        $db = wfGetDB(DB_REPLICA);
        MWDebug::log("seasonBySeasonInvestments: Season: ".$startSeason."-".$endSeason);

        $sharkQuery = "SELECT s.id, s.shark, s.full_name FROM sfs_sharks s, sfs_episode_shark_map m, sfs_episodes e ".
                    "WHERE s.id = m.shark_id AND m.episode_id = e.id ";
        
        
        if (strlen($sharkExtra) && $mainCast == 1) {
            $sharkQuery .= "AND (s.main_cast = ".$mainCast." OR s.shark IN (".$sharkExtra.")) ";
        } else if ($mainCast == 1) {
            $sharkQuery .= "AND s.main_cast = ".$mainCast." ";
        }
        if ($startSeason > 0 && $endSeason > 0) {
            $sharkQuery .= "AND e.season_id >= ".$startSeason." AND e.season_id <= ".$endSeason." ";
        } else if ($season > 0) {
            $sharkQuery .= "AND e.season_id = ".$startSeason." ";
        }
        $sharkQuery .= "ORDER BY s.main_cast DESC, s.full_name ASC";

        $sharkResult = $db->query($sharkQuery, 'StatsWidgetLib::seasonBySeasonInvestments');

        $seasons = array();
        $sharkIds = "";
        $sharks = array();

        foreach($sharkResult as $shark) {
            if (strlen($sharkIds)) $sharkIds .= ",";
            $sharkName = $shark->full_name;
            $sharkIds .= $shark->id;
            $sharks[$sharkName] = array();
        }

        $index = 0;
        for ($i = $startSeason; $i <= $endSeason; $i++) {
            foreach(array_keys($sharks) as $sharkName) {
                $sharks[$sharkName][$index] = 'NaN';
            }
            $amtQuery = "SELECT SUM(sdm.deal_amt) AS amt_invested, e.season_id, s.shark, s.full_name ".
                    "FROM sfs_deal d, sfs_sharks s, sfs_shark_deal_map sdm, sfs_episodes e ".
                    "WHERE d.id = sdm.deal_id ".
                    "AND s.id = sdm.shark_id ".
                    "AND d.episode_id = e.id ";
            
            $amtQuery .= StatsWidgetLib::builCategoryQuery($categories);
            /*
            if (strlen($categories) && strpos($categories, ',') != -1) {
                $comma_count = substr_count($categories, ',') + 1;
                $amtQuery .= "
                    AND d.id IN (
                        SELECT d.id
                        FROM sfs_deal d
                        JOIN sfs_episodes e ON d.episode_id = e.id
                        JOIN sfs_deal_category_map m ON d.id = m.deal_id
                        WHERE 1 = 1
                        AND m.category_id IN (
                        SELECT id FROM sfs_category WHERE code IN ({$categories})
                        )
                        GROUP BY d.id
                        HAVING count(*) = {$comma_count}
                    )
                ";
            } else if (strlen($categories)) {
                $amtQuery .= "AND d.category IN ({$categories}) ";
            }
            */

            $amtQuery .=  "AND s.id IN (SELECT DISTINCT id FROM sfs_sharks s WHERE 1=1 ";

            if (strlen($sharkExtra) && $mainCast == 1) {
                $amtQuery .= "AND (main_cast = ".$mainCast." OR shark IN (".$sharkExtra."))";
            } else if ($mainCast == 1) {
                $amtQuery .= "AND main_cast = ".$mainCast;
            }
            $amtQuery .= ") ".
                    "AND e.season_id = ".$i." ".
                    "GROUP BY e.season_id, s.shark ".
                    "ORDER BY s.shark ASC, e.season_id ASC ";
            
            $amtResult = $db->query($amtQuery, 'StatsWidgetLib::seasonBySeasonInvestments');
            $seasons[] = "Season ".$i;

            foreach($amtResult as $amt) {
                $sharkName = $amt->full_name;
                $amtInvested = $amt->amt_invested;
                $sharks[$sharkName][$index] = $amtInvested;
            }
            $index++;
        }

        return array('sharkData' => $sharks, 'seasonLabels' => $seasons);
    }

    public static function investmentAmountsByShark($season, $mainCast = 1, $categories = "", $shark = "") {
        $db = wfGetDB(DB_REPLICA);
        $investmentAmtQuery = "SELECT SUM(sdm.deal_amt) AS amt_invested, COUNT(d.id) as num_invested, s.shark, s.full_name ".
                                "FROM sfs_deal d, sfs_sharks s, sfs_shark_deal_map sdm, sfs_episodes e ".
                                "WHERE d.id = sdm.deal_id ".
                                "AND s.id = sdm.shark_id ".
                                "AND d.episode_id = e.id ";
        
        if (strlen($shark) && $mainCast == 1) {
            $investmentAmtQuery .= "AND (s.main_cast =  ".$mainCast." OR s.shark IN (".$shark.")) ";

        } else if ($mainCast == 1) {
            $investmentAmtQuery .= "AND s.main_cast =  ".$mainCast." ";
        }
        if ($season > 0) {
            $investmentAmtQuery .= "AND e.season_id = ".$season." ";
        }
        $investmentAmtQuery .= StatsWidgetLib::builCategoryQuery($categories);
        /*
        if (strlen($categories)) {
            $investmentAmtQuery .= "AND d.category IN (".$categories.") ";
        }
        */

        $investmentAmtQuery .= "GROUP BY s.shark ORDER BY ";
        if (strlen($shark)) {
            $investmentAmtQuery .= " (CASE WHEN s.shark IN (".$shark.") THEN 0 ELSE 1 END), ";
        }
        $investmentAmtQuery .= "s.shark ASC";

        $investmentResults = $db->query($investmentAmtQuery, 'StatsWidgetLib::investmentAmountsByShark');
                                             
			
		$labels = [];
		$colors = [];
        $amts = [];
        $nums = [];
		$i = 0;
		foreach ($investmentResults as $obj) {
            $colors[] = StatsWidgetLib::$SITE_COLORS[$i];
            $amts[] = $obj->amt_invested;
            $nums[] = $obj->num_invested;
            $labels[] = $obj->full_name;
			$i++;
        }
        return array('labels' => $labels, 'colors' => $colors, 'amts' => $amts, 'nums' => $nums);
    }

    public static function relativeInvestmentByShark($startSeason = 1, $endSeason = 0, $categories = "", $includeShark = "") {
        $db = wfGetDB(DB_REPLICA);
        $bubbleInvestmentQuery = "SELECT SUM(sdm.deal_amt) AS amt_invested, e.season_id AS season, COUNT(*) AS num_deals, s.shark, s.full_name ".
                                    "FROM sfs_deal d, sfs_sharks s, sfs_shark_deal_map sdm, sfs_episodes e ".
                                    "WHERE d.id = sdm.deal_id ".
                                    "AND s.id = sdm.shark_id ".
                                    "AND d.episode_id = e.id ";

        if (strlen($includeShark) > 0) {
            $bubbleInvestmentQuery .= "AND (s.main_cast =  1 OR s.shark IN (".$includeShark.")) ";
        } else {
            $bubbleInvestmentQuery .= "AND s.main_cast =  1 ";
        }
        if (strlen($categories)) {
            $bubbleInvestmentQuery .= "AND d.category IN (".$categories.") ";
        }

        if ($startSeason > 1) {
            $bubbleInvestmentQuery .= "AND e.season_id >= ".$startSeason." ";
        }
        if ($endSeason > 0) {
            $bubbleInvestmentQuery .= "AND e.season_id <= ".$endSeason." ";
        }

        $bubbleInvestmentQuery .= "GROUP BY season, s.shark ".
                                    "ORDER BY s.shark ASC, season ASC";
                                                    
        $bubbleInvestmentResult = $db->query($bubbleInvestmentQuery, 'StatsWidgetLib::relativeInvestmentByShark');

        $maxAmt = 0;
        $minAmt = PHP_INT_MAX;
        $data = array();
        $radius = 80;

        foreach($bubbleInvestmentResult as $obj) {
            $season = array();
            $maxAmt = max($obj->amt_invested, $maxAmt);
            $minAmt = min($obj->amt_invested, $minAmt);
            $season['amt_invested'] = $obj->amt_invested;
            if (!strlen($season['amt_invested'])) $season['amt_invested'] = 0;
            $season['season'] = $obj->season;
            $season['num_deals'] = $obj->num_deals;
            $season['categories'] = array();
            if (!array_key_exists($obj->full_name, $data)) {
                $data[$obj->full_name] = array();
                $data[$obj->full_name]['data'] = array();
            }
            $catQuery = "SELECT COUNT(*) AS investments, SUM(sdm.deal_amt) AS total, d.category, s.shark ".
                        "FROM sfs_deal d, sfs_sharks s, sfs_shark_deal_map sdm, sfs_episodes e ".
                        "WHERE d.id = sdm.deal_id ".
                        "AND s.id = sdm.shark_id ".
                        "AND d.episode_id = e.id ".
                        "AND s.shark = '".$obj->shark."' ".
                        "AND e.season_id = ".$obj->season." ";

            if (strlen($categories)) {
                $catQuery .= "AND d.category IN (".$categories.") ";
            }
            $catQuery .= "GROUP BY s.shark, d.category ".
                        "ORDER BY total DESC";

            $catResult = $db->query($catQuery, 'StatsWidgetLib::relativeInvestmentByShark');
            foreach($catResult as $cat) {
                $num_invest = $cat->investments;
                $invest_total = $cat->total;

                if ($cat->total == NULL) {
                    continue;
                }

                $invest_cat = ucfirst(strtolower($cat->category));
                $season['categories'][] = array(
                    'number' => $num_invest,
                    'total' => $invest_total,
                    'category' => $invest_cat
                );
            }
            $data[$obj->full_name]['data'][] = $season;
        }

        $labels = "";
        $i = 0;
        foreach($data as $name => &$shark) {
            $shark['label'] = $name;
            $shark['color'] = StatsWidgetLib::$SITE_COLORS[$i];
            foreach($shark['data'] as &$season) {
                $percentage = $season['amt_invested'] / $maxAmt;
                $rad = $radius * $percentage;
                $season['percentage'] = $percentage;
                $season['radius'] = $rad;
            }
            $i++;
        }

        return $data;
    }

    public static function dealsByInvestmentType($startSeason = 1, $endSeason = 0, $dealTypes = "") {
        MWDebug::log("dealsByInvestmentType: Season: ".$startSeason."-".$endSeason);

        $db = wfGetDB(DB_REPLICA);
        $seasons = array();
        
        $typesQuery = "SELECT DISTINCT deal_type FROM sfs_deal WHERE deal_type != 'NONE' ";
        if (strlen($dealTypes)) {
            $typesQuery .= "AND deal_type IN (".$dealTypes.") ";
        }
        $typesQuery .= "ORDER BY deal_type ASC";
        $typeResult = $db->query($typesQuery, 'StatsWidgetLib::dealsByInvestmentType');

        foreach($typeResult as $type) {
            $seasons[$type->deal_type] = array();
            for ($i = $startSeason; $i <= $endSeason; $i++) {
                $seasons[$type->deal_type][] = 'NaN';
            }
        }

        //print_r($seasons);

        $dealTypeQuery = "SELECT e.season_id, COUNT(*) AS num_deals, d.deal_type ".
                        "FROM sfs_deal d, sfs_episodes e ".
                        "WHERE d.episode_id = e.id ".
                        "AND d.deal_type != 'NONE' ";
        if (strlen($dealTypes)) {
            $dealTypeQuery .= "AND d.deal_type IN (".$dealTypes.") ";
        }
        $dealTypeQuery .= "AND e.season_id >= ".$startSeason." AND e.season_id <= ".$endSeason." ";
        $dealTypeQuery .= "GROUP by e.season_id, d.deal_type ORDER BY e.season_id";
        $dealTypeResult = $db->query($dealTypeQuery, 'StatsWidgetLib::dealsByInvestmentType');

        $currSeason = 0;
        $seasonCount = 0;
        foreach($dealTypeResult as $obj) {
            if ($currSeason != $obj->season_id) {
                if ($currSeason > 0) {
                    $seasonCount++;
                }
                $currSeason = $obj->season_id;
            }
            $seasons[$obj->deal_type][$seasonCount] = $obj->num_deals;
        }

        return $seasons;
    }

    public static function dealsByCategory($startSeason = 1, $endSeason = 0, $catTypes = "") {
        MWDebug::log("dealsByCategory: Season: ".$startSeason."-".$endSeason);

        $db = wfGetDB(DB_REPLICA);
        $seasons = array();
        
        $catsQuery = "SELECT count(*) AS num_deals, category FROM sfs_deal WHERE 1=1 ";
        if (strlen($catTypes)) {
            if (strpos($catTypes, ',') === TRUE) {
                $catsQuery .= "AND category IN (".$catTypes.") ";
            } else {
                $catsQuery .= "OR category IN (".$catTypes.") ";
            }
        }
        $catsQuery .= "GROUP BY category ORDER BY ";
        if (!strlen($catTypes) || (strlen($catTypes) && strpos($catTypes, ',') === FALSE)) {
            if (strlen($catTypes)) {
                $catsQuery .= "(CASE WHEN category = ".$catTypes." THEN 0 ELSE 1 END), ";
            }
            $catsQuery .= "num_deals DESC LIMIT 5"; 
        } else {
            $catsQuery .= "num_deals DESC LIMIT 5";
        }
        $catResult = $db->query($catsQuery, 'StatsWidgetLib::dealsByCategory');

        $builtCats = array();
        foreach($catResult as $type) {
            $builtCats[] = "'".$type->category."'";
            $seasons[$type->category] = array();
            for ($i = $startSeason; $i <= $endSeason; $i++) {
                $seasons[$type->category][] = 'NaN';
            }
        }
        $catTypes = implode(",", $builtCats);

        //print_r($seasons);

        $catNumQuery = "SELECT e.season_id, COUNT(*) AS num_deals, d.category ".
                        "FROM sfs_deal d, sfs_episodes e ".
                        "WHERE d.episode_id = e.id ";
        if (strlen($catTypes)) {
            $catNumQuery .= "AND d.category IN (".$catTypes.") ";
        }
        $catNumQuery .= "AND e.season_id >= ".$startSeason." AND e.season_id <= ".$endSeason." ";
        $catNumQuery .= "GROUP by e.season_id, d.category ORDER BY e.season_id";
        $catNumResult = $db->query($catNumQuery, 'StatsWidgetLib::dealsByCategory');

        $currSeason = 0;
        $seasonCount = 0;
        foreach($catNumResult as $obj) {
            if ($currSeason != $obj->season_id) {
                if ($currSeason > 0) {
                    $seasonCount++;
                }
                $currSeason = $obj->season_id;
            }
            $seasons[$obj->category][$seasonCount] = $obj->num_deals;
        }

        return $seasons;
    }

    public static function biteBySeason($startSeason, $endSeason, $categories = "", $shark = "", $avg = false) {
        MWDebug::log("biteBySeason: Season: ".$startSeason."-".$endSeason);

        $db = wfGetDB(DB_REPLICA);
        $labels = array();
        $proposedData = array();
        $biteData = array();

        $dealCapQuery = "SELECT s.season, ";
        if ($avg) {
            $dealCapQuery .= "AVG(d.proposed_money_amt / d.proposed_equity_amt) AS proposed_cap, AVG(d.deal_money_amt / d.deal_equity_amt) AS deal_cap  ";
        } else {
            $dealCapQuery .= "SUM(d.proposed_money_amt / d.proposed_equity_amt) AS proposed_cap, SUM(d.deal_money_amt / d.deal_equity_amt) AS deal_cap  ";
        }
        $dealCapQuery .= "FROM sfs_deal d , sfs_episodes e, sfs_season s ";
        if (strlen($shark)) {
            $dealCapQuery .= ", sfs_shark_deal_map sdm, sfs_sharks sharks ";
        }
        $dealCapQuery .= "WHERE d.episode_id = e.id ".
                        "AND e.season_id = s.id ".
                        "AND d.deal_type != 'NONE' ";
        if (strlen($categories)) {
            $dealCapQuery .= "AND d.category IN (".$categories.") ";
        }
        if (strlen($shark)) {
            $dealCapQuery .= "AND d.id = sdm.deal_id ".
                            "AND sdm.shark_id = sharks.id ".
                            "AND sharks.shark IN (".$shark.") ";
        }
        $dealCapQuery .= "GROUP BY s.season ".
                        "ORDER BY s.id ASC"; 

        $dealCapResult = $db->query($dealCapQuery, 'StatsWidgetLib::biteBySeason');
        foreach($dealCapResult as $seasonData) {
            $labels[] = $seasonData->season;
            $proposedData[] = $seasonData->proposed_cap;
            $biteData[] = $seasonData->deal_cap;
        }

        return array(
            'labels' => $labels,
            'proposed' => $proposedData,
            'bite' => $biteData
        );
    }

    public static function teamupsByShark($categories, $shark, $limit) {
        // TODO possibly set a lower limit parameter, 0 to see all, but filter by a certain number...
        //MWDebug::log("biteBySeason: Season: ".$startSeason."-".$endSeason);
        $sharkId = StatsWidgetLib::getSharkId($shark);

        $db = wfGetDB(DB_REPLICA);
        $labels = [];
        $data = [];
        $colors = [];

        $query = "SELECT q.shark_count, q.shark_id, s.full_name, s.main_cast FROM 
            (SELECT COUNT(*) as shark_count, dm.shark_id
            FROM sfs_shark_deal_map dm, sfs_deal d
            WHERE 1 = 1
            AND dm.deal_id = d.id
            AND dm.deal_id IN (SELECT sdm.deal_id FROM sfs_shark_deal_map sdm WHERE 1=1 AND sdm.shark_id  = $sharkId) ";

        if (strlen($categories)) {
           $query .= "AND d.category IN ($categories) ";
        }
        $query .= "GROUP BY dm.shark_id) q, sfs_sharks s ";

        if (strlen($categories)) {
            $query .= "WHERE q.shark_count > 0 ";
        } else if ($limit > 0) {
            $query .= "WHERE q.shark_count >= ".$limit." ";
        } else {
            $query .= "WHERE q.shark_count > 2 ";
        }
        $query .= "AND q.shark_id = s.id
                AND q.shark_id != $sharkId
                ORDER BY q.shark_count DESC";

        $teampUpResult = $db->query($query, 'StatsWidgetLib::teamupsByShark');
        $i = 0;
        $guestCount = 10;
        foreach($teampUpResult as $teamUp) {
            $labels[] = $teamUp->full_name;
            $data[] = $teamUp->shark_count;

            if ($teamUp->main_cast) {
                $colors[] = StatsWidgetLib::$SITE_COLORS[$i];
                $i++;
            } else {
                $colors[] = StatsWidgetLib::$SITE_COLORS[$guestCount];
                $guestCount++;
                if ($guestCount > 12) $guestCount = 7;
            }
        }

        return array('labels' => $labels, 'nums' => $data, 'colors' => $colors);
    }

    public static function dealTypeLabel($key) {
        if ($key == "IP") {
            return $key;
        } else if ($key == 'DEBT') {
            return "Debt";
        } else if ($key == 'EQ') {
            return "Equity";
        } else if ($key == 'EQ-R') {
            return "Equity/Royalty Mix";
        } else if ($key == 'EQ-DEBT') {
            return "Equity/Debt Mix";
        } else if ($key == 'SALE') {
            return "Buyout";
        } else if ($key == 'ROYALTY') {
            return "Royalty";
        } else if ($key == 'DEBT-R') {
            return "Debt/Royalty Mix";
        } else if ($key == 'NONE') {
            return "No Deal";
        }
        return $key;
    }

    public static function categoryLabel($key) {
        return ucfirst(strtolower($key));
    }

    public static function seasonLabels($startSeason, $endSeason) {
        $seasonLabels = array();
        for ($i = $startSeason; $i <= $endSeason; $i++) {
            $seasonLabels[] = "Season ".$i;
        }
        return $seasonLabels;
    }

    public static function normalizeList($list) {
        if (strpos($list, ',')) {
            $items = explode(",", $list);
            $modItems = array();
            foreach ($items as $item) {
                $modItems[] = "'".trim($item)."'";
            }
            $items = implode(",", $modItems);
        } else {
            $items = "'".$list."'";
        }
        return $items;
    }

    public static function cleanShark($shark) {
        return trim(str_replace("'", "", $shark));
    }

    public static function builCategoryQuery($categories) {
        $query = " ";
        if (strlen($categories) && strpos($categories, ',') != -1) {
            $comma_count = substr_count($categories, ',') + 1;
            $query = "
                AND d.id IN (
                    SELECT d.id
                    FROM sfs_deal d
                    JOIN sfs_episodes e ON d.episode_id = e.id
                    JOIN sfs_deal_category_map m ON d.id = m.deal_id
                    WHERE 1 = 1
                    AND m.category_id IN (
                        SELECT id FROM sfs_category WHERE code IN ({$categories})
                    )
                    GROUP BY d.id
                    HAVING count(*) = {$comma_count}
                )
            ";
        } else if (strlen($categories)) {
            $query = " AND d.category IN ({$categories}) ";
        }
        return $query;
    }
}

?>