<?php
/**
 * @category    MT
 * @package     MT_Location
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Location_Block_Adminhtml_Location_Edit extends Mage_Adminhtml_Block_Widget_Form_Container{
    public function __construct(){
        $this->_blockGroup  = 'mtlocation';
        $this->_controller  = 'adminhtml_location';
        $this->_form        = 'edit';
        parent::__construct();
    }

    public function getHeaderText(){
        $rate = Mage::registry('mtlocation');
        if ($rate->getId()) {
            return Mage::helper('mtlocation')->__('Edit Location');
        } else {
            return Mage::helper('mtlocation')->__('New Location');
        }
    }
}