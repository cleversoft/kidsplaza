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
    protected $_isSync = true;

    public function setIsCron($flag=false){
        $this->_isSync = (bool)$flag;
    }

    public function getIsCron(){
        return $this->_isSync;
    }

    protected function _closeMSSQLConnection(){
        if ($this->_MSSQLConnection) sqlsrv_close($this->_MSSQLConnection);
        $this->log('Microsoft SQL Server closed');
    }

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
            if ($this->getIsCron()){
                $this->log('Microsoft SQL Server connected');
            }
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
        if (!$this->getIsCron()){
            Mage::log($message, $level);
            return;
        }
        if (!$this->_logger){
            $logDir = Mage::getBaseDir('media') . DS . 'erp';
            if (!$this->_logFile){
                $date = Mage::getModel('core/date');
                $this->_logFile = uniqid($date->date('Y-m-d-H-i-')) . '.log';
            }

            if (!is_dir($logDir)){
                mkdir($logDir);
                chmod($logDir, 0777);
            }

            if (!file_exists($logDir . DS . $this->_logFile)){
                file_put_contents($logDir . DS . $this->_logFile, '');
                chmod($logDir . DS . $this->_logFile, 0777);
            }

            $format = '%timestamp% %priorityName%: %message%' . PHP_EOL;
            $formatter = new Zend_Log_Formatter_Simple($format);

            $writer = new Zend_Log_Writer_Stream($logDir . DS . $this->_logFile);
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

    protected function _updatePrices($productId, $erp){
        if (!$productId || !$erp->MHCODE) return false;

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

        $connection->query($sql, array($price, $priceAttributeId, $productId));
        $connection->query($sql, array($specialPrice, $specialPriceAttributeId, $productId));

        $this->log(sprintf('PRICE SKU [%s] price=%s, special_price=%s', $erp->MHCODE, $price, $specialPrice ? $specialPrice : 'NULL'));

        return true;
    }

    protected function _updateParentStocks($parentId){
        if (!$parentId) return false;

        $stockId = null;
        $connection = $this->_getConnection('core_write');
        $logs = array();
        $inStock = 0;
        $countSql = "
            SELECT COUNT(*)
            FROM ".$this->_getTableName('cataloginventory_stock_status')."
            WHERE product_id = ? AND website_id = ?
        ";

        foreach ($this->_getWebsites() as $websiteId){
            $qty = $this->_getQtyParentProduct($parentId, $websiteId);
            $stockStatus = $qty > 0 ? 1 : 0;
            if ($stockStatus == 1) $inStock = 1;
            $isInTable = $connection->fetchOne($countSql, array($parentId, $websiteId));

            if ($isInTable){
                $updateSql = "
                    UPDATE " . $this->_getTableName('cataloginventory_stock_status') . "
                    SET qty = ?, stock_status = ?
                    WHERE product_id = ? AND website_id = ?
                ";

                $connection->query($updateSql, array(0, $stockStatus, $parentId, $websiteId));
            }else{
                if (is_null($stockId)){
                    $stockSql = "
                        SELECT stock_id FROM ".$this->_getTableName('cataloginventory_stock_item')."
                        WHERE product_id = ?
                    ";
                    $stockId = $connection->fetchOne($stockSql, array($parentId));
                    $stockId = $stockId  > 0 ? $stockId : 1;
                }

                $insertSql = "
                    INSERT INTO " . $this->_getTableName('cataloginventory_stock_status') . "
                    (product_id, website_id, stock_id, qty, stock_status) VALUES (?,?,?,?,?)
                ";

                $connection->query($insertSql, array($parentId, $websiteId, $stockId, 0, $stockStatus));
            }

            $logs[] = sprintf('%d=%d(%d)', $websiteId, $stockStatus, $qty);
        }

        $updateSql = "
            UPDATE ".$this->_getTableName('cataloginventory_stock_item')."
            SET is_in_stock = ?
            WHERE product_id = ?
        ";
        if ($connection->query($updateSql, array($inStock, $parentId)))
            $logs[] = sprintf('all=%d', $inStock);

        $this->log(sprintf('STOCK ID [%d] %s', $parentId, implode(', ', $logs)));

        return true;
    }

    protected function _updateStocks($productId, $erp){
        if (!$productId || !$erp->MHID) return false;

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

            $isInTable = $connection->fetchOne($countSql, array($productId, $website));

            if ($isInTable){
                $sql = "
                    UPDATE " . $this->_getTableName('cataloginventory_stock_status') . "
                    SET qty = ?, stock_status = ?
                    WHERE product_id = ? AND website_id = ?
                ";

                $connection->query($sql, array($newQty, $stockStatus, $productId, $website));
                $logs[] = sprintf("%d=%d", $website, $newQty);
            }else{
                if (is_null($stockId)){
                    $stockSql = "
                        SELECT stock_id FROM ".$this->_getTableName('cataloginventory_stock_item')."
                        WHERE product_id = ?
                    ";
                    $stockId = $connection->fetchOne($stockSql, array($productId));
                    $stockId = $stockId  > 0 ? $stockId : 1;
                }

                $sql = "
                    INSERT INTO " . $this->_getTableName('cataloginventory_stock_status') . "
                    (product_id, website_id, stock_id, qty, stock_status) VALUES (?,?,?,?,?)
                ";

                $connection->query($sql, array($productId, $website, $stockId, $newQty, $stockStatus));
                $logs[] = sprintf("%d=%d", $website, $newQty);
            }
        }

        $insertItemSql = "
            UPDATE " . $this->_getTableName('cataloginventory_stock_item') ."
            SET qty = ?, is_in_stock = ?
            WHERE product_id = ?
        ";

        if ($connection->query($insertItemSql, array($totalQty, $totalQty > 0 ? 1 : 0, $productId)))
            $logs[] = sprintf("all=%s", $totalQty);

        $this->log(sprintf('STOCK SKU [%s] %s', $erp->MHCODE, implode(', ', $logs)));

        return true;
    }

    protected function _getQtyParentProduct($parentId, $websiteId){
        if (!$parentId || !$websiteId) return 0;

        $connection = $this->_getConnection('core_read');
        $query = "
            SELECT SUM(css.qty) FROM ".$this->_getTableName('cataloginventory_stock_status')." AS css
            INNER JOIN ".$this->_getTableName('catalog_product_relation')." AS cpr ON cpr.child_id = css.product_id
            WHERE css.website_id = ? AND cpr.parent_id = ?
        ";

        return $connection->fetchOne($query, array($websiteId, $parentId));
    }

    protected function _getProductCollection(){
        $connection = $this->_getConnection('core_read');
        $query = sprintf("SELECT entity_id,type_id,sku FROM %s ORDER BY entity_id DESC", $this->_getTableName('catalog_product_entity'));
        return $connection->fetchAll($query);
    }

    protected function _getErpProductBySku($sku){
        if (!$sku) return false;
        $query = sprintf("SELECT * FROM [dbo].[MATHANG] WHERE [MHCODE] = '%s'", $sku);
        $rs = $this->_MSSQLQuery($query);
        if (!$rs) return null;
        return sqlsrv_fetch_object($rs);
    }

    protected function _getCatalogProductMeta(){
        $connection = $this->_getConnection('core_read');
        $query = sprintf(
            "SELECT eas.attribute_set_id FROM %s AS eas INNER JOIN %s AS eet ON eas.entity_type_id = eet.entity_type_id WHERE eet.entity_type_code = '%s'",
            $this->_getTableName('eav_attribute_set'),
            $this->_getTableName('eav_entity_type'),
            'catalog_product'
        );
        return $connection->fetchOne($query);
    }

    protected function _getChildProduct($parentId){
        if (!$parentId) return array();

        $connection = $this->_getConnection('core_read');
        $query = sprintf(
            "SELECT child_id FROM %s WHERE parent_id = %s",
            $this->_getTableName('catalog_product_relation'),
            $parentId
        );
        return $connection->fetchAll($query);
    }

    public function runAll(){
        $date = Mage::getModel('core/date');
        $this->_logFile = uniqid($date->date('Y-m-d-H-i-\a\l\l-')) . '.log';

        if (!$this->_getMSSQLConnection()) return;

        $sql = "SELECT [MHID],[MHCODE],[MHTEN],[SOLUONG],[GIABANLE] FROM [dbo].[MATHANG]";
        $rs = $this->_MSSQLQuery($sql);
        if (!$rs) return;

        $erpProducts = array();
        while (sqlsrv_fetch($rs)){
            $erpProducts[] = (object)array(
                'MHID'      => sqlsrv_get_field($rs, 0, SQLSRV_PHPTYPE_STRING('UTF-8')),
                'MHCODE'    => sqlsrv_get_field($rs, 1, SQLSRV_PHPTYPE_STRING('UTF-8')),
                'MHTEN'     => sqlsrv_get_field($rs, 2, SQLSRV_PHPTYPE_STRING('UTF-8')),
                'SOLUONG'   => sqlsrv_get_field($rs, 3, SQLSRV_PHPTYPE_STRING('UTF-8')),
                'GIABANLE'  => sqlsrv_get_field($rs, 4, SQLSRV_PHPTYPE_STRING('UTF-8'))
            );
        }

        $erpTotal = count($erpProducts);
        if (!$erpTotal) return;
        $this->log(sprintf('Total ERP products: %d', $erpTotal));

        $products = $this->_getProductCollection();
        $total = count($products);
        for ($i=0; $i<$total; $i++){
            $products[$products[$i]['sku']] = $products[$i];
        }

        $attributeSetId = $this->_getCatalogProductMeta();

        $updateProduct = 0;
        $insertProduct = 0;
        $failedProduct = 0;
        for ($i=0; $i<$erpTotal; $i++){
            //if ($i == 60) break;
            $erp = $erpProducts[$i];
            if (!isset($products[$erp->MHCODE])){
                $product = Mage::getModel('catalog/product');
                $product->setData(array(
                    'type_id'       => 'simple',
                    'attribute_set_id' => $attributeSetId,
                    'sku'           => $erp->MHCODE,
                    'name'          => $erp->MHTEN,
                    'website_ids'   => $this->_getWebsites(),
                    'weight'        => 1,
                    'visibility'    => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                    'status'        => 2,
                    'tax_class_id'  => 0,
                    'price'         => $erp->GIABANLE,
                    'stock_data'    => array(
                        'qty' => $erp->SOLUONG > 0 ? $erp->SOLUONG : 0,
                        'is_in_stock' => $erp->SOLUONG > 0 ? 1 : 0
                    )
                ));
                try{
                    $product->save();
                    $this->log(sprintf('INSERT SKU [%s]', $product->getSku()));
                    $insertProduct++;
                }catch (Exception $e){
                    $this->log(sprintf('INSERT ERROR SKU [%s]: %s', $product->getSku(), $e->getMessage()), Zend_Log::CRIT);
                    $failedProduct++;
                }
            }else{
                $product = $products[$erp->MHCODE];
                $this->_updatePrices($product['entity_id'], $erp);
                $this->_updateStocks($product['entity_id'], $erp);
                $updateProduct++;
            }
        }

        $this->log(sprintf('Update products: %d', $updateProduct));
        $this->log(sprintf('Insert products: %d', $insertProduct));
        $this->log(sprintf('Failed products: %d', $failedProduct));

        $this->_closeMSSQLConnection();

        return sprintf('<a href="%s" target="_blank">%s</a>',
            Mage::getBaseUrl('media').'erp/'.$this->_logFile,
            Mage::helper('mterp')->__('View log')
        );
    }

    public function run(){
        if (!$this->_getMSSQLConnection()) return;

        $products = $this->_getProductCollection();

        $total = count($products);
        if (!$total){
            $this->log('No product avaiable', Zend_Log::CRIT);
            return;
        }else $this->log(sprintf('Total products: %d', $total));

        $countProcessed = 0;
        $configurableProducts = array();

        for ($i=0; $i<$total; $i++){
            if ($products[$i]['type_id'] == 'configurable'){
                $configurableProducts[] = $products[$i];
                continue;
            }elseif ($products[$i]['type_id'] == 'simple'){
                $row = $this->_getErpProductBySku($products[$i]['sku']);
                if (!$row) continue;
                //if ($i == 5) break;
                $this->_updatePrices($products[$i]['entity_id'], $row);
                $this->_updateStocks($products[$i]['entity_id'], $row);
                $countProcessed++;
            }
        }

        $totalConfigurableProducts = count($configurableProducts);
        if ($totalConfigurableProducts){
            for ($i=0; $i<$totalConfigurableProducts; $i++){
                $this->_updateParentStocks($configurableProducts[$i]['entity_id']);
                $countProcessed++;
            }
        }

        $this->log(sprintf('Update products: %d', $countProcessed));

        $this->log('Reindex catalog_product_price');
        Mage::getModel('index/indexer')->getProcessByCode('catalog_product_price')->reindexAll();

        $this->_closeMSSQLConnection();

        return sprintf('<a href="%s" target="_blank">%s</a>',
            Mage::getBaseUrl('media').'erp/'.$this->_logFile,
            Mage::helper('mterp')->__('View log')
        );
    }

    /**
     * Get ERP customer by phone number
     */
    public function getErpCustomer($phoneNumber){
        if (!$phoneNumber) return null;
        if (!$this->_getMSSQLConnection()) return null;
        $sql = "SELECT TOP 1 [KHTEN],[GIOITINH],[EMAIL],[DIACHI] FROM [dbo].[KHACHHANG] WHERE [DIENTHOAI] = ? OR [DIDONG] = ?";
        return $this->_MSSQLQuery($sql, array(
            array($phoneNumber, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8')),
            array($phoneNumber, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8'))
        ));
    }

    /**
     * Send new customer to ERP
     */
    public function customerRegisterSuccess($observer, $customer=null){
        if (!Mage::getStoreConfigFlag('kidsplaza/erp/customer')) return;
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

    /**
     * Log cleaning
     */
    public function clearLog(){
        if (!Mage::getStoreConfigFlag('kidsplaza/erp/enable_clear_log')) return;
        $logDir = Mage::getBaseDir('media') . DS . 'erp';
        if (!is_dir($logDir)) return;
        $keepLogDays = (int)Mage::getStoreConfig('kidsplaza/erp/log_days');
        $keepLogDays = $keepLogDays > 0 ? $keepLogDays : 7;
        $date = Mage::getModel('core/date');
        $today = $date->timestamp($date->date('Y-m-d'));
        $expireDate = $date->timestamp($date->date('Y-m-d', $today - $keepLogDays * 86400));
        foreach (scandir($logDir) as $file){
            $filePath = $logDir . DS .$file;
            if (!file_exists($filePath)) continue;
            $parts = explode('-', $file);
            if (count($parts) <= 3 || (int)$parts[0] == 0 || (int)$parts[1] == 0 || (int)$parts[2] == 0) continue;
            $dateOfFile = $date->timestamp(sprintf('%d-%d-%d', $parts[0], $parts[1], $parts[2]));
            if ($dateOfFile < $expireDate) @unlink($filePath);
        }
    }
}