<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Block_Adminhtml_Catalog_Product_Edit_Tab_Video extends Mage_Adminhtml_Block_Widget_Form{
    protected function _prepareForm(){
        $product = Mage::registry('product');
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('video_fieldset', array('legend' => Mage::helper('kidsplaza')->__('Video Settings')));
        $fieldset->addField('video', 'text', array(
            'label' => Mage::helper('kidsplaza')->__('Videos'),
            'note' => Mage::helper('kidsplaza')->__('Ex: https://www.youtube.com/watch?v=vYxNMsqtPeA'),
        ));
        $form->getElement('video')->setRenderer(
            $this->getLayout()->createBlock('kidsplaza/adminhtml_widget_form_element_video')
        );
        $form->setValues($product->getData());
        $this->setForm($form);
    }
}