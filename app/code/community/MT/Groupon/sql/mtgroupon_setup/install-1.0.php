<?php
/**
 * @category    MT
 * @package     MT_Groupon
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
$this->startSetup();
$this->addAttribute('catalog_product', 'groupon_enable', array(
    'group'     => 'Groupon',
    'input'     => 'text',
    'type'      => 'int',
    'label'     => 'groupon_enable',
    'backend'   => '',
    'frontend'  => '',
    'visible'   => false,
    'required'  => false,
    'visible_on_front' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_in_advanced_search' => false,
    'is_filterable_in_search' => false,
    'is_html_allowed_on_front' => false,
    'used_in_product_listing' => true,
    'used_for_sort_by' => false,
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE
));
$this->addAttribute('catalog_product', 'groupon_from', array(
    'group'     => 'Groupon',
    'input'     => 'text',
    'type'      => 'datetime',
    'label'     => 'groupon_from',
    'backend'   => '',
    'frontend'  => '',
    'visible'   => false,
    'required'  => false,
    'visible_on_front' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_in_advanced_search' => false,
    'is_filterable_in_search' => false,
    'is_html_allowed_on_front' => false,
    'used_in_product_listing' => true,
    'used_for_sort_by' => false,
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE
));
$this->addAttribute('catalog_product', 'groupon_to', array(
    'group'     => 'Groupon',
    'input'     => 'text',
    'type'      => 'datetime',
    'label'     => 'groupon_to',
    'backend'   => '',
    'frontend'  => '',
    'visible'   => false,
    'required'  => false,
    'visible_on_front' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_in_advanced_search' => false,
    'is_filterable_in_search' => false,
    'is_html_allowed_on_front' => false,
    'used_in_product_listing' => true,
    'used_for_sort_by' => false,
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE
));
$this->addAttribute('catalog_product', 'groupon_qty', array(
    'group'     => 'Groupon',
    'input'     => 'text',
    'type'      => 'int',
    'label'     => 'groupon_qty',
    'backend'   => '',
    'frontend'  => '',
    'visible'   => false,
    'required'  => false,
    'visible_on_front' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_in_advanced_search' => false,
    'is_filterable_in_search' => false,
    'is_html_allowed_on_front' => false,
    'used_in_product_listing' => true,
    'used_for_sort_by' => false,
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE
));
$this->addAttribute('catalog_product', 'groupon_bought', array(
    'group'     => 'Groupon',
    'input'     => 'text',
    'type'      => 'int',
    'label'     => 'groupon_bought',
    'backend'   => '',
    'frontend'  => '',
    'visible'   => false,
    'required'  => false,
    'visible_on_front' => false,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_in_advanced_search' => false,
    'is_filterable_in_search' => false,
    'is_html_allowed_on_front' => false,
    'used_in_product_listing' => true,
    'used_for_sort_by' => false,
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE
));
$this->endSetup();