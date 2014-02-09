<?php
/**
 * @category    MT
 * @package     MT_CatalogSlider
 * @copyright   Copyright (C) 2008-2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
$this->startSetup();
$this->addAttribute('catalog_category', 'slider_params', array(
    'input'     => 'text',
    'type'      => 'text',
    'backend'   => '',
    'visible'   => false,
    'required'  => false,
    'visible_on_front' => true,
    'global'    => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE
));
$this->endSetup();