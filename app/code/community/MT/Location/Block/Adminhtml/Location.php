<?php
/**
 * @category    MT
 * @package     MT_Location
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Location_Block_Adminhtml_Location extends Mage_Adminhtml_Block_Widget_Grid_Container{
    public function __construct(){
        $this->_headerText = Mage::helper('mtlocation')->__('Location');
        $this->_blockGroup = 'mtlocation';
        $this->_controller = 'adminhtml_location';
        parent::__construct();
        $this->_updateButton('add', 'label', Mage::helper('mtpoint')->__('Add New Location'));
    }
}