<?php
/**
 * @category    MT
 * @package     MT_Attribute
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Attribute_Block_Adminhtml_Catalog_Product_Attribute_Options_Edit_Form extends Mage_Adminhtml_Block_Widget_Form{
    protected function _prepareForm(){
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save'),
            'method' => 'post'
        ));
        $form->setUseContainer(true);

        $optionModel = Mage::registry('attribute_option');

        if ($optionModel->getId()) {
            $form->addField('option_id', 'hidden', array(
                'name' => 'option_id',
                'value' => $optionModel->getId()
            ));
        }

        $fieldset = $form->addFieldset('fieldset', array('legend' => Mage::helper('mtattribute')->__('Attribute Option Settings')));
        $fieldset->addField('description', 'editor', array(
            'name'      => 'description',
            'style'     => 'width:600px;height:400px;',
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'label'     => Mage::helper('mtattribute')->__('Description')
        ));

        if ($optionModel->getId()) {
            $form->setValues($optionModel->getData());
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }

    protected function _prepareLayout(){
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        return $this;
    }
}