<?php
/****************************************************** 
 * @author http://www.9magentothemes.com
 * @copyright (C) 2012- 9MagentoThemes.Com
 * @license PHP files are GNU/GPL
*******************************************************/
?>
<?php

$installer = $this;

$installer->startSetup();

$installer->run("
		SET FOREIGN_KEY_CHECKS = 0;
		DROP TABLE IF EXISTS {$this->getTable('megamenu_group')};
		DROP TABLE IF EXISTS {$this->getTable('megamenu_group_store')};
		DROP TABLE IF EXISTS {$this->getTable('megamenu_relation_group')};
		DROP TABLE IF EXISTS {$this->getTable('megamenu')};
		CREATE TABLE {$this->getTable('megamenu')} (
		  `megamenu_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `title` varchar(255) NOT NULL DEFAULT '',
		  `label` varchar(50) NOT NULL DEFAULT '',
		  `image` varchar(255) NOT NULL DEFAULT '',
		  `parent_id` int(11) NOT NULL DEFAULT '0',
		  `is_group` smallint(6) NOT NULL DEFAULT '2',
		  `width` varchar(255) DEFAULT NULL,
		  `subitem_width` varchar(255) DEFAULT NULL,
		  `article` int(11) DEFAULT NULL,
		  `col` varchar(255) DEFAULT NULL,
		  `type` varchar(255) NOT NULL,
		  `is_content` smallint(6) NOT NULL DEFAULT '2',
		  `show_title` smallint(6) NOT NULL DEFAULT '1',
		  `level` smallint(6) NOT NULL DEFAULT '0',
		  `status` smallint(6) NOT NULL DEFAULT '0',
		  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
		  `position` smallint(5) unsigned NOT NULL DEFAULT '0',
		  `show_sub` smallint(6) NOT NULL DEFAULT '0',
		  `url` varchar(255) DEFAULT NULL,
		  `nofollow` smallint(6) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`megamenu_id`)
		) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;
		DROP TABLE IF EXISTS {$this->getTable('megamenu_store')};
		CREATE TABLE {$this->getTable('megamenu_store')} (
		  `megamenu_id` int(11) unsigned NOT NULL,
		  `store_id` smallint(5) unsigned NOT NULL,
		  PRIMARY KEY (`megamenu_id`,`store_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		SET FOREIGN_KEY_CHECKS = 1;
                    ");
$installer->endSetup();