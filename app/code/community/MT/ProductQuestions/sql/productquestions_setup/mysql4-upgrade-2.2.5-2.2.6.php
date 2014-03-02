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
ADD `identifier` varchar(255) NOT NULL AFTER `question_text`;

ALTER TABLE {$this->getTable('productquestions/categories')}
ADD `identifier` varchar(255) NOT NULL AFTER `name`;
    ");

$installer->endSetup();