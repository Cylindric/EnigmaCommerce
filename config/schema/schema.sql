/**
* Enigma : Online Sales Management. (http://www.enigmagen.org)
* Licensed under The MIT License
* Redistributions of files must retain the above copyright notice.
**/

DROP TABLE IF EXISTS `enigma3_baskets`;
CREATE TABLE IF NOT EXISTS `enigma3_baskets` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `created` datetime DEFAULT NULL,
    `modified` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `enigma3_baskets_details`;
CREATE TABLE IF NOT EXISTS `enigma3_baskets_details` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `basket_id` int(11) NOT NULL,
    `detail_id` int(11) NOT NULL,
    `created` datetime DEFAULT NULL,
    `modified` datetime DEFAULT NULL,
    UNIQUE KEY ixBasketDetailID (`basket_id`, `detail_id`),
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `enigma3_categories`;
CREATE TABLE IF NOT EXISTS `enigma3_categories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `parent_id` int(11) NOT NULL default 0,
    `lft` int(11) NOT NULL default 0,
    `rght` int(11) NOT NULL default 0,
    `name` varchar(100) NOT NULL,
    `slug` varchar(100) NOT NULL default '',
    `description` text NOT NULL default '',
    `stockcode_prefix` varchar(10) NOT NULL default '',
    `status_id` int(11) NOT NULL default 1,
    `created` datetime DEFAULT NULL,
    `modified` datetime DEFAULT NULL,
    `legacy_id` int(11) NOT NULL default 0,
    `legacy_parent_id` int(11) NOT NULL default 0,
    PRIMARY KEY (`id`),
    KEY `ixSlug` (`slug`),
    KEY `ixName` (`name`),
    KEY `ixStatusId` (`status_id`),
    KEY `ixParentId` (`parent_id`),
    KEY `ixLft` (`lft`),
    KEY `ixRght` (`rght`),
    KEY `ixTree` (`lft`, `rght`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `enigma3_category_items`;
CREATE TABLE IF NOT EXISTS `enigma3_category_items` (
    `id` int NOT NULL AUTO_INCREMENT,
    `category_id` int NOT NULL,
    `item_id` int NOT NULL,
    `is_primary` tinyint(1) NOT NULL DEFAULT 0,
    `created` datetime DEFAULT NULL,
    `modified` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY ixCategoryItemID (`category_id`, `item_id`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `enigma3_groups`;
CREATE TABLE IF NOT EXISTS `enigma3_groups` (
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(128) NOT NULL,
    `access_admin` int(1) NOT NULL DEFAULT 0,
    `created` datetime DEFAULT NULL,
    `modified` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
);


DROP TABLE IF EXISTS `enigma3_items`;
CREATE TABLE IF NOT EXISTS `enigma3_items` (
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(128) NOT NULL,
    `slug` varchar(100) NOT NULL default '',
    `description` text NOT NULL default '',
    `status_id` int(11) NOT NULL default 1,
    `created` datetime DEFAULT NULL,
    `modified` datetime DEFAULT NULL,
    `legacy_id` int(11) NOT NULL default 0,
    KEY `ixSlug` (`slug`),
    KEY `ixStatusId` (`status_id`),
    PRIMARY KEY (`id`)
);


DROP TABLE IF EXISTS `enigma3_statuses`;
CREATE TABLE IF NOT EXISTS `enigma3_statuses` (
    `id` int NOT NULL,
    `name` varchar(128) NOT NULL,
    `created` datetime DEFAULT NULL,
    `modified` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `enigma3_units`;
CREATE TABLE IF NOT EXISTS `enigma3_units` (
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(128) NOT NULL,
    `plural_name` varchar(128) NOT NULL,
    `unit` varchar(10) NOT NULL,
    `parent_id` int NOT NULL DEFAULT 0,
    `scale_factor` FLOAT NOT NULL DEFAULT 1.00,
    `created` datetime DEFAULT NULL,
    `modified` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `ixParentId` (`parent_id`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `enigma3_users`;
CREATE TABLE IF NOT EXISTS `enigma3_users` (
    `id` int NOT NULL AUTO_INCREMENT,
    `group_id` int NOT NULL DEFAULT 1,
    `username` varchar(128) NOT NULL,
    `password` varchar(128) NOT NULL,
    `created` datetime DEFAULT NULL,
    `modified` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `ixUsername` (`username`),
    KEY `ixGroup` (`group_id`),
    KEY `ixUsernamePassword` (`username`, `password`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `enigma3_variations`;
CREATE TABLE IF NOT EXISTS `enigma3_variations` (
    `id` int NOT NULL AUTO_INCREMENT,
    `item_id` int NOT NULL,
    `unit_id` int NOT NULL,
    `size` decimal(10,2) NOT NULL DEFAULT 0.00,
    `name` varchar(128) NOT NULL,
    `slug` varchar(100) NOT NULL default '',
    `price` decimal(10,2) NOT NULL DEFAULT 0.00,
    `rrp` decimal(10,2) NOT NULL DEFAULT 0.00,
    `stockcode` varchar(10) NOT NULL default '',
    `status_id` int(11) NOT NULL default 1,
    `created` datetime DEFAULT NULL,
    `modified` datetime DEFAULT NULL,
    `legacy_id` int(11) NOT NULL default 0,
    PRIMARY KEY (`id`),
    KEY `ixItemId` (`item_id`),
    KEY `ixSlug` (`slug`),
    KEY `ixStatusId` (`status_id`)
) ENGINE=MyISAM;


DROP TABLE IF EXISTS `enigma3_vatrates`;
CREATE TABLE IF NOT EXISTS `enigma3_vatrates` (
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(128) NOT NULL,
    `rate` float(10,2) NOT NULL DEFAULT 0,
    `created` datetime DEFAULT NULL,
    `modified` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;
