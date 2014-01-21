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
Class MT_ProductQuestions_Block_Adminhtml_Questions_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'question_id';
        $this->_blockGroup = 'productquestions';
        $this->_controller = 'adminhtml_questions';

        parent::__construct();
        $this->_updateButton('save', 'label', Mage::helper('megamenu')->__('Save Question'));
        $this->_updateButton('delete', 'label', Mage::helper('megamenu')->__('Delete Question'));

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
        if (Mage::registry('productquestions_questions')->getId()) {
            return Mage::helper('megamenu')->__("Edit/Reply question '%s'", $this->escapeHtml(Mage::registry('productquestions_questions')->getQuestionProductName()));
        }
        else {
            return Mage::helper('megamenu')->__('Edit/Reply question');
        }
    }

}