<?php
/**
 * @category    MT
 * @package     MT_StockNotify
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_StockNotify_Model_Mysql4_Notify extends Mage_Core_Model_Mysql4_Abstract{
    protected function _construct(){
        $this->_init('mtstocknotify/notify', 'id');
    }
}