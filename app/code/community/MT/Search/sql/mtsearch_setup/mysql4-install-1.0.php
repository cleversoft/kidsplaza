<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
$installer = $this;
$installer->startSetup();

// add new field to catalog_eav_attribute
$installer->getConnection()->addColumn($installer->getTable('catalog/eav_attribute'), 'search_weight', array(
	'type'		=> Varien_Db_Ddl_Table::TYPE_FLOAT,
	'unsigned'	=> true,
	'nullable'	=> true,
	'default'	=> 1,
	'comment'	=> 'Search Weight Number'
));

$installer->endSetup();