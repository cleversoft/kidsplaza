<?php
/**
 * @category    MT
 * @package     KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->run("
ALTER TABLE {$this->getTable('blog/blog')} ADD `promotion_start` DATE NULL DEFAULT NULL;
ALTER TABLE {$this->getTable('blog/blog')} ADD `promotion_end` DATE NULL DEFAULT NULL;
");
$installer->endSetup();