<?php
/**
 * @category    MT
 * @package     MT_CatalogInventory
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_CatalogInventory_Model_Resource_Indexer_Stock_Configurable
    extends MT_CatalogInventory_Model_Resource_Indexer_Stock_Default{

    public function reindexEntity($entityIds){
        $connection = $this->_getWriteAdapter();
        $cssTable   = $this->getTable('cataloginventory/stock_status');
        $csiTable   = $this->getTable('cataloginventory/stock_item');
        $cpslTable  = $this->getTable('catalog/product_super_link');
        $stockId    = 1;

        foreach ($entityIds as $parentId){
            foreach (Mage::app()->getWebsites() as $website){
                $websiteId = $website->getId();

                $parentQtySql = "
                    SELECT SUM(css.qty) FROM {$cssTable} AS css
                    INNER JOIN {$cpslTable} AS cpsl ON cpsl.product_id = css.product_id
                    WHERE css.website_id = ? AND cpsl.parent_id = ?
                ";
                $parentQty = $connection->fetchOne($parentQtySql, array($websiteId, $parentId));

                $checkRecordSql = "
                    SELECT COUNT(*) FROM {$cssTable}
                    WHERE product_id = ? AND website_id = ? AND stock_id = ?
                ";
                $isRecordExist = $connection->fetchOne($checkRecordSql, array($parentId, $websiteId, $stockId));

                if ($isRecordExist){
                    $updateSql = "
                        UPDATE {$cssTable} SET stock_status = ?
                        WHERE product_id = ? AND stock_id = ? AND website_id = ?
                    ";
                    $connection->query($updateSql, array($parentQty > 0 ? 1 : 0, $parentId, $stockId, $websiteId));
                }else{
                    $insertSql = "
                        INSERT INTO {$cssTable}
                        (product_id,website_id,stock_id,qty,stock_status) VALUES (?,?,?,?,?)
                    ";
                    $connection->query($insertSql, array($parentId, $websiteId, $stockId, 0, $parentQty > 0 ? 1 : 0));
                }

                $updateStockItemSql = "
                    UPDATE {$csiTable} SET is_in_stock = ?
                    WHERE product_id = ?
                ";
                $connection->query($updateStockItemSql, array($parentQty > 0 ? 1 : 0, $parentId));
            }
        }

        return $this;
    }
}