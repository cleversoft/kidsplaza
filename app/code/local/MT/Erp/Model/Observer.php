<?php
/**
 * @category    MT
 * @package     MT_Erp
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Erp_Model_Observer{
    protected $_MSSQLConnection;
    protected $_attributes;
    protected $_entityTypes;
    protected $_promotions;
    protected $_websites;
    protected $_stores;

    protected function _getMSSQLConnection(){
        if ($this->_MSSQLConnection) return $this->_MSSQLConnection;

        if (!function_exists('sqlsrv_connect')){
            $this->log('ERROR: Microsoft SQL Server Driver not found');
            return null;
        }

        $mssql_host = Mage::getStoreConfig('kidsplaza/erp/host');
        $mssql_user = Mage::getStoreConfig('kidsplaza/erp/user');
        $mssql_pass = Mage::getStoreConfig('kidsplaza/erp/pass');
        $mssql_db   = Mage::getStoreConfig('kidsplaza/erp/db');

        $mssql_info = array('UID' => $mssql_user, 'PWD' => $mssql_pass, 'Database' => $mssql_db);
        $mssql_conn = sqlsrv_connect($mssql_host, $mssql_info);

        if (!$mssql_conn){
            $this->log('ERROR: Microsoft SQL Server connection failed');
            return null;
        }else{
            $this->log('INFO: Microsoft SQL Server connected');
        }

        $this->_MSSQLConnection = $mssql_conn;

        return $this->_MSSQLConnection;
    }

    protected function _MSSQLQuery($query){
        $mssql_conn = $this->_getMSSQLConnection();
        if (!$mssql_conn) return null;

        $rs = sqlsrv_query($mssql_conn, $query);
        if (!$rs) {
            $this->log("ERROR: Microsoft SQL Server query:\n{$query}");
            return null;
        }

        return $rs;
    }

    protected function log($message){
        Mage::log($message);
    }

    protected function _getConnection($type = 'core_read'){
        return Mage::getSingleton('core/resource')->getConnection($type);
    }

    protected function _getTableName($tableName){
        return Mage::getSingleton('core/resource')->getTableName($tableName);
    }

    protected function _getAttributeId($attribute_code){
        if (!$attribute_code) return null;

        if (!is_array($this->_attributes)) {
            $connection = $this->_getConnection('core_read');
            $sql = "
                SELECT attribute_id
                FROM " . $this->_getTableName('eav_attribute') . "
                WHERE entity_type_id = ? AND attribute_code = ?";

            $entity_type_id = $this->_getEntityTypeId('catalog_product');
            $this->_attributes[$attribute_code] = $connection->fetchOne($sql, array($entity_type_id, $attribute_code));

            return $this->_attributes[$attribute_code];
        }else{
            if (isset($this->_attributes[$attribute_code])) {
                return $this->_attributes[$attribute_code];
            }
        }
    }

    protected function _getEntityTypeId($entity_type_code='catalog_product'){
        if (!$entity_type_code) return null;

        if (!is_array($this->_entityTypes)) {
            $connection = $this->_getConnection('core_read');
            $sql = "SELECT entity_type_id FROM " . $this->_getTableName('eav_entity_type') . " WHERE entity_type_code = ?";
            $this->_entityTypes[$entity_type_code] = $connection->fetchOne($sql, array($entity_type_code));

            return $this->_entityTypes[$entity_type_code];
        }else{
            if (isset($entityTypes[$entity_type_code])){
                return $entityTypes[$entity_type_code];
            }
        }
    }

    /**
     * Get all products in promotions from ERP database
     */
    protected function _getErpProductPromotion(){
        if (!is_array($this->_promotions)){
            $sql = "
                SELECT km.[MHID], mh.[MHCODE], km.[GIABANLE], km.[GIAKM]
                FROM [dbo].[KHUYENMAICT] AS km
                LEFT JOIN [dbo].[MATHANG] AS mh ON km.[MHID] = mh.[MHID]
                WHERE km.[KMID] IN (
                    SELECT [KMID]
                    FROM [dbo].[KHUYENMAI]
                    WHERE (GETDATE() BETWEEN [TUNGAY] AND [DENNGAY]) AND [APPLYTOALLKH] = 1
                )";

            $rs = $this->_MSSQLQuery($sql);
            if (!$rs) return null;
            while ($row = sqlsrv_fetch_object($rs)) {
                $this->_promotions[$row->MHCODE] = $row;
            }
        }

        return $this->_promotions;
    }

    /**
     * Get all websites in Magento
     */
    protected function _getWebsites(){
        if (!is_array($this->_websites)){
            foreach (Mage::app()->getWebsites() as $website){
                $this->_websites[] = $website->getId();
            }
        }

        return $this->_websites;
    }

    protected function _getErpStoresByWebsite($website){
        if (!$website) return array();

        if (isset($this->_stores[$website])) return $this->_stores[$website];

        $storesInWebsite = Mage::app()->getWebsite($website)->getStores();
        $storeInWebsite = array_pop($storesInWebsite);

        $erpStores = explode("\n", Mage::getStoreConfig('kidsplaza/erp/stores', $storeInWebsite));

        if (count($erpStores)){
            array_walk($erpStores, function(&$v){ $v = "'".$v."'"; });
            $this->_stores[$website] = $erpStores;
            return $this->_stores[$website];
        }

        return array();
    }

    /**
     * Get total qty from ERP database by stores
     */
    protected function _getErpQtyByStores($erp, $website){
        if (!$erp->MHID || !$website) return null;

        $stores = $this->_getErpStoresByWebsite($website);

        if (!count($stores)) return null;

        $sql = "
            SELECT SUM([SOLUONG]) AS QTY
            FROM [dbo].[KHOMATHANG]
            WHERE [KHOID] IN (" . implode(',', $stores) . ")
                AND [MHID] = '" . $erp->MHID ."'";

        $rs = $this->_MSSQLQuery($sql);
        if (!$rs) return null;
        $row = sqlsrv_fetch_object($rs);

        return $row->QTY > 0 ? (int)$row->QTY : 0;
    }

    protected function _updatePrices($product, $erp){
        if (!$product['sku'] || !$erp->MHCODE) return false;

        $connection = $this->_getConnection('core_write');
        $priceAttributeId = $this->_getAttributeId('price');
        $specialPriceAttributeId = $this->_getAttributeId('special_price');
        $promotions = $this->_getErpProductPromotion();

        $sql = "
            UPDATE " . $this->_getTableName('catalog_product_entity_decimal') . " cped
            SET cped.value = ?
            WHERE cped.attribute_id = ? AND cped.entity_id = ?";

        $price = $erp->GIABANLE ? $erp->GIABANLE : 0;
        if (isset($promotions[$erp->MHCODE])){
            $promotion = $promotions[$erp->MHCODE];
            $specialPrice = $promotion->GIAKM;
        }else{
            $specialPrice = null;
        }

        $connection->query($sql, array($price, $priceAttributeId, $product['entity_id']));
        $connection->query($sql, array($specialPrice, $specialPriceAttributeId, $product['entity_id']));

        $this->log(sprintf('INFO: PRICE SKU [%s], price=%s, special_price=%s', $erp->MHCODE, $price, $specialPrice ? $specialPrice : 'NULL'));

        return true;
    }

    protected function _updateStocks($product, $erp){
        if (!$product['sku'] || !$erp->MHCODE) return false;

        $connection = $this->_getConnection('core_write');
        $websites = $this->_getWebsites();
        $logs = array();

        foreach ($websites as $website) {
            $newQty = $this->_getErpQtyByStores($erp, $website);
            if (is_null($newQty)) continue;

            $isInStock = $newQty > 0 ? 1 : 0;
            $stockStatus = $isInStock;

            $sql = "
                UPDATE " . $this->_getTableName('cataloginventory_stock_item') . " cisi," . $this->_getTableName('cataloginventory_stock_status') . " ciss
                SET cisi.qty = ?, cisi.is_in_stock = ?, ciss.qty = ?, ciss.stock_status = ?
                WHERE cisi.product_id = ? AND ciss.website_id = ? AND cisi.product_id = ciss.product_id";

            $connection->query($sql, array($newQty, $isInStock, $newQty, $stockStatus, $product['entity_id'], $website));

            $logs[] = sprintf("%s=%s", $website, $newQty);
        }

        $this->log(sprintf('INFO: STOCK SKU [%s], %s', $erp->MHCODE, implode(', ', $logs)));

        return true;
    }

    protected function _getProductCollection(){
        $connection = $this->_getConnection('core_read');
        $query = sprintf("SELECT * FROM %s", $this->_getTableName('catalog_product_entity'));
        return $connection->fetchAll($query);
    }

    protected function _getErpProductBySku($sku){
        if (!$sku) return false;
        $query = sprintf("SELECT * FROM [dbo].[MATHANG] WHERE [MHCODE] = '%s'", $sku);
        $rs = $this->_MSSQLQuery($query);
        if (!$rs) return null;
        return sqlsrv_fetch_object($rs);
    }

    public function run(){
        if (!$this->_getMSSQLConnection()) return;

        $products = $this->_getProductCollection();
        if (!count($products)){
            $this->log('ERROR: No product avaiable');
            return;
        }else $this->log(sprintf('INFO: Total products: %d', count($products)));

        $i = 0;
        $countProcessed = 0;
        foreach ($products as $product){
            if ($i++ == 100) break;
            $row = $this->_getErpProductBySku($product['sku']);
            if (!$row) continue;
            $this->_updatePrices($product, $row);
            $this->_updateStocks($product, $row);
            $countProcessed++;
        }

        $this->log(sprintf('INFO: Processed products: %d', $countProcessed));
        return;
    }
}