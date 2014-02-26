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
Class MT_ProductQuestions_Block_Adminhtml_Answers_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $data = Mage::registry('productquestions_answers');
        $data['question_datetime'] = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));

        $productId = isset($data['question_product_id']) ? $data['question_product_id'] : 0;
        $productName = isset($data['question_product_name']) ? $data['question_product_name'] : 'unknown';
        $questionId = isset($data['parent_question_id']) ? $data['parent_question_id'] : 0;
        $storeId = isset($data['question_store_id']) ? $data['question_store_id'] : Mage::app()->getStore()->getId();
        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'), 'ret' => Mage::registry('ret'))),
            'method'    => 'post'
        ));
        $fieldset = $form->addFieldset('productquestions_form', array('legend' => $this->__('Answer details')));

        $fieldset->addField('parent_question_id', 'hidden', array(
            'name'      => 'parent_question_id',
        ));
        $fieldset->addField('category_id', 'hidden', array(
            'name'      => 'category_id',
        ));
        if($productId){
            $fieldset->addField('question_product_link', 'note', array(
                'label'     => $this->__('Product'),
                'text'      => '<a href="#" onclick="window.open(\''.Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/catalog_product/edit', array('id' => $productId)).'\', \''.$productName.'\', \'\').focus()" title="'.$this->__('Open product page in new window').'">'.$productName.'</a>',
            ));
            $fieldset->addField('question_product_name', 'hidden', array(
                'name'     => 'question_product_name',
            ));
            $fieldset->addField('question_product_id', 'hidden', array(
                'name'     => 'question_product_id',
            ));
        }
        if($questionId){
            $model = Mage::getModel('productquestions/productquestions')->load($questionId);
            $fieldset->addField('question_link', 'note', array(
                'label'     => $this->__('Question'),
                'text'      => '<a href="#" onclick="window.open(\''.Mage::getSingleton('adminhtml/url')->getUrl('productquestions/adminhtml_questions/edit', array('id' => $model->getQuestionId())).'\', \''.$model->getQuestionText().'\', \'\').focus()" title="'.$this->__('Open product page in new window').'">'.$model->getQuestionText().'</a>',
            ));
        }
        $fieldset->addField('question_store_id', 'hidden', array(
            'name'      => 'question_store_id',
        ));
        $fieldset->addField('asked_from', 'note', array(
            'label'     => $this->__('Asked from'),
            'text'     => Mage::getSingleton('adminhtml/system_store')->getStoreNameWithWebsite($storeId),
        ));
        $fieldset->addField('question_datetime', 'hidden', array(
            'name'  => 'question_datetime',
        ));
        $fieldset->addField('question_date', 'date', array(
            'name'      => 'question_date',
            'label'     => $this->__('Answer on'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
        ));
        if(Mage::app()->isSingleStoreMode())
            $fieldset->addField('question_store_ids', 'hidden', array(
                'name'      => 'question_store_ids[]',
                'value'     => Mage::app()->getStore()->getId(),
            ));
        else
            $fieldset->addField('question_store_ids', 'multiselect', array(
                'name'      => 'question_store_ids[]',
                'label'     => $this->__('Show in stores'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));


        $fieldset->addField('question_author_id', 'hidden', array(
            'name'     => 'question_author_id',
        ));
        $fieldset->addField('question_author_type', 'hidden', array(
            'name'     => 'question_author_type',
        ));
        $fieldset->addField('question_author_name', 'text', array(
            'name'      => 'question_author_name',
            'label'     => $this->__('Author name'),
            'required'  => true,
            'style'     => 'width:700px;',
        ));

        $fieldset->addField('question_author_email', 'text', array(
            'name'      => 'question_author_email',
            'label'     => $this->__('Author email'),
            'required'  => true,
            'class'     => 'validate-email',
            'style'     => 'width:700px;',
        ));

        $fieldset->addField('question_text', 'editor', array(
            'name'      => 'question_text',
            'label'     => $this->__('Answer'),
            'required'  => true,
            'style'     => 'width:700px; height:200px;',
        ));
        $fieldset->addField('question_status', 'select', array(
            'name'      => 'question_status',
            'label'     => $this->__('Visibility'),
            'values'    => MT_ProductQuestions_Model_Source_Question_Status::toOptionArray(),
        ));
        $form->setUseContainer(true);
        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}