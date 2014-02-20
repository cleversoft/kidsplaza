<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_ProductQuestions
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
CREATE TABLE {$this->getTable('productquestions/categories')} (
    `cat_id` int(10) unsigned NOT NULL auto_increment,
    `name` varchar(255) NULL DEFAULT NULL,
    `text` text NOT NULL,
    `status` tinyint(2) NOT NULL default '1',
    PRIMARY KEY (`cat_id`),
    KEY `question_status` (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();