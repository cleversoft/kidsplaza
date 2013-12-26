<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_Block_Adminhtml_Rate extends Mage_Adminhtml_Block_Widget_Grid_Container{
    public function __construct(){
        $this->_headerText = Mage::helper('mtpoint')->__('MT Point Rate');
        $this->_blockGroup = 'mtpoint';
        $this->_controller = 'adminhtml_rate';
        parent::__construct();
        $this->_updateButton('add', 'label', Mage::helper('mtpoint')->__('Add New Rate'));
    }
}