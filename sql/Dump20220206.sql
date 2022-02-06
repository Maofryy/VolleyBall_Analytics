-- sport_analytics.club definition

CREATE TABLE `club` (
  `club_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`club_id`)
) ENGINE=InnoDB AUTO_INCREMENT=314 DEFAULT CHARSET=latin1;


-- sport_analytics.departement definition

CREATE TABLE `departement` (
  `departement_id` int(11) NOT NULL AUTO_INCREMENT,
  `departement_code` varchar(3) CHARACTER SET utf8 DEFAULT NULL,
  `departement_nom` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `departement_nom_uppercase` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `departement_slug` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `departement_nom_soundex` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`departement_id`),
  KEY `departement_slug` (`departement_slug`),
  KEY `departement_code` (`departement_code`),
  KEY `departement_nom_soundex` (`departement_nom_soundex`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=latin1;


-- sport_analytics.division definition

CREATE TABLE `division` (
  `division_id` int(11) NOT NULL AUTO_INCREMENT,
  `div_name` varchar(45) DEFAULT NULL,
  `div_code` varchar(45) DEFAULT NULL,
  `order` int(2) DEFAULT NULL,
  PRIMARY KEY (`division_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;


-- sport_analytics.ligue definition

CREATE TABLE `ligue` (
  `ligue_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`ligue_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2165 DEFAULT CHARSET=latin1;


-- sport_analytics.`match` definition

CREATE TABLE `match` (
  `match_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `team_home_id` int(11) NOT NULL,
  `team_out_id` int(11) NOT NULL,
  `winner_team_id` int(10) unsigned DEFAULT NULL,
  `score` varchar(100) DEFAULT NULL,
  `div_code` varchar(3) NOT NULL,
  `div_pool` varchar(45) NOT NULL,
  `match_number` varchar(45) NOT NULL,
  `match_day` int(2) NOT NULL,
  `city` varchar(45) NOT NULL,
  `gym` varchar(45) NOT NULL,
  `category` varchar(45) NOT NULL,
  `ligue` varchar(45) NOT NULL,
  `date_match` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`match_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2144 DEFAULT CHARSET=latin1;


-- sport_analytics.match_other_player definition

CREATE TABLE `match_other_player` (
  `match_id` int(11) NOT NULL,
  `licence` int(11) NOT NULL,
  `function_id` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- sport_analytics.match_set definition

CREATE TABLE `match_set` (
  `match_id` int(11) NOT NULL,
  `set` int(11) NOT NULL,
  `team_id_server` int(11) NOT NULL,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL,
  PRIMARY KEY (`match_id`,`set`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- sport_analytics.match_set_details definition

CREATE TABLE `match_set_details` (
  `match_id` int(11) NOT NULL,
  `set` int(11) NOT NULL,
  `date_start` datetime NOT NULL,
  `date_end` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- sport_analytics.match_set_position definition

CREATE TABLE `match_set_position` (
  `match_set_position_id` int(11) NOT NULL AUTO_INCREMENT,
  `match_id` int(11) NOT NULL,
  `set` int(11) NOT NULL,
  `position_1` int(11) NOT NULL,
  `position_2` int(11) NOT NULL,
  `position_3` int(11) NOT NULL,
  `position_4` int(11) NOT NULL,
  `position_5` int(11) NOT NULL,
  `position_6` int(11) NOT NULL,
  `team_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`match_set_position_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16037 DEFAULT CHARSET=latin1;


-- sport_analytics.match_set_rotation definition

CREATE TABLE `match_set_rotation` (
  `match_id` int(11) NOT NULL,
  `set` int(11) NOT NULL,
  `point` int(11) NOT NULL,
  `team_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- sport_analytics.match_set_substitution definition

CREATE TABLE `match_set_substitution` (
  `match_set_substitution_id` int(11) NOT NULL AUTO_INCREMENT,
  `match_id` int(11) NOT NULL,
  `set` int(11) NOT NULL,
  `licence_in` int(11) NOT NULL,
  `licence_out` int(11) NOT NULL,
  `score` text NOT NULL,
  `team_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`match_set_substitution_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24245 DEFAULT CHARSET=latin1;


-- sport_analytics.match_set_timeout definition

CREATE TABLE `match_set_timeout` (
  `match_set_timeout_id` int(11) NOT NULL AUTO_INCREMENT,
  `match_id` int(11) NOT NULL,
  `set` int(11) NOT NULL,
  `score` text NOT NULL,
  `team_id` int(11) NOT NULL,
  PRIMARY KEY (`match_set_timeout_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20160 DEFAULT CHARSET=latin1;


-- sport_analytics.player definition

CREATE TABLE `player` (
  `licence` int(11) NOT NULL,
  `first_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`licence`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- sport_analytics.season definition

CREATE TABLE `season` (
  `season_id` int(11) NOT NULL,
  `year` text DEFAULT NULL,
  PRIMARY KEY (`season_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- sport_analytics.sport definition

CREATE TABLE `sport` (
  `sport_id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  PRIMARY KEY (`sport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- sport_analytics.team definition

CREATE TABLE `team` (
  `team_id` int(11) NOT NULL AUTO_INCREMENT,
  `club_id` int(11) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `gender` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=314 DEFAULT CHARSET=latin1;


-- sport_analytics.team_player definition

CREATE TABLE `team_player` (
  `team_player_id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `season_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `match_id` int(10) unsigned NOT NULL,
  `function_id` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`team_player_id`)
) ENGINE=InnoDB AUTO_INCREMENT=44407 DEFAULT CHARSET=latin1;


-- sport_analytics.users definition

CREATE TABLE `users` (
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `licence` varchar(100) DEFAULT NULL,
  `pseudo` varchar(100) DEFAULT NULL,
  `created_at` varchar(100) NOT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`user_id`),
  KEY `users_user_id_IDX` (`user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;