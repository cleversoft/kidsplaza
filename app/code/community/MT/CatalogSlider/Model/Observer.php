<?php
/**
 * @category    MT
 * @package     MT_CatalogSlider
 * @copyright   Copyright (C) 2008-2014 MagentoThemes.net. All Rights Reserved.
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

    public function catalogCategoryPrepareSave($observer){
        $category = $observer->getEvent()->getCategory();
        /* @var $category Mage_Catalog_Model_Category */
        $request = $observer->getEvent()->getRequest();
        /* @var $request Mage_Core_Controller_Request_Http */

        $images = $request->getParam('slider_images');
        $object = array();

        if (is_array($images)){
            foreach ($images as $image){
                $object[] = array(
                    'uri' => $image
                );
            }
        }

        $category->setData('slider_params', Mage::helper('core')->jsonEncode($object));
    }
}