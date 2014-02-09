<?php
/**
 * @category    MT
 * @package     MT_CatalogSlider
 * @copyright   Copyright (C) 2008-2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_CatalogSlider_Block_Adminhtml_Catalog_Category_Tab_Slider extends Mage_Adminhtml_Block_Catalog_Form{
    protected function _prepareLayout(){
        parent::_prepareLayout();
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('slider_fieldset', array(
            'legend' => Mage::helper('mtcatalogslider')->__('Slider Information')
        ));
        $fieldset->addField('slider_images', 'text', array(
            'label' => Mage::helper('mtcatalogslider')->__('Images')
        ));
        $form->getElement('slider_images')->setRenderer(
            $this->getLayout()->createBlock('mtcatalogslider/adminhtml_widget_form_element_images')
                ->setData('category', Mage::registry('current_category'))
        );

        $this->setForm($form);
    }
}