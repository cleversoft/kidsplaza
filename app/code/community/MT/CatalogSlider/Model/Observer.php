<?php
/**
 * @category    MT
 * @package     MT_CatalogSlider
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_CatalogSlider_Model_Observer{
    public function adminhtmlCatalogCategoryTabs($observer){
        /* @var $tabs Mage_Adminhtml_Block_Catalog_Category_Tabs */
        $tabs = $observer->getEvent()->getTabs();
        $tabs->addTab('slider_section', array(
            'label'     => Mage::helper('mtcatalogslider')->__('Slider Settings'),
            'content'   => $tabs->getLayout()->createBlock('mtcatalogslider/adminhtml_catalog_category_tab_slider')->toHtml()
        ));
    }
}