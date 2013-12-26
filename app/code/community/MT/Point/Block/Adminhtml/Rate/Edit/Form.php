<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_Block_Adminhtml_Rate_Edit_Form extends Mage_Adminhtml_Block_Widget_Form{
    protected function _prepareForm(){
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save'),
            'method' => 'post'
        ));
        $form->setUseContainer(true);

        $fieldset = $form->addFieldset('main_fieldset', array(
            'legend' => Mage::helper('mtpoint')->__('Rate Information')
        ));
        $fieldset->addField('amount', 'text', array(
            'name'      => 'amount',
            'required'  => true,
            'label'     => Mage::helper('mtpoint')->__('Amount'),
            'title'     => Mage::helper('mtpoint')->__('Amount'),
            'class'     => 'validate-number'
        ));
        $fieldset->addField('point', 'text', array(
            'name'      => 'point',
            'required'  => true,
            'label'     => Mage::helper('mtpoint')->__('Point'),
            'title'     => Mage::helper('mtpoint')->__('Point'),
            'class'     => 'validate-number'
        ));

        $rate = Mage::registry('mtpoint_rate');
        $form->setValues($rate->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}