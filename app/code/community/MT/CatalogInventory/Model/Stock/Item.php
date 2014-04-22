<?php
/**
 * @category    MT
 * @package     MT_CatalogInventory
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_CatalogInventory_Model_Stock_Item extends Mage_CatalogInventory_Model_Stock_Item{
    public function getQty(){
        if (!$this->getProduct()) return $this->getData('qty');

        $productId = $this->getProduct()->getId();
        $stockStatus = Mage::getSingleton('cataloginventory/stock_status');
        $productsData = $stockStatus->getProductData($productId, Mage::app()->getStore()->getWebsiteId());

        return isset($productsData[$productId]) ? $productsData[$productId]['qty'] : $this->getData('qty');
    }
}