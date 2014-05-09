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
    protected $_adapter;
    protected $_attributes;
    protected $_eavAttributes;
    protected $_entityTypes;
    protected $_promotions;
    protected $_websites;
    protected $_websiteNames;
    protected $_stores;
    protected $_priceWebsite;
    protected $_specialPriceWebsite;
    protected $_websiteStores;
    protected $_defaultPriceStore;
    protected $_logger;
    protected $_logFile;
    protected $_isSync = true;

    public function __construct(){
        switch (Mage::getStoreConfig('kidsplaza/erp/adapter')){
            case 'db':
                $host = Mage::getStoreConfig('kidsplaza/erp/host');
                $user = Mage::getStoreConfig('kidsplaza/erp/user');
                $pass = Mage::getStoreConfig('kidsplaza/erp/pass');
                $db   = Mage::getStoreConfig('kidsplaza/erp/db');
                $this->_adapter = Mage::getModel('mterp/adapter_database', array($host, $user, $pass, $db));
                break;
            case 'api':
                $url = Mage::getStoreConfig('kidsplaza/erp/api');
                $this->_adapter = Mage::getModel('mterp/adapter_api', array($url));
                break;
            default:
                throw new Exception(Mage::helper('mterp')->__('No adapter found'));
        }

        return $this;
    }

    public function setIsCron($flag=false){
        $this->_isSync = (bool)$flag;
    }

    public function getIsCron(){
        return $this->_isSync;
    }

    protected function log($message, $level=Zend_Log::INFO){
        if (!$this->getIsCron()){
            Mage::log($message, $level);
            return;
        }
        if (is_null($this->_logger)){
            $logDir = Mage::getBaseDir('media') . DS . 'erp';
            if (is_null($this->_logFile)){
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

        if (!isset($this->_attributes[$attribute_code])) {
            $connection = $this->_getConnection('core_read');
            $sql = "
                SELECT attribute_id
                FROM {$this->_getTableName('eav_attribute')}
                WHERE entity_type_id = ? AND attribute_code = ?
            ";
            $entity_type_id = $this->_getEntityTypeId('catalog_product');
            $this->_attributes[$attribute_code] = $connection->fetchOne($sql, array($entity_type_id, $attribute_code));
        }

        return $this->_attributes[$attribute_code];
    }

    protected function _getEntityTypeId($entity_type_code='catalog_product'){
        if (!$entity_type_code) return null;

        if (!isset($this->_entityTypes[$entity_type_code])) {
            $connection = $this->_getConnection('core_read');
            $sql = "SELECT entity_type_id FROM {$this->_getTableName('eav_entity_type')} WHERE entity_type_code = ?";
            $this->_entityTypes[$entity_type_code] = $connection->fetchOne($sql, array($entity_type_code));
        }

        return $this->_entityTypes[$entity_type_code];
    }

    /**
     * Get all products in promotions from ERP database
     */
    protected function _getErpProductPromotion($storeId){
        if (isset($this->_promotions[$storeId])){
            return $this->_promotions[$storeId];
        }

        $this->_promotions[$storeId] = $this->_adapter->getProductsPromotion(null, $storeId);

        return $this->_promotions[$storeId];
    }

    /**
     * Get all websites in Magento
     */
    protected function _getWebsites(){
        if (is_null($this->_websites)){
            foreach (Mage::app()->getWebsites() as $website){
                $websiteId = $website->getId();

                $this->_websites[] = $websiteId;
                $this->_websiteNames[$websiteId] = $website->getCode();

                $this->_websiteStores[$websiteId] = array();
                foreach ($website->getStores() as $store){
                    $this->_websiteStores[$websiteId][] = $store->getId();
                }

                $this->_getErpStoresByWebsite($websiteId);
                $this->_getErpPriceStoreByWebsite($websiteId);
                $this->_getErpSpecialPriceStoreByWebsite($websiteId);
                $this->_getErpDefaultPriceStore();
            }
        }

        return $this->_websites;
    }

    protected function _getErpDefaultPriceStore(){
        if ($this->_defaultPriceStore !== null){
            return $this->_defaultPriceStore;
        }

        $storeString = Mage::getStoreConfig('kidsplaza/product/price_store_default');
        if (!$storeString){
            $this->log(Mage::helper('mterp')->__('Default price store not found. Ignore default price'));
        }

        $this->_defaultPriceStore = strtoupper($storeString);
        return $storeString;
    }

    protected function _getErpStoresByWebsite($website){
        if (!$website) return array();

        if (isset($this->_stores[$website])){
            return $this->_stores[$website];
        }

        $storeInWebsite = $this->_websiteStores[$website][0];

        $storesString = Mage::getStoreConfig('kidsplaza/product/stock_stores', $storeInWebsite);
        $erpStores = array();

        if (!$storesString){
            $this->log(Mage::helper('mterp')->__('Stores for website %d not found. Ignore stock for this website', $website));
        }else{
            foreach (explode(',', $storesString) as $storeString){
                $erpStores[] = strtoupper($storeString);
            }
        }

        $this->_stores[$website] = $erpStores;
        return $erpStores;
    }

    protected function _getErpStockByWebsite($productId, $websiteId){
        if (!$productId || !$websiteId) return null;

        $stores = $this->_getErpStoresByWebsite($websiteId);
        if (!count($stores)) return null;

        return (int)$this->_adapter->getStockByStores($productId, $stores);
    }

    protected function _getErpPriceStoreByWebsite($websiteId){
        if (isset($this->_priceWebsite[$websiteId])){
            return $this->_priceWebsite[$websiteId];
        }

        $storeInWebsite = $this->_websiteStores[$websiteId][0];

        $storeString = Mage::getStoreConfig('kidsplaza/product/price_store', $storeInWebsite);
        if (!$storeString){
            $this->log(Mage::helper('mterp')->__('Price store for website [%d] not found. Ignore price for this website', $this->_websiteNames[$websiteId]));
        }

        $this->_priceWebsite[$websiteId] = strtoupper($storeString);
        return $storeString;
    }

    protected function _getErpPriceByWebsite($productId, $websiteId){
        if (!$productId || !$websiteId) return 0;

        $storeId = $this->_getErpPriceStoreByWebsite($websiteId);
        if (!$storeId) return 0;

        return $this->_adapter->getPriceByStore($productId, $storeId);
    }

    protected function _getErpSpecialPriceStoreByWebsite($websiteId){
        if (isset($this->_specialPriceWebsite[$websiteId])){
            return $this->_specialPriceWebsite[$websiteId];
        }

        $storeInWebsite = $this->_websiteStores[$websiteId][0];

        $storeString = Mage::getStoreConfig('kidsplaza/product/promotion_store', $storeInWebsite);
        if (!$storeString){
            $this->log(Mage::helper('mterp')->__('Promotion store for website [%d] not found. Ignore promotion price for this website', $this->_websiteNames[$websiteId]));
        }

        $this->_specialPriceWebsite[$websiteId] = strtoupper($storeString);
        return $storeString;
    }

    protected function _getErpDefaultPrice($productId){
        if (!$productId) return 0;

        $storeId = $this->_getErpDefaultPriceStore();
        if (!$storeId) return 0;

        return $this->_adapter->getPriceByStore($productId, $storeId);
    }

    protected function _getErpSpecialPriceByWebsite($productId, $websiteId){
        if (!$productId || !$websiteId) return null;

        $erpStore = $this->_getErpSpecialPriceStoreByWebsite($websiteId);
        if (!$erpStore) return null;

        $promotions = $this->_getErpProductPromotion($erpStore);

        if (isset($promotions[$productId])){
            return $promotions[$productId]['specialPrice'];
        }
        return null;
    }

    protected function _updatePrices($productId, $erpProduct){
        if (!$productId || !is_array($erpProduct)) return false;

        $connection = $this->_getConnection('core_write');
        $priceAttributeId = $this->_getAttributeId('price');
        $specialPriceAttributeId = $this->_getAttributeId('special_price');
        $entityTypeId = $this->_getEntityTypeId('catalog_product');

        $checkSql = "
            SELECT COUNT(*)
            FROM {$this->_getTableName('catalog_product_entity_decimal')}
            WHERE entity_id = ? AND attribute_id = ? AND entity_type_id = ? AND store_id = ?
        ";

        $updateSql = "
            UPDATE {$this->_getTableName('catalog_product_entity_decimal')}
            SET value = ?
            WHERE attribute_id = ? AND entity_id = ? AND store_id = ? AND entity_type_id = ?
        ";

        $insertSql = "
            INSERT INTO {$this->_getTableName('catalog_product_entity_decimal')}
            (entity_type_id,attribute_id,store_id,entity_id,value)
            VALUES (?,?,?,?,?)
        ";

        $logs1 = array();
        $logs2 = array();

        foreach ($this->_getWebsites() as $websiteId) {
            $price = $this->_getErpPriceByWebsite($erpProduct['productId'], $websiteId);
            $specialPrice = $this->_getErpSpecialPriceByWebsite($erpProduct['productId'], $websiteId);

            foreach ($this->_websiteStores[$websiteId] as $storeId) {
                if ($connection->fetchOne($checkSql, array($productId, $priceAttributeId, $entityTypeId, $storeId))) {
                    $connection->query($updateSql, array($price, $priceAttributeId, $productId, $storeId, $entityTypeId));
                } else {
                    $connection->query($insertSql, array($entityTypeId, $priceAttributeId, $storeId, $productId, $price));
                }

                if ($connection->fetchOne($checkSql, array($productId, $specialPriceAttributeId, $entityTypeId, $storeId))) {
                    $connection->query($updateSql, array($specialPrice, $specialPriceAttributeId, $productId, $storeId, $entityTypeId));
                } else {
                    $connection->query($insertSql, array($entityTypeId, $specialPriceAttributeId, $storeId, $productId, $specialPrice));
                }
            }

            $logs1[] = sprintf('%s=%d', $this->_websiteNames[$websiteId], $price);
            if ($specialPrice) {
                $logs2[] = sprintf('%s=%d', $this->_websiteNames[$websiteId], $specialPrice);
            }
        }

        $defaultPrice = $this->_getErpDefaultPrice($erpProduct['productId']);
        if ($connection->fetchOne($checkSql, array($productId, $priceAttributeId, $entityTypeId, 0))) {
            $connection->query($updateSql, array($defaultPrice, $priceAttributeId, $productId, 0, $entityTypeId));
        } else {
            $connection->query($insertSql, array($entityTypeId, $priceAttributeId, 0, $productId, $defaultPrice));
        }
        $logs1[] = sprintf('default=%d', $defaultPrice);

        $this->log(sprintf("\tPRICE: %s", implode(', ', $logs1)));
        if (count($logs2)){
            $this->log(sprintf("\tPROMO: %s", implode(', ', $logs2)));
        }

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
            FROM {$this->_getTableName('cataloginventory_stock_status')}
            WHERE product_id = ? AND website_id = ?
        ";

        foreach ($this->_getWebsites() as $websiteId){
            $qty = (int)$this->_getQtyParentProduct($parentId, $websiteId);
            $stockStatus = $qty > 0 ? 1 : 0;
            if ($stockStatus == 1) $inStock = 1;
            $isInTable = $connection->fetchOne($countSql, array($parentId, $websiteId));

            if ($isInTable){
                $updateSql = "
                    UPDATE {$this->_getTableName('cataloginventory_stock_status')}
                    SET qty = ?, stock_status = ?
                    WHERE product_id = ? AND website_id = ?
                ";

                $connection->query($updateSql, array(0, $stockStatus, $parentId, $websiteId));
            }else{
                if (is_null($stockId)){
                    $stockSql = "
                        SELECT stock_id FROM {$this->_getTableName('cataloginventory_stock_item')}
                        WHERE product_id = ?
                    ";
                    $stockId = $connection->fetchOne($stockSql, array($parentId));
                    $stockId = $stockId  > 0 ? $stockId : 1;
                }

                $insertSql = "
                    INSERT INTO {$this->_getTableName('cataloginventory_stock_status')}
                    (product_id, website_id, stock_id, qty, stock_status) VALUES (?,?,?,?,?)
                ";

                $connection->query($insertSql, array($parentId, $websiteId, $stockId, 0, $stockStatus));
            }

            $logs[] = sprintf('%s=%d', $this->_websiteNames[$websiteId], $qty);
        }

        $updateSql = "
            UPDATE {$this->_getTableName('cataloginventory_stock_item')}
            SET is_in_stock = ?
            WHERE product_id = ?
        ";

        if ($connection->query($updateSql, array($inStock, $parentId))) {
            $logs[] = sprintf('stock=%d', $inStock);
        }

        $this->log(sprintf("\tSTOCK: %s", implode(', ', $logs)));

        return true;
    }

    protected function _updateStocks($productId, $erpProduct){
        if (!$productId || !is_array($erpProduct)) return false;

        $connection = $this->_getConnection('core_write');
        $websites = $this->_getWebsites();
        $logs = array();
        $stockId = null;
        $totalQty = 0;

        $checkStatusSql = "
            SELECT COUNT(*)
            FROM {$this->_getTableName('cataloginventory_stock_status')}
            WHERE product_id = ? AND website_id = ?
        ";

        foreach ($websites as $website){
            $newQty = $this->_getErpStockByWebsite($erpProduct['productId'], $website);
            $stockStatus = $newQty > 0 ? 1 : 0;
            $totalQty += $newQty;

            $isInTable = $connection->fetchOne($checkStatusSql, array($productId, $website));

            if ($isInTable){
                $sql = "
                    UPDATE {$this->_getTableName('cataloginventory_stock_status')}
                    SET qty = ?, stock_status = ?
                    WHERE product_id = ? AND website_id = ?
                ";

                $connection->query($sql, array($newQty, $stockStatus, $productId, $website));
                $logs[] = sprintf("%s=%d", $this->_websiteNames[$website], $newQty);
            }else{
                if (is_null($stockId)){
                    $stockSql = "
                        SELECT stock_id FROM {$this->_getTableName('cataloginventory_stock_item')}
                        WHERE product_id = ?
                    ";
                    $stockId = $connection->fetchOne($stockSql, array($productId));
                    $stockId = $stockId  > 0 ? $stockId : 1;
                }

                $sql = "
                    INSERT INTO {$this->_getTableName('cataloginventory_stock_status')}
                    (product_id, website_id, stock_id, qty, stock_status) VALUES (?,?,?,?,?)
                ";

                $connection->query($sql, array($productId, $website, $stockId, $newQty, $stockStatus));
                $logs[] = sprintf("%s=%d", $this->_websiteNames[$website], $newQty);
            }
        }

        $checkItemSql = "SELECT COUNT(*) FROM {$this->_getTableName('cataloginventory_stock_item')} WHERE product_id = ?";
        if ($connection->fetchOne($checkItemSql, array($productId))){
            $updateItemSql = "
                UPDATE {$this->_getTableName('cataloginventory_stock_item')}
                SET qty = ?, is_in_stock = ?
                WHERE product_id = ?
            ";

            $connection->query($updateItemSql, array($totalQty, $totalQty > 0 ? 1 : 0, $productId));
        }else{
            $insertItemSql = "
                INSERT INTO {$this->_getTableName('cataloginventory_stock_item')}
                (product_id,stock_id,qty,use_config_min_qty,is_qty_decimal,use_config_backorders,use_config_min_sale_qty,use_config_max_sale_qty,is_in_stock,use_config_notify_stock_qty,use_config_manage_stock,use_config_qty_increments,use_config_enable_qty_inc)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
            ";

            $connection->query($insertItemSql, array($productId, 1, $totalQty, 1, 0, 1, 1, 1, $totalQty > 0 ? 1 : 0, 1, 1, 1, 1));
        }
        $logs[] = sprintf("total=%s", $totalQty);

        $this->log(sprintf("\tSTOCK: %s", implode(', ', $logs)));
        return true;
    }

    protected function _getQtyParentProduct($parentId, $websiteId){
        if (!$parentId || !$websiteId) return 0;

        $connection = $this->_getConnection('core_read');
        $query = "
            SELECT SUM(css.qty) FROM {$this->_getTableName('cataloginventory_stock_status')} AS css
            INNER JOIN {$this->_getTableName('catalog_product_relation')} AS cpr ON cpr.child_id = css.product_id
            WHERE css.website_id = ? AND cpr.parent_id = ?
        ";

        return $connection->fetchOne($query, array($websiteId, $parentId));
    }

    protected function _getProductCollection(){
        $connection = $this->_getConnection('core_read');
        $query = "SELECT entity_id,type_id,sku FROM {$this->_getTableName('catalog_product_entity')} ORDER BY entity_id DESC";
        return $connection->fetchAll($query);
    }

    protected function _getCatalogProductMeta(){
        $connection = $this->_getConnection('core_read');
        $query = "
            SELECT eas.attribute_set_id
            FROM {$this->_getTableName('eav_attribute_set')} AS eas
            INNER JOIN {$this->_getTableName('eav_entity_type')} AS eet ON eas.entity_type_id = eet.entity_type_id
            WHERE eet.entity_type_code = ?";
        return $connection->fetchOne($query, array('catalog_product'));
    }

    protected function _getChildProduct($parentId){
        if (!$parentId) return array();

        $connection = $this->_getConnection('core_read');
        $query = "SELECT child_id FROM {$this->_getTableName('catalog_product_relation')} WHERE parent_id = ?";
        return $connection->fetchAll($query, array($parentId));
    }


    protected function _insertEntity($product){
        if (!$product->getSku() || !$product->getTypeId() || !$product->getAttributeSetId()){
            throw new Exception('SKU, Type Id or Attribute Set Id not found');
        }
        $connection = $this->_getConnection('core_write');
        $checkSql = "SELECT entity_id FROM {$this->_getTableName('catalog_product_entity')} WHERE sku = ?";
        $rs = $connection->fetchOne($checkSql, array($product->getSku()));
        if ($rs){
            $product->setId($rs);
        }else{
            $insertSql = "
            INSERT INTO {$this->_getTableName('catalog_product_entity')}
            (entity_type_id,attribute_set_id,type_id,sku,created_at,updated_at) VALUES (?,?,?,?,NOW(),NOW())";
            $connection->query($insertSql, array(
                $product->getEntityTypeId(),
                $product->getAttributeSetId(),
                $product->getTypeId(),
                $product->getSku()
            ));
            $entityId = $connection->fetchOne("SELECT LAST_INSERT_ID() FROM {$this->_getTableName('catalog_product_entity')}");
            $product->setId($entityId);
        }
    }

    protected function _getEavAttributes($model){
        $entityTypeId = $model->getEntityTypeId();
        if (!$entityTypeId){
            throw new Exception('Entity Type Id not found');
        }

        if (isset($this->_eavAttributes[$entityTypeId])) return $this->_eavAttributes[$entityTypeId];

        $connection = $this->_getConnection('core_read');
        $sql = "SELECT attribute_id,attribute_code,backend_type FROM {$this->_getTableName('eav_attribute')} WHERE entity_type_id = ?";
        $this->_eavAttributes[$entityTypeId] = $connection->fetchAll($sql, array($entityTypeId));

        return $this->_eavAttributes[$entityTypeId];
    }

    protected function _insertAttributes($product){
        if (!$product->getId()){
            throw new Exception('Product Id not found');
        }

        $productAttributes = $this->_getEavAttributes($product);
        $connection = $this->_getConnection('core_write');
        foreach ($productAttributes as $productAttribute){
            if ($product->getData($productAttribute['attribute_code']) === null) continue;
            $backendType = $productAttribute['backend_type'];
            switch ($backendType){
                case 'varchar':
                case 'decimal':
                case 'int':
                case 'text':
                    $entityTable = $this->_getTableName('catalog_product_entity_' . $backendType);
                    break;
                default:
                    $entityTable = null;
            }
            if (is_null($entityTable)) continue;
            $sql = "INSERT INTO {$entityTable} (entity_type_id,attribute_id,store_id,entity_id,value) VALUES (?,?,?,?,?)";
            $connection->query($sql, array(
                $product->getEntityTypeId(),
                $productAttribute['attribute_id'],
                0,
                $product->getId(),
                $product->getData($productAttribute['attribute_code'])
            ));
        }
    }

    protected function _insertWebsites($product){
        if (!$product->getId()){
            throw new Exception('Product Id not found');
        }

        $websites = $product->getData('website_ids');
        if (!$websites) return;
        if (!is_array($websites)) $websites = explode(',', $websites);
        $connection = $this->_getConnection('core_write');

        $sql = "INSERT INTO {$this->_getTableName('catalog_product_website')} (product_id,website_id) VALUES (?,?)";
        foreach ($websites as $website){
            if (!is_numeric($website)) continue;
            $connection->query($sql, array($product->getId(), $website));
        }
    }

    protected function _saveProduct($product){
        if (!$product instanceof Mage_Catalog_Model_Product){
            throw new Exception('Product Model invalid');
        }

        $this->_insertEntity($product);
        $this->_insertAttributes($product);
        $this->_insertWebsites($product);
    }

    /**
     * Sync from ERP
     */
    public function runAll(){
        try{
            $startTime = microtime(true);
            $date = Mage::getModel('core/date');
            $this->_logFile = uniqid($date->date('Y-m-d-H-i-\A\L\L-')) . '.log';

            $erpTotal = $this->_adapter->getProductCount();
            if (!$erpTotal){
                throw new Exception('No ERP product found');
            }
            $this->log(sprintf('Total ERP products: %d', $erpTotal));

            $paging     = 100;
            $pageTotal  = ceil($erpTotal / $paging);
            $this->log(sprintf('Page limit: %d, total pages: %d', $paging, $pageTotal));

            $products = $this->_getProductCollection();
            $webTotal = count($products);
            $this->log(sprintf('Total website products: %d', $webTotal));

            $configurableProducts   = array();
            $simpleProducts         = array();

            for ($i=0; $i<$webTotal; $i++){
                if ($products[$i]['type_id'] == 'configurable'){
                    $configurableProducts[] = $products[$i];
                }elseif ($products[$i]['type_id'] == 'simple'){
                    $simpleProducts[$products[$i]['sku']] = $products[$i];
                }
            }

            unset ($products);
            $totalConfigurableProducts = count($configurableProducts);
            $this->log(sprintf('Total configurable products: %d', $totalConfigurableProducts));
            $this->log(sprintf('Total simple products: %d', count($simpleProducts)));

            $attributeSetId = $this->_getCatalogProductMeta();
            if (!$attributeSetId){
                throw new Exception('Product attribute set not found');
            }

            $updateProduct  = 0;
            $insertProduct  = 0;
            $failedProduct  = 0;
            $limit          = 0;

            $this->_getWebsites();

            for ($i=1; $i<$pageTotal; $i++){
                $erpProducts    = $this->_adapter->getProducts($i, $paging);
                $currentTotal   = count($erpProducts);
                $this->log(sprintf("\nPage: %d, products: %d\n", $i, $currentTotal));

                if (!$currentTotal){
                    $this->log('No product from ERP. Abort fetching.');
                    break;
                }

                for ($j=0; $j<$currentTotal; $j++){
                    //if ($limit++ >= 1000) break 2;

                    $erpProduct = $erpProducts[$j];

                    if (!isset($simpleProducts[$erpProduct['productCode']])){
                        $product = Mage::getModel('catalog/product');
                        $product->setData(array(
                            'entity_type_id'    => 4,
                            'type_id'           => 'simple',
                            'attribute_set_id'  => $attributeSetId,
                            'sku'               => $erpProduct['productCode'],
                            'name'              => $erpProduct['productName'],
                            'website_ids'       => $this->_getWebsites(),
                            'weight'            => 1,
                            'visibility'        => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                            'status'            => Mage_Catalog_Model_Product_Status::STATUS_DISABLED,
                            'tax_class_id'      => 0
                        ));

                        try{
                            $this->_saveProduct($product);
                            $this->log(sprintf('INSERT SKU [%s]', $product->getSku()));
                            $this->_updatePrices($product->getId(), $erpProduct);
                            $this->_updateStocks($product->getId(), $erpProduct);
                            $insertProduct++;
                        }catch (Exception $e){
                            $this->log(sprintf('INSERT ERROR SKU [%s]: %s', $product->getSku(), $e->getMessage()), Zend_Log::CRIT);
                            Mage::logException($e);
                            $failedProduct++;
                        }
                    }else{
                        $this->log(sprintf('FOUND SKU [%s]', $erpProduct['productCode']));
                        $product = $simpleProducts[$erpProduct['productCode']];
                        $this->_updatePrices($product['entity_id'], $erpProduct);
                        $this->_updateStocks($product['entity_id'], $erpProduct);
                        $updateProduct++;
                    }
                }
            }

            if ($totalConfigurableProducts){
                $this->log('Process configurable products');
                for ($i=0; $i<$totalConfigurableProducts; $i++){
                    $this->log(sprintf('CONFIG SKU [%s]', $configurableProducts[$i]['sku']));
                    $this->_updateParentStocks($configurableProducts[$i]['entity_id']);
                    $updateProduct++;
                }
            }

            $this->log(sprintf('Updated products: %d', $updateProduct));
            $this->log(sprintf('Inserted products: %d', $insertProduct));
            $this->log(sprintf('Failed products: %d', $failedProduct));

            $this->log('Reindex catalog_product_price');
            Mage::getModel('index/indexer')->getProcessByCode('catalog_product_price')->reindexAll();

            $this->log('Done!');
            $this->log(sprintf('Excution time: %ds', microtime(true) - $startTime));
            $this->_adapter->close();
        }catch (Exception $e){
            Mage::logException($e);
            $this->log($e->getMessage(), Zend_Log::CRIT);
        }

        return sprintf('<a href="%s" target="_blank">%s</a>',
            Mage::getBaseUrl('media') . 'erp/' . $this->_logFile,
            Mage::helper('mterp')->__('View log')
        );
    }

    /**
     * Sync with ERP
     */
    public function run(){
        try{
            $products = $this->_getProductCollection();

            $total = count($products);
            if (!$total){
                throw new Exception('No product avaiable');
            }
            $this->log(sprintf('Total products: %d', $total));

            $countProcessed = 0;
            $configurableProducts = array();

            for ($i=0; $i<$total; $i++){
                //if ($i == 10) break;
                if ($products[$i]['type_id'] == 'configurable'){
                    $configurableProducts[] = $products[$i];
                    continue;
                }elseif ($products[$i]['type_id'] == 'simple'){
                    $erpProduct = $this->_adapter->getProductBySku($products[$i]['sku']);
                    if (!$erpProduct) continue;
                    $this->_updatePrices($products[$i]['entity_id'], $erpProduct);
                    $this->_updateStocks($products[$i]['entity_id'], $erpProduct);
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

            $this->_adapter->close();
        }catch (Exception $e){
            Mage::logException($e);
            $this->log($e->getMessage(), Zend_Log::CRIT);
        }

        return sprintf('<a href="%s" target="_blank">%s</a>',
            Mage::getBaseUrl('media') . 'erp/' . $this->_logFile,
            Mage::helper('mterp')->__('View log')
        );
    }

    /**
     * Get ERP customer by phone number
     */
    public function getErpCustomer($phoneNumber){
        try{
            if (!$phoneNumber) return null;

            $customer = $this->_adapter->getCustomerByTelephone($phoneNumber);
            $this->_adapter->close();
            return $customer;
        }catch (Exception $e){
            Mage::log($e);
            return null;
        }
    }

    public function checkoutTypeOnepageSaveOrderAfter($observer){
        try{
            $quote = $observer->getEvent()->getQuote();
            if ($quote->getIsNewCustomer()){
                $customer = $observer->getEvent()->getOrder()->getCustomer();
                foreach ($customer->getAddresses() as $address){
                    $phoneNumber = $address->getTelephone();
                    if ($phoneNumber){
                        $customer->setPhoneNumber($phoneNumber);
                        $customer->save();
                        $this->_adapter->addCustomer($customer);
                        $this->_adapter->close();
                        break;
                    }
                }
            }
        }catch (Exception $e){}
    }

    /**
     * Send new customer to ERP
     */
    public function customerRegisterSuccess($observer, $customer=null){
        try{
            if (!Mage::getStoreConfigFlag('kidsplaza/customer/customer')) return;

            /* @var $customer Mage_Customer_Model_Customer */
            $customer = $customer ? $customer : $observer->getEvent()->getCustomer();

            if ($customer->getId() && $customer->getPhoneNumber()){
                $this->_adapter->addCustomer($customer);
                $this->_adapter->close();
            }
        }catch (Exception $e){
            Mage::log($e);
            return null;
        }
    }

    /**
     * Log cleaning
     */
    public function clearLog(){
        if (!Mage::getStoreConfigFlag('kidsplaza/log/enable_clear_log')) return;
        $logDir = Mage::getBaseDir('media') . DS . 'erp';
        if (!is_dir($logDir)) return;
        $keepLogDays = (int)Mage::getStoreConfig('kidsplaza/log/log_days');
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

    public function getErpStores(){
        return $this->_adapter->getAllStores();
    }
}