<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_Review
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */
$installer = $this;
$installer->startSetup();



$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('mt_review_detail')};
CREATE TABLE {$this->getTable('mt_review_detail')} (
  `detail_id` bigint(20) unsigned NOT NULL auto_increment,
  `review_id` bigint(20) unsigned NOT NULL default '0',
  `parent_review_id` int(11) unsigned NOT NULL,
  `store_id` smallint(6) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `detail` text NOT NULL,
  `nickname` varchar(128) NOT NULL default '',
  `customer_id` int(10) unsigned default NULL,
  `like` int(11) NOT NULL,
  `notlike` int(11) NOT NULL,
  PRIMARY KEY  (`detail_id`),
  KEY `FK_REVIEW_DETAIL_REVIEW` (`review_id`),
  CONSTRAINT `FK_REVIEW_DETAIL_REVIEW` FOREIGN KEY (`review_id`) REFERENCES {$this->getTable('review')} (`review_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Review detail information';
");

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('mt_review_report')};
CREATE TABLE {$this->getTable('mt_review_report')}(
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `review_id` bigint(20) unsigned NOT NULL,
  `customer_name` varchar(255) NOT NULL default '',
  `customer_id` bigint(20) unsigned NOT NULL,
  `report_at` DATETIME NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_REVIEW_REPORT` (`review_id`),
  KEY `FK_REIVEW_REPORT_STORE` (`store_id`),
  CONSTRAINT `FK_REVIEW_REPORT` FOREIGN KEY (`review_id`) REFERENCES `{$this->getTable('review')}` (`review_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_REIVEW_REPORT_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('mt_review_comment')};
CREATE TABLE {$this->getTable('mt_review_comment')}(
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `review_id` bigint(20) unsigned NOT NULL,
  `customer_name` varchar(255) NOT NULL default '',
  `customer_id` bigint(20) unsigned NOT NULL,
  `comments` text NOT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_REVIEW_COMMENT` (`review_id`),
  KEY `FK_REIVEW_COMMENT_STORE` (`store_id`),
  CONSTRAINT `FK_REVIEW_COMMENT` FOREIGN KEY (`review_id`) REFERENCES `{$this->getTable('review')}` (`review_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_REIVEW_COMMENT_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('mt_review_helpfulness')};
CREATE TABLE {$this->getTable('mt_review_helpfulness')}(
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `review_id` bigint(20) unsigned NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `FK_REVIEW_HELPFUL_REVIEW` (`review_id`),
  CONSTRAINT `FK_REVIEW_HELPFUL_REVIEW` FOREIGN KEY (`review_id`) REFERENCES {$this->getTable('review')} (`review_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$installer->endSetup();
