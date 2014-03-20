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

class MT_Review_Block_Adminhtml_Comments_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'mtreview';
        $this->_controller = 'adminhtml_comments';
        $this->_updateButton('save', 'label', Mage::helper('review')->__('Save Answer'));
        $this->_updateButton('save', 'id', 'save_button');
    }

    public function getHeaderText()
    {
        if( Mage::registry('review_data') && Mage::registry('review_data')->getId() ) {
            return Mage::helper('review')->__("Edit comment '%s'", $this->escapeHtml(Mage::registry('review_data')->getTitle()));
        } else {
            return Mage::helper('review')->__('New Review');
        }
    }
}
