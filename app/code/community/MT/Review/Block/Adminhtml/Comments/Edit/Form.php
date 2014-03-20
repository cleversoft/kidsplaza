<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_Review
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */

class MT_Review_Block_Adminhtml_Comments_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $comment = Mage::registry('review_data');

        $form = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'), 'ret' => Mage::registry('ret'))),
            'method'    => 'post'
        ));

        $fieldset = $form->addFieldset('review_details', array('legend' => Mage::helper('review')->__('Review Details'), 'class' => 'fieldset-wide'));

        $fieldset->addField('review_id', 'hidden', array(
            'name'  => 'review_id',
            'value' => $comment->getReviewId()
        ));
        $fieldset->addField('store_id', 'hidden', array(
            'name'  => 'store_id',
        ));
        $fieldset->addField('title', 'note', array(
            'label'     => Mage::helper('review')->__('Review title'),
            'text'      => '<a href="' . $this->getUrl('*/adminhtml_review/edit', array('id' => $comment->getReviewId())) . '" onclick="this.target=\'blank\'">'.$comment->getTitle().'</a>'
        ));
        $fieldset->addField('detail', 'note', array(
            'label'     => Mage::helper('review')->__('Review detail'),
            'text'      => $comment->getDetail()
        ));

        $fieldset->addField('customer', 'note', array(
            'label'     => Mage::helper('review')->__('Posted By'),
            'text'      => $comment->getCustomerName(),
        ));

        $fieldset->addField('summary_rating', 'note', array(
            'label'     => Mage::helper('review')->__('Summary Rating'),
            'text'      => $this->getLayout()->createBlock('adminhtml/review_rating_summary')->toHtml(),
        ));
        $fieldset->addField('created_datetime', 'hidden', array(
            'name'  => 'created_datetime',
        ));
        $fieldset->addField('created_at', 'date', array(
            'name'      => 'created_at',
            'label'     => $this->__('Answer on'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
        ));
        $fieldset->addField('customer_name', 'hidden', array(
            'name'     => 'customer_name',
        ));
        $fieldset->addField('customer_id', 'hidden', array(
            'name'     => 'customer_id',
        ));
        $fieldset->addField('author_type', 'hidden', array(
            'name'     => 'author_type',
        ));

        $fieldset->addField('comments', 'textarea', array(
            'label'     => Mage::helper('review')->__('Answer'),
            'required'  => true,
            'name'      => 'comments',
            'style'     => 'height:24em;',
        ));

        $form->setUseContainer(true);
        $form->setValues($comment->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
