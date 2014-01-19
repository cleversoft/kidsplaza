<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_Helper_Data extends Mage_Core_Helper_Abstract{
    public function isActive(){
        return Mage::getStoreConfigFlag('mtpoint/general/enable')
            && Mage::getStoreConfigFlag('mtpoint/general/purchase');
    }

    public function getPointRate(){
        $collection = Mage::getModel('mtpoint/rate')->getCollection();
        if (!$collection->getSize()) return;
        $rate = $collection->getFirstItem();
        return $rate->getAmount() / $rate->getPoint();
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     * @return float
     */
    public function getPointInCart($quote=null){
        $rate = $this->getPointRate();
        if (!$rate) return;
        if (!($quote instanceof Mage_Sales_Model_Quote)){
            $quote = Mage::getSingleton('checkout/session')->getQuote();
        }
        return floor($quote->getSubtotal() / $rate);
    }
}