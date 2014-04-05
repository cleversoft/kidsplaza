<?php
/**
 * @category    MT
 * @package     MT_Location
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Location_Block_Adminhtml_Location_Edit_Form extends Mage_Adminhtml_Block_Widget_Form{
    protected function _prepareForm(){
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save'),
            'method' => 'post'
        ));
        $form->setUseContainer(true);

        $rate = Mage::registry('mtlocation');
        if ($rate){
            $form->addField('id', 'hidden', array(
                'name' => 'id'
            ));
        }

        $fieldset = $form->addFieldset('main_fieldset', array(
            'legend' => Mage::helper('mtlocation')->__('Location Information')
        ));
        $fieldset->addField('address', 'text', array(
            'name'      => 'address',
            'required'  => true,
            'label'     => Mage::helper('mtlocation')->__('Address'),
            'title'     => Mage::helper('mtlocation')->__('Address'),
            'class'     => 'required-entry'
        ));
        $fieldset->addField('position', 'text', array(
            'name'      => 'position',
            'required'  => true,
            'label'     => Mage::helper('mtlocation')->__('Latiude,Longtitude'),
            'title'     => Mage::helper('mtlocation')->__('Latiude,Longtitude'),
            'note'      => Mage::helper('mtlocation')->__('Ex: 123.456,987.654')
        ));

        $form->setValues($rate->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}