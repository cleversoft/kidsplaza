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
    protected $_logger;
    protected $_logFile;

    protected function _getMSSQLConnection(){
        if ($this->_MSSQLConnection) return $this->_MSSQLConnection;

        if (!function_exists('sqlsrv_connect')){
            $this->log('Microsoft SQL Server Driver not found', Zend_Log::CRIT);
            return null;
        }

        $mssql_host = Mage::getStoreConfig('kidsplaza/erp/host');
        $mssql_user = Mage::getStoreConfig('kidsplaza/erp/user');
        $mssql_pass = Mage::getStoreConfig('kidsplaza/erp/pass');
        $mssql_db   = Mage::getStoreConfig('kidsplaza/erp/db');

        $mssql_info = array('UID' => $mssql_user, 'PWD' => $mssql_pass, 'Database' => $mssql_db);
        $mssql_conn = sqlsrv_connect($mssql_host, $mssql_info);

        if (!$mssql_conn){
            $this->log('Microsoft SQL Server connection failed', Zend_Log::CRIT);
            return null;
        }else{
            $this->log('Microsoft SQL Server connected');
        }

        $this->_MSSQLConnection = $mssql_conn;

        return $this->_MSSQLConnection;
    }

    protected function _MSSQLQuery($query, $params=array()){
        $mssql_conn = $this->_getMSSQLConnection();
        if (!$mssql_conn) return null;

        $rs = sqlsrv_query($mssql_conn, $query, $params);
        if (!$rs) {
            $this->log("Microsoft SQL Server query:\n{$query}", Zend_Log::CRIT);
            $this->log(sqlsrv_errors(), Zend_Log::CRIT);
            return null;
        }

        return $rs;
    }

    protected function log($message, $level=Zend_Log::INFO){
        if (!$this->_logger){
            $date       = Mage::getModel('core/date');
            $logDir     = Mage::getBaseDir('media') . DS . 'erp';
            $logFile    = uniqid($date->date('Y-m-d-H-i-')) . '.log';
            $this->_logFile = $logFile;

            if (!is_dir($logDir)){
                mkdir($logDir);
                chmod($logDir, 0777);
            }

            if (!file_exists($logDir . DS . $logFile)){
                file_put_contents($logDir . DS . $logFile, '');
                chmod($logDir . DS . $logFile, 0777);
            }

            $format = '%timestamp% %priorityName%: %message%' . PHP_EOL;
            $formatter = new Zend_Log_Formatter_Simple($format);

            $writer = new Zend_Log_Writer_Stream($logDir . DS . $logFile);
            $writer->setFormatter($formatter);

            $this->_logger = new Zend_Log($writer);
        }

        if (is_array($message) || is_object($message)){
            $message = print_r($message, true);
        }

        $this->_logger->log($message, $level);
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
            //enclose store id string
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

        $this->log(sprintf('PRICE SKU [%s], price=%s, special_price=%s', $erp->MHCODE, $price, $specialPrice ? $specialPrice : 'NULL'));

        return true;
    }

    protected function _updateStocks($product, $erp){
        if (!$product['sku'] || !$erp->MHCODE) return false;

        $connection = $this->_getConnection('core_write');
        $websites = $this->_getWebsites();
        $logs = array();
        $stockId = null;
        $totalQty = 0;

        $countSql = "
            SELECT COUNT(*)
            FROM ".$this->_getTableName('cataloginventory_stock_status')." ciss
            WHERE ciss.product_id = ? AND ciss.website_id = ?
        ";

        foreach ($websites as $website) {
            $newQty = $this->_getErpQtyByStores($erp, $website);
            if (is_null($newQty)) continue;
            $stockStatus = $newQty > 0 ? 1 : 0;
            $totalQty += $newQty;

            $isInTable = $connection->fetchOne($countSql, array($product['entity_id'], $website));

            if ($isInTable){
                $sql = "
                    UPDATE " . $this->_getTableName('cataloginventory_stock_status') . "
                    SET qty = ?, stock_status = ?
                    WHERE product_id = ? AND website_id = ?
                ";

                $connection->query($sql, array($newQty, $stockStatus, $product['entity_id'], $website));
                $logs[] = sprintf("%d=%d", $website, $newQty);
            }else{
                if (is_null($stockId)){
                    $stockSql = "SELECT stock_id FROM ".$this->_getTableName('cataloginventory_stock_item')." WHERE product_id=?";
                    $stockId = $connection->fetchOne($stockSql, array($product['entity_id']));
                    $stockId = $stockId  > 0 ? $stockId : 1;
                }

                $sql = "
                    INSERT INTO " . $this->_getTableName('cataloginventory_stock_status') . "
                    (product_id, website_id, stock_id, qty, stock_status) VALUES(?,?,?,?,?)
                ";

                $connection->query($sql, array($product['entity_id'], $website, $stockId, $newQty, $stockStatus));
                $logs[] = sprintf("%d=%d", $website, $newQty);
            }
        }

        $insertItemSql = "
            UPDATE " . $this->_getTableName('cataloginventory_stock_item') ."
            SET qty = ?, is_in_stock = ?
            WHERE product_id = ?
        ";

        $connection->query($insertItemSql, array($totalQty, $totalQty > 0 ? 1 : 0, $product['entity_id']));
        $logs[] = sprintf("all=%s", $totalQty);

        $this->log(sprintf('STOCK SKU [%s], %s', $erp->MHCODE, implode(', ', $logs)));

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
            $this->log('No product avaiable', Zend_Log::CRIT);
            return;
        }else $this->log(sprintf('Total products: %d', count($products)));

        $i = 0;
        $countProcessed = 0;
        foreach ($products as $product){
            //limit for test
            //if ($i++ == 10) break;
            $row = $this->_getErpProductBySku($product['sku']);
            if (!$row) continue;
            $this->_updatePrices($product, $row);
            $this->_updateStocks($product, $row);
            $countProcessed++;
        }

        $this->log(sprintf('Processed products: %d', $countProcessed));

        $this->log('Reindex catalog_product_price');
        Mage::getModel('index/indexer')->getProcessByCode('catalog_product_price')->reindexAll();

        //$this->log('Reindex cataloginventory_stock');
        //Mage::getModel('index/indexer')->getProcessByCode('cataloginventory_stock')->reindexAll();

        return sprintf('<a href="%s" target="_blank">%s</a>', Mage::getBaseUrl('media').'erp/'.$this->_logFile, Mage::helper('mterp')->__('View log'));
    }

    /**
     * Send new customer to ERP
     */
    public function customerRegisterSuccess($observer, $customer=null){
        if (!$this->_getMSSQLConnection()) return;
        $customer = $customer ? $customer : $observer->getEvent()->getCustomer();
        /* @var $customer Mage_Customer_Model_Customer */
        if ($customer->getId() && $customer->getPhoneNumber()){
            $sql = "
                INSERT INTO [dbo].[KHACHHANG] ([KHID],[KHCODE],[KHCID],[KHNID],[KHTEN],[GIOITINH],[DIENTHOAI],[DIDONG],[EMAIL],[NGAYTAO],[CREATEDDATE])
                VALUES (NEWID(),?,?,?,?,?,?,?,?,CONVERT(date, GETDATE()),GETDATE())
            ";

            $params = array(
                array($customer->getPhoneNumber(), SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8')),
                array(Mage::getStoreConfig('kidsplaza/erp/khcid'), SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8')),
                array(Mage::getStoreConfig('kidsplaza/erp/khnid'), SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8')),
                array($customer->getName(), SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8')),
                array($customer->getGender() ? ($customer->getGender() == 1 ? "Nam" : "Nữ") : null, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8')),
                array($customer->getPhoneNumber(), SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8')),
                array($customer->getPhoneNumber(), SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8')),
                array($customer->getEmail(), SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8'))
            );

            $this->_MSSQLQuery($sql, $params);
        }
    }
}