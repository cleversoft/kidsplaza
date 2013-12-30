<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_Block_Adminhtml_Customer_Edit_Tab_Point_Manage extends Mage_Adminhtml_Block_Widget_Form{
    protected function _prepareForm(){
        $form = new Varien_Data_Form();
        $form->setFieldNameSuffix('point');
        $form->setHtmlIdPrefix('point_');

        $fieldset = $form->addFieldset('mtpoint_manage_section', array(
            'legend' => Mage::helper('mtpoint')->__('Update Point Balance')
        ));
        $fieldset->addField('delta', 'text', array(
            'name' => 'delta',
            'label' => Mage::helper('mtpoint')->__('Amount'),
            'title' => Mage::helper('mtpoint')->__('Amount'),
            'class' => 'validate-number',
            'note' => Mage::helper('mtpoint')->__('Enter a negative number to subtract from balance')
        ));
        $fieldset->addField('comment', 'text', array(
            'name' => 'comment',
            'label' => Mage::helper('mtpoint')->__('Comment'),
            'title' => Mage::helper('mtpoint')->__('Comment'),
            'note' => Mage::helper('mtpoint')->__('Enter the reason here')
        ));

        $this->setForm($form);
        return parent::_prepareForm();
    }
}