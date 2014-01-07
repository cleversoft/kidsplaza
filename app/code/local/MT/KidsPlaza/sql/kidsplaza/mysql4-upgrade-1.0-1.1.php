<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
$this->startSetup();
$this->addAttribute('catalog_product', 'brand', array(
    'group'     => 'General',
    'input'     => 'select',
    'type'      => 'int',
    'label'     => 'Brand',
    'backend'   => '',
    'frontend'  => '',
    'visible'   => true,
    'required'  => false,
    'visible_on_front' => true,
    'user_defined' => true,
    'searchable' => true,
    'filterable' => true,
    'comparable' => true,
    'visible_in_advanced_search' => true,
    'is_html_allowed_on_front' => true,
    'used_in_product_listing' => true,
    'used_for_sort_by' => false,
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
));
$this->endSetup();