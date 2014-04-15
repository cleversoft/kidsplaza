<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */

/* config here */

$mssql_host     = 'LBEE-PC\SQLEXPRESS';
$mssql_user     = 'sa';
$mssql_pass     = 'long123';
$mssql_db       = 'HTsoft';
$mssql_stores   = array(
    1 => array(
        'DD18FA75-D1C5-4E67-9707-14BFBF7A5676',
        '98E8C1E1-C670-4A36-9310-1F117CC0A73F',
        '9A14252B-570E-4886-B641-4FBD26A4924B',
        '630EC97B-B12D-405C-8BD2-5E6F112145EB',
        'D91F6D5B-9B9A-47A9-AAC0-68277E3BE1E0',
        '930A43C1-6184-4838-A156-6DC8DD200948',
        '0E2242BB-57D6-4A59-A64C-C7F2348FC11F',
        '45719452-46DB-4024-B9A1-D63097B80054',
        '891E7A0C-89FC-4411-8636-F683ACC24113',
        '9D72217F-F375-4725-A86B-FBD64D93EEF1'
    ),
    2 => array(
        'F36E7DB8-6190-4DC9-865D-22BBCA876F77',
        '5B5DE19D-4D2C-40D4-90B5-1D7A305D23F3',
        '76BF17D8-D55E-4080-BD98-4DB66EEDBC95'
    )
);

/* end config */

/* functions helper */
function ms_query($query){
    global $mssql_conn;

    $rs = sqlsrv_query($mssql_conn, $query);
    if (!$rs) {
        runlog("ERROR: SQL Server query: {$query}");
        print_r(sqlsrv_errors());
        die;
    }

    return $rs;
}

function runlog($message, $die=false){
    switch (PHP_SAPI){
        case 'cli':
            echo $message."\n";
            break;
        default:
            echo $message.'<br>';
            break;
    }
    if ($die) die();
}

function _getConnection($type = 'core_read'){
    return Mage::getSingleton('core/resource')->getConnection($type);
}

function _getTableName($tableName){
    return Mage::getSingleton('core/resource')->getTableName($tableName);
}

function _getAttributeId($attribute_code){
    global $attributes;

    if (!$attribute_code) return null;
    if (isset($attributes[$attribute_code])) return $attributes[$attribute_code];
    $connection = _getConnection('core_read');
    $sql = "
        SELECT attribute_id
        FROM " . _getTableName('eav_attribute') . "
        WHERE entity_type_id = ? AND attribute_code = ?";

    $entity_type_id = _getEntityTypeId('catalog_product');
    $attributes[$attribute_code] = $connection->fetchOne($sql, array($entity_type_id, $attribute_code));

    return $attributes[$attribute_code];
}

function _getEntityTypeId($entity_type_code='catalog_product'){
    global $entityTypes;

    if (!$entity_type_code) return null;
    if (isset($entityTypes[$entity_type_code])) return $entityTypes[$entity_type_code];
    $connection = _getConnection('core_read');
    $sql = "SELECT entity_type_id FROM " . _getTableName('eav_entity_type') . " WHERE entity_type_code = ?";
    $entityTypes[$entity_type_code] = $connection->fetchOne($sql, array($entity_type_code));

    return $entityTypes[$entity_type_code];
}

function _getIdFromSku($sku){
    if (!$sku) return null;
    $connection = _getConnection('core_read');
    $sql = "SELECT entity_id FROM " . _getTableName('catalog_product_entity') . " WHERE sku = ?";
    return $connection->fetchOne($sql, array($sku));
}

/**
 * Get all products in promotions from ERP database
 */
function _getErpProductPromotion(){
    global $promotions;

    if (!count($promotions)){
        $sql = "
            SELECT km.[MHID], mh.[MHCODE], km.[GIABANLE], km.[GIAKM]
            FROM [dbo].[KHUYENMAICT] as km
            LEFT JOIN [dbo].[MATHANG] as mh ON km.[MHID] = mh.[MHID]
            WHERE km.[KMID] IN (
                SELECT [KMID]
                FROM [dbo].[KHUYENMAI]
                WHERE (GETDATE() BETWEEN [TUNGAY] and [DENNGAY]) AND [APPLYTOALLKH] = 1
            )";

        $rs = ms_query($sql);
        while ($row = sqlsrv_fetch_object($rs)) {
            $promotions[$row->MHCODE] = $row;
        }
    }

    return $promotions;
}

/**
 * Get all websites in Magento
 */
