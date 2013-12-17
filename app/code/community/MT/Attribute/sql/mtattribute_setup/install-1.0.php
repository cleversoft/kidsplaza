<?php
/**
 * @category    MT
 * @package     MT_Attribute
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->addColumn(
    $installer->getTable('eav/attribute_option'),
    'image',
    'VARCHAR(255) NULL DEFAULT NULL COMMENT "Attribute Image"'
);
$installer->getConnection()->addColumn(
    $installer->getTable('eav/attribute_option'),
    'thumb',
    'VARCHAR(255) NULL DEFAULT NULL COMMENT "Attribute Image Thumb"'
);