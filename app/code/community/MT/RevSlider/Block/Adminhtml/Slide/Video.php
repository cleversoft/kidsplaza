<?php
/**
 * @category    MT
 * @package     MT_RevSlider
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_RevSlider_Block_Adminhtml_Slide_Video extends Mage_Adminhtml_Block_Widget_Form_Container{
    public function __construct(){
        $this->_blockGroup      = 'revslider';
        $this->_controller      = 'adminhtml_slide';
        $this->_mode            = 'video';
        parent::__construct();
        $this->setId('addVideoForm');
        $this->_headerText      = Mage::helper('revslider')->__('Add Video Form');
        $this->removeButton('back');
        $this->removeButton('reset');
        $this->_updateButton('save', 'label', Mage::helper('revslider')->__('Add'));
        $windowId = $this->getRequest()->getParam('windowId');
        $this->_updateButton('save', 'onclick', "revLayer.addLayerVideo('{$windowId}')");
        if ($serial = $this->getRequest()->getParam('serial')){
            $this->_formScripts[]   = "revLayer.assignVideoForm('{$serial}')";
        }else{
            $this->_formScripts[]   = 'revLayer.toggleVideoForm(false)';
        }
    }
}