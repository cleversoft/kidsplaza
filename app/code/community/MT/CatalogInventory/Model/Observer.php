<?php
class MT_CatalogInventory_Model_Observer{
    /**
     * Save product qty in website
     */
    public function saveAfterProduct($observer){
        $product = $observer->getEvent()->getProduct();
        if (!$product->getId() || !$product->getStockItem() || $product->getStore()->getId() == 0) return;

        if ($product->getTypeId() == 'simple'){
            $adapter = Mage::getSingleton('core/resource');
            $connection = $adapter->getConnection('core_write');
            $cssTable = $adapter->getTableName('cataloginventory_stock_status');
            $csiTable = $adapter->getTableName('cataloginventory_stock_item');
            $stockStatus = Mage::getSingleton('cataloginventory/stock_status');
            $stockData = $product->getStockItem()->getData();
            $productId = $product->getId();
            $websiteId = $product->getStore()->getWebsiteId();
            $stockId = 1;

            $stockStatus->saveProductStatus(
                $productId,
                $stockData['qty'] > 0 ? 1 : 0,
                $stockData['qty'],
                $stockId,
                $websiteId
            );

            //update qty and stock status in cataloginventory_stock_item table
            $qtySql = "
                SELECT SUM(qty) FROM {$cssTable}
                WHERE product_id = ? AND stock_id = ?
            ";
            $totalQty = $connection->fetchOne($qtySql, array($productId, $stockId));
            $updateSql = "
                UPDATE {$csiTable}
                SET qty = ?, is_in_stock = ? WHERE product_id = ?
            ";
            $connection->query($updateSql, array($totalQty, $totalQty > 0 ? 1 : 0, $productId));
        }
    }
}