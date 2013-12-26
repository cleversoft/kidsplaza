<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_Block_Adminhtml_Rate_Edit extends Mage_Adminhtml_Block_Widget_Form_Container{
    public function __construct(){
        $this->_blockGroup  = 'mtpoint';
        $this->_controller  = 'adminhtml_rate';
        $this->_form        = 'edit';
        parent::__construct();
    }

    public function getHeaderText(){
        $rate = Mage::registry('mtpoint_rate');
        if ($rate->getId()) {
            return Mage::helper('mtpoint')->__('Edit Rate');
        } else {
            return Mage::helper('mtpoint')->__('New Rate');
        }
    }
}