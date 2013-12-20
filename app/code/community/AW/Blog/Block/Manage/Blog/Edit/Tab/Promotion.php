<?php
/**
 * @category    MT
 * @package     KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */

class AW_Blog_Block_Manage_Blog_Edit_Tab_Promotion extends Mage_Adminhtml_Block_Widget_Form{
    protected function _prepareForm(){
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('promotion_form', array('legend' => Mage::helper('blog')->__('General Infomation')));

        $fieldset->addField('promotion_start', 'date', array(
            'name'      => 'promotion_start',
            'label'     => Mage::helper('blog')->__('From Date'),
            'title'     => Mage::helper('blog')->__('From Date'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => Mage::app()->getLocale()->getDateFormat(
                Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
            )
        ));

        $fieldset->addField('promotion_end', 'date', array(
            'name'      => 'promotion_end',
            'label'     => Mage::helper('blog')->__('Until Date'),
            'title'     => Mage::helper('blog')->__('Until Date'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => Mage::app()->getLocale()->getDateFormat(
                Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
            )
        ));

        if (Mage::getSingleton('adminhtml/session')->getBlogData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getBlogData());
            Mage::getSingleton('adminhtml/session')->setBlogData(null);
        } elseif ($data = Mage::registry('blog_data')) {
            $form->setValues(Mage::registry('blog_data')->getData());
            if ($data->getData('promotion_start')) {
                $form->getElement('promotion_start')->setValue(
                    Mage::app()->getLocale()->date(
                        $data->getData('promotion_start'), Varien_Date::DATE_INTERNAL_FORMAT
                    )
                );
            }
            if ($data->getData('promotion_end')) {
                $form->getElement('promotion_end')->setValue(
                    Mage::app()->getLocale()->date(
                        $data->getData('promotion_end'), Varien_Date::DATE_INTERNAL_FORMAT
                    )
                );
            }
        }

        return parent::_prepareForm();
    }
}