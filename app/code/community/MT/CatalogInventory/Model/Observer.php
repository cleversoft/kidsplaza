<?php
class MT_CatalogInventory_Model_Observer{
    /**
     * Save product qty with website
     */
    public function saveAfterProduct($observer){
        $product = $observer->getEvent()->getProduct();
        $stockStatus = Mage::getSingleton('cataloginventory/stock_status');
        $stockData = $product->getStockItem()->getData();
        $stockStatus->saveProductStatus(
            $product->getId(),
            $stockData['qty'] > 0 ? 1 : 0,
            $stockData['qty'],
            1,
            $product->getStore()->getWebsiteId()
        );
    }
}