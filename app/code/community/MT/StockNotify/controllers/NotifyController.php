<?php
/**
 * @category    MT
 * @package     MT_StockNotify
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_StockNotify_NotifyController extends Mage_Core_Controller_Front_Action{
    public function submitAction(){
        if ($this->_validateFormKey()){
            $productId = $this->getRequest()->getParam('product');
            if (!$productId) return;
            $product = Mage::getModel('catalog/product')->load($productId, array('entity_id'));
            if (!$product->getId()) return;
            $notify = Mage::getModel('mtstocknotify/notify');
            //
        }
    }
}