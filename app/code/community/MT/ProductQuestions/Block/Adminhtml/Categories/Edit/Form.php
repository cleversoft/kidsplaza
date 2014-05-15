<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_ProductQuestions
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */
Class MT_ProductQuestions_Block_Adminhtml_Categories_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $data = Mage::registry('productquestions_categories');
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'), 'ret' => Mage::registry('ret'))),
            'method'    => 'post'
        ));
        $fieldset = $form->addFieldset('productquestions_form', array('legend' => $this->__('Category details')));

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => $this->__('Name'),
            'required'  => true,
            'style'     => 'width:700px;',
        ));

        $fieldset->addField('identifier', 'text', array(
            'name'      => 'identifier',
            'label'     => $this->__('URL Key'),
            'required'  => false,
            'style'     => 'width:700px;',
        ));

        if(Mage::app()->isSingleStoreMode())
            $fieldset->addField('stores', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore()->getId(),
            ));
        else
            $fieldset->addField('stores', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => $this->__('Show in stores'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));

        $fieldset->addField('status', 'select', array(
            'name'      => 'status',
            'label'     => $this->__('Visibility'),
            'values'    => MT_ProductQuestions_Model_Source_Question_Status::toOptionArray(),
        ));
        $form->setUseContainer(true);
        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}