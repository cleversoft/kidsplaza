<?php
/**
 * @category    MT
 * @package     MT_StockNotify
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_StockNotify_Block_Adminhtml_Notify extends Mage_Adminhtml_Block_Widget_Grid_Container{
    public function __construct(){
        $this->_headerText = Mage::helper('mtstocknotify')->__('Stock Notify');
        $this->_blockGroup = 'mtstocknotify';
        $this->_controller = 'adminhtml_notify';
        parent::__construct();
        $this->_removeButton('add');
    }
}