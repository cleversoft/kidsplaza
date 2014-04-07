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
        $fieldset->addField('description', 'editor', array(
            'name'      => 'description',
            'label'     => Mage::helper('mtlocation')->__('Description'),
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
            'style'     => 'width:600px;height:400px;',
        ));

        $form->setValues($rate->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    protected function _prepareLayout(){
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()){
            $head = $this->getLayout()->getBlock('head');
            $head->setCanLoadTinyMce(true);
            $head->setCanLoadExtJs(true);
            $head->addJs('prototype/window.js')
                ->addJs('lib/flex.js')
                ->addJs('lib/FABridge.js')
                ->addJs('mage/adminhtml/flexuploader.js')
                ->addJs('mage/adminhtml/browser.js')
                ->addJs('mage/adminhtml/variables.js')
                ->addJs('mage/adminhtml/wysiwyg/widget.js')
                ->addItem('js_css', 'prototype/windows/themes/default.css')
                ->addItem('skin_css', 'lib/prototype/windows/themes/magento.css');
        }
        return $this;
    }
}