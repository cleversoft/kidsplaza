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
ALTER TABLE {$this->getTable('productquestions/productquestions')}
ADD `category_id` int(10) unsigned NULL default null AFTER `parent_question_id`;
    ");

$installer->endSetup();