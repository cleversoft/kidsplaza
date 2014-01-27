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
CREATE TABLE {$this->getTable('productquestions/productquestions')} (
    `question_id` int(10) unsigned NOT NULL auto_increment,
    `question_status` tinyint(2) NOT NULL default '1',
    `question_product_id` int(10) unsigned NULL default null,
    `question_store_id` int(11) NOT NULL COMMENT 'asked from',
    `question_store_ids` varchar(255) NOT NULL COMMENT 'displayed on',
    `question_product_name` varchar(255) NULL DEFAULT NULL,
    `question_author_name` varchar(255) NOT NULL,
    `question_author_email` varchar(255) NOT NULL,
    `question_date` datetime NOT NULL,
    `question_text` text NOT NULL,
    `question_reply_text` text NOT NULL,
    PRIMARY KEY (`question_id`),
    KEY `question_status` (`question_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->endSetup();