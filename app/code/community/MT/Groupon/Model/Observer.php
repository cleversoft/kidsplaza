<?php
/**
 * @category    MT
 * @package     MT_Groupon
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Groupon_Model_Observer{
    public function catalogProductPrepareSave($observer){
        $product = $observer->getEvent()->getProduct();

        $from = $product->getData('groupon_from');
        if ($from){
            $product->setData('groupon_from', Mage::app()->getLocale()->date($from, null, null, false));
        }
        $to = $product->getData('groupon_to');
        if ($to){
            $product->setData('groupon_to', Mage::app()->getLocale()->date($to, null, null, false));
        }
    }
}