function _getWebsites(){
    global $websites, $mssql_stores;

    if (!count($websites)) {
        foreach (Mage::app()->getWebsites() as $website) {
            $websites[] = $website->getId();
            if (isset($mssql_stores[$website->getId()])){
                // enclose store id string
                array_walk($mssql_stores[$website->getId()], function(&$v){ $v = "'".$v."'";});
            }
        }
    }

    return $websites;
}

/**
 * Get total qty from ERP database by stores
 */
function _getErpQtyByStores($erp, $stores){
    if (!$erp->MHID || !is_array($stores) || !count($stores)) return 0;

    $sql = "
        SELECT SUM([SOLUONG]) QTY
        FROM [dbo].[KHOMATHANG]
        WHERE [KHOID] IN (" . implode(',', $stores) . ")
            AND [MHID] = '" . $erp->MHID ."'";

    $rs = ms_query($sql);
    $row = sqlsrv_fetch_object($rs);

    return $row->QTY > 0 ? (int)$row->QTY : 0;
}

function _updatePrices($product, $erp){
    if (!$product['sku'] || !$erp->MHCODE) return false;

    $connection = _getConnection('core_write');
    $priceAttributeId = _getAttributeId('price');
    $specialPriceAttributeId = _getAttributeId('special_price');
    $promotions = _getErpProductPromotion();

    $sql = "
        UPDATE " . _getTableName('catalog_product_entity_decimal') . " cped
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

    runlog(sprintf('INFO: PRICE SKU [%s], price=%s, special_price=%s', $erp->MHCODE, $price, $specialPrice ? $specialPrice : 'NULL'));

    return true;
}

function _updateStocks($product, $erp){
    global $mssql_stores;

    if (!$product['sku'] || !$erp->MHCODE) return false;

    $connection = _getConnection('core_write');
    $websites = _getWebsites();
    $logs = array();

    foreach ($websites as $website) {
        if (isset($mssql_stores[$website])) {
            $newQty = _getErpQtyByStores($erp, $mssql_stores[$website]);
            $isInStock = $newQty > 0 ? 1 : 0;
            $stockStatus = $isInStock;

            $sql = "
                UPDATE " . _getTableName('cataloginventory_stock_item') . " cisi," . _getTableName('cataloginventory_stock_status') . " ciss
                SET cisi.qty = ?, cisi.is_in_stock = ?, ciss.qty = ?, ciss.stock_status = ?
                WHERE cisi.product_id = ? AND ciss.website_id = ? AND cisi.product_id = ciss.product_id";

            $connection->query($sql, array($newQty, $isInStock, $newQty, $stockStatus, $product['entity_id'], $website));

            $logs[] = sprintf("%s=%s", $website, $newQty);
        }
    }

    runlog(sprintf('INFO: STOCK SKU [%s], %s', $erp->MHCODE, implode(', ', $logs)));

    return true;
}

function _getProductCollection(){
    $connection = _getConnection('core_read');
    $query = sprintf("SELECT * FROM %s", _getTableName('catalog_product_entity'));
    return $connection->fetchAll($query);
}

function _getErpProductBySku($sku){
    if (!$sku) return false;
    $query = "SELECT * FROM [dbo].[MATHANG] WHERE [MHCODE] = '{$sku}'";
    $rs = ms_query($query);
    return sqlsrv_fetch_object($rs);
}
/* end functions helper */

/* logic here */
require_once 'app/Mage.php';
Mage::app('admin', 'store');

$attributes     = array(); // store attribute_code => attribute_id
$entityTypes    = array(); // store entity_type_code => entity_type_id
$promotions     = array(); // store all products in current promotions
$websites       = array(); // store all websites in Magento

if (!function_exists('sqlsrv_connect')){
    runlog('ERROR: Microsoft SQL Server Driver not found', true);
}

$mssql_info = array('UID' => $mssql_user, 'PWD' => $mssql_pass, 'Database' => $mssql_db);
$mssql_conn = sqlsrv_connect($mssql_host, $mssql_info);
if (!$mssql_conn){
    runlog('ERROR: Microsoft SQL Server connection');
    print_r(sqlsrv_errors());
    die;
}else{
    runlog('INFO: Microsoft SQL Server connected');
}

$products = _getProductCollection();
if (!count($products)) runlog('ERROR: No product avaiable', 1);
else runlog(sprintf('INFO: Total products: %d', count($products)));

$i = 0;
$countProcessed = 0;
foreach ($products as $product){
    if ($i++ == 100) break;
    $row = _getErpProductBySku($product['sku']);
    if (!$row) continue;
    runlog('');
    _updatePrices($product, $row);
    _updateStocks($product, $row);
    $countProcessed++;
}

runlog('');
runlog(sprintf('INFO: Processed products: %d', $countProcessed));
/* end logic */