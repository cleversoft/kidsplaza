<?php
/**
 * @category    MT
 * @package     MT_CatalogInventory
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_CatalogInventory_Model_Stock_Status extends Mage_CatalogInventory_Model_Stock_Status {
    /**
     * Add information about stock status to product collection
     *
     * @param   Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $productCollection
     * @param   int|null $websiteId
     * @param   int|null $stockId
     * @return  Mage_CatalogInventory_Model_Stock_Status
     */
    public function addStockStatusToProducts($productCollection, $websiteId = null, $stockId = null) {
        if ($stockId === null) {
            $stockId = Mage_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID;
        }
        if ($websiteId === null) {
            $websiteId = Mage::app()->getStore()->getWebsiteId();
            if ((int)$websiteId == 0 && $productCollection->getStoreId()) {
                $websiteId = Mage::app()->getStore($productCollection->getStoreId())->getWebsiteId();
            }
        }
        $productIds = array();
        foreach ($productCollection as $product) {
            $productIds[] = $product->getId();
        }

        if (!empty($productIds)) {
            $stockStatuses = $this->_getResource()->getProductStatus($productIds, $websiteId, $stockId);
            if (!count($stockStatuses)){
                foreach ($productCollection as $product) {
                    $product->setIsSalable(false);
                }
            }else{
                $productStatus = array();
                foreach ($stockStatuses as $productId => $status) {
                    if ($product = $productCollection->getItemById($productId)) {
                        $product->setIsSalable($status);
                        $productStatus[] = $productId;
                    }
                }
                foreach ($productCollection as $product) {
                    if (!isset($productStatus[$product->getId()])){
                        $product->setIsSalable(false);
                    }
                }
            }
        }

        /* back compatible stock item */
        foreach ($productCollection as $product) {
            $object = new Varien_Object(array('is_in_stock' => $product->getData('is_salable')));
            $product->setStockItem($object);
        }

        return $this;
    }
}