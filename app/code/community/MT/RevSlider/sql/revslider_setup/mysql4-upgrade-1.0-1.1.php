<?php
/**
 * @category    MT
 * @package     MT_RevSlider
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('revslider/slider'),
    'store_ids',
    'VARCHAR(255) NULL DEFAULT NULL COMMENT "Slider Stores"'
);