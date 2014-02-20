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
Class MT_ProductQuestions_Block_Adminhtml_Categories_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'cat_id';
        $this->_blockGroup = 'productquestions';
        $this->_controller = 'adminhtml_categories';

        parent::__construct();
        $this->_updateButton('save', 'label', Mage::helper('productquestions')->__('Save Categories'));
        $this->_updateButton('delete', 'label', Mage::helper('productquestions')->__('Delete Categories'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('block_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'block_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'block_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('productquestions_categories')->getId()) {
            return Mage::helper('productquestions')->__("Edit '%s'", $this->escapeHtml(Mage::registry('productquestions_categories')->getQuestionProductName()));
        }
        else {
            return Mage::helper('productquestions')->__('Edit');
        }
    }

}