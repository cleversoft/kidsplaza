<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
$this->startSetup();

$this->removeAttribute('catalog_product', 'products_combo');
$this->addAttribute('catalog_product', 'products_combo', array(
    'group'     => 'General',
    'input'     => 'text',
    'type'      => 'text',
    'label'     => 'Products Combo',
    'backend'   => '',
    'frontend'  => '',
    'visible'   => false,
    'required'  => false,
    'visible_on_front' => true,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_in_advanced_search' => false,
    'is_filterable_in_search' => false,
    'is_html_allowed_on_front' => true,
    'used_in_product_listing' => true,
    'used_for_sort_by' => false,
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE
));

$this->endSetup();