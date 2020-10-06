DROP TABLE IF EXISTS `sfs_deal`;
CREATE TABLE `sfs_deal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `episode_id` int(11) NOT NULL,
  `company` varchar(150) NOT NULL,
  `deal_type` varchar(20) NOT NULL,
  `proposed_money_amt` decimal(9,2) DEFAULT NULL,
  `proposed_equity_amt` decimal(4,3) DEFAULT NULL,
  `deal_money_amt` decimal(9,2) DEFAULT NULL,
  `deal_equity_amt` decimal(4,3) DEFAULT NULL,
  `deal_loan_amt` decimal(9,2) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=802 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `sfs_episode_shark_map`;
CREATE TABLE `sfs_episode_shark_map` (
  `episode_id` int(11) NOT NULL,
  `shark_id` int(11) NOT NULL,
  INDEX (`episode_id`, `shark_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `sfs_episodes`;
CREATE TABLE `sfs_episodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `episode_num` varchar(5) NOT NULL,
  `season_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=206 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `sfs_season`;
CREATE TABLE `sfs_season` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `season` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `sfs_shark_deal_map`;
CREATE TABLE `sfs_shark_deal_map` (
  `shark_id` int(11) NOT NULL,
  `deal_id` int(11) NOT NULL,
  `deal_amt` decimal(9,2) DEFAULT NULL,
  `deal_eq` decimal(4,3) DEFAULT NULL,
  `deal_add_eq` decimal(4,3) DEFAULT NULL,
  INDEX (`shark_id`, `deal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `sfs_sharks`;
CREATE TABLE `sfs_sharks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shark` varchar(150) NOT NULL,
  `main_cast` tinyint(4) DEFAULT 0,
  `full_name` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `sfs_category`;
CREATE TABLE `sfs_category` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`category` varchar(50) NOT NULL,
	`code` VARCHAR(20) NOT NULL,
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `sfs_deal_category_map`;
CREATE TABLE `sfs_deal_category_map` (
	`deal_id` int(11) NOT NULL,
	`category_id` int(11) NOT NULL,
	`primary` tinyint NOT NULL DEFAULT 0,
	INDEX (`deal_id`, `category_id`, `primary`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `sfs_deal_trivia`;
CREATE TABLE `sfs_deal_category_map` (
	`deal_id` int(11) NOT NULL,
	`trivia` VARCHAR(255) NOT NULL,
	INDEX (`deal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE OR REPLACE VIEW statsshark.sfs_investment_by_shark AS
SELECT COUNT(*) AS investments, SUM(sdm.deal_amt) AS total, d.category, s.shark, s.full_name AS shark_name, s.id AS shark_id, s.main_cast, e.season_id 
FROM sfs_deal d, sfs_sharks s, sfs_shark_deal_map sdm, sfs_episodes e 
WHERE d.id = sdm.deal_id 
AND s.id = sdm.shark_id 
AND d.episode_id = e.id 
GROUP BY s.shark, e.season_id, d.category 
ORDER BY e.season_id ASC, investments DESC
