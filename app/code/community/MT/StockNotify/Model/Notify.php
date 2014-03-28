<?php
/**
 * @category    MT
 * @package     MT_StockNotify
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_StockNotify_Model_Notify extends Mage_Core_Model_Abstract{
    const STATUS_PENDING    = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_DONE       = 3;

    protected function _construct(){
        parent::_construct();
        $this->_init('mtstocknotify/notify');
    }

    public function getStatuses(){
        return array(
            self::STATUS_PENDING    => Mage::helper('mtstocknotify')->__('Pending'),
            self::STATUS_PROCESSING => Mage::helper('mtstocknotify')->__('Processing'),
            self::STATUS_DONE       => Mage::helper('mtstocknotify')->__('Done')
        );
    }
}