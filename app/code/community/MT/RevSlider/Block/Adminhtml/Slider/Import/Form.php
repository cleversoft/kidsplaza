<?php
/**
 * @category    MT
 * @package     MT_RevSlider
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_RevSlider_Block_Adminhtml_Slider_Import_Form extends Mage_Adminhtml_Block_Widget_Form{
    protected function _prepareForm(){
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/importPost'),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $fieldset = $form->addFieldset('info_fieldset', array('legend' => Mage::helper('revslider')->__('Import Settings')));
        $fieldset->addField('file', 'file', array(
            'name'  => 'file',
            'label' => Mage::helper('revslider')->__('File Data')
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}