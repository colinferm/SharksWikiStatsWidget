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

DROP VIEW IF EXISTS statsshark.sfs_investment_by_shark;

DROP TABLE IF EXISTS `sfs_investment_by_shark`;
CREATE TABLE `sfs_investment_by_shark` (
  investments INT NOT NULL DEFAULT '0',
  total decimal(9,2),
  category VARCHAR(100),
  deal_type VARCHAR(20),
  shark VARCHAR(150),
  shark_name VARCHAR(150),
  shark_id INT,
  main_cast TINYINT(4),
  season_id INT
);
CREATE INDEX idx_shark ON sfs_investment_by_shark (shark);
CREATE INDEX idx_category ON sfs_investment_by_shark (category);
CREATE INDEX idx_deal_type ON sfs_investment_by_shark (deal_type);
CREATE INDEX idx_main_cast ON sfs_investment_by_shark (main_cast);
CREATE INDEX idx_season ON sfs_investment_by_shark (season_id);

INSERT INTO sfs_investment_by_shark
SELECT COUNT(d.id) AS investments, SUM(sdm.deal_amt) AS total, d.category, d.deal_type, s.shark, s.full_name AS shark_name, s.id AS shark_id, s.main_cast, e.season_id 
FROM sfs_deal d
JOIN sfs_episodes e ON (d.episode_id = e.id)
LEFT JOIN sfs_shark_deal_map sdm ON (d.id = sdm.deal_id)
LEFT JOIN sfs_sharks s ON (s.id = sdm.shark_id)
GROUP BY s.shark, e.season_id, d.category, d.deal_type
ORDER BY e.season_id ASC, investments DESC
