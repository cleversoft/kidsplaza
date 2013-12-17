<?php
/**
 * @category    MT
 * @package     MT_RevSlider
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_RevSlider_Block_Adminhtml_Slider extends Mage_Adminhtml_Block_Widget_Grid_Container{
    public function __construct(){
        $this->_headerText = Mage::helper('revslider')->__('MT Revolution Slider');
        $this->_blockGroup = 'revslider';
        $this->_controller = 'adminhtml_slider';
        parent::__construct();
        if ($this->_isAllowedAction('add')){
            $this->_updateButton('add', 'label', Mage::helper('revslider')->__('Add New Slider'));
        }else{
            $this->_removeButton('add');
        }
    }

    protected function _isAllowedAction($action){
        //return Mage::getSingleton('admin/session')->isAllowed('revslider/slider/' . $action);
        return true;
    }
}