<?php
/**
 * @category    MT
 * @package     MT_Location
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
$installer = $this;
$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('mtlocation/location')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(255) NOT NULL,
  `position` varchar(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();