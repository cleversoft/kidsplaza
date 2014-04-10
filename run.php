<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */

require_once 'app/Mage.php';
Mage::app('admin', 'store');

function _getConnection($type = 'core_read'){
    return Mage::getSingleton('core/resource')->getConnection($type);
}

function _getTableName($tableName){
    return Mage::getSingleton('core/resource')->getTableName($tableName);
}

function _getAttributeId($attribute_code){
    if (!$attribute_code) return null;
    $connection = $this->_getConnection('core_read');
    $sql = "SELECT attribute_id
                FROM " . $this->_getTableName('eav_attribute') . "
                WHERE entity_type_id = ? AND attribute_code = ?";
    $entity_type_id = $this->_getEntityTypeId();
    return $connection->fetchOne($sql, array($entity_type_id, $attribute_code));
}

function _getEntityTypeId($entity_type_code){
    if (!$entity_type_code) return null;
    $connection = $this->_getConnection('core_read');
    $sql = "SELECT entity_type_id FROM " . $this->_getTableName('eav_entity_type') . " WHERE entity_type_code = ?";
    return $connection->fetchOne($sql, array($entity_type_code));
}

function _getIdFromSku($sku){
    if (!$sku) return null;
    $connection = $this->_getConnection('core_read');
    $sql = "SELECT entity_id FROM " . $this->_getTableName('catalog_product_entity') . " WHERE sku = ?";
    return $connection->fetchOne($sql, array($sku));
}

function _checkIfSkuExists($sku){
    if (!$sku) return false;
    $connection = $this->_getConnection('core_read');
    $sql = "SELECT COUNT(*) AS count FROM " . $this->_getTableName('catalog_product_entity') . " WHERE sku = ?";
    $count = $connection->fetchOne($sql, array($sku));
    if ($count > 0){
        return true;
    }else{
        return false;
    }
}

function _updatePrices($data){
    $connection     = $this->_getConnection('core_write');
    $sku            = $data[0];
    $newPrice       = $data[1];
    $productId      = $this->_getIdFromSku($sku);
    $attributeId    = $this->_getAttributeId('price');

    $sql = "UPDATE " . $this->_getTableName('catalog_product_entity_decimal') . " cped
                SET cped.value = ?
                WHERE cped.attribute_id = ? AND cped.entity_id = ?";
    $connection->query($sql, array($newPrice, $attributeId, $productId));
}

function _updateStocks($data){
    $connection     = $this->_getConnection('core_write');
    $sku            = $data[0];
    $newQty         = (int)$data[1];
    $productId      = $this->_getIdFromSku($sku);
    $isInStock      = $newQty > 0 ? 1 : 0;
    $stockStatus    = $newQty > 0 ? 1 : 0;

    $sql = "UPDATE " . $this->_getTableName('cataloginventory_stock_item') . " csi,
               " . $this->_getTableName('cataloginventory_stock_status') . " css
               SET csi.qty = ?, csi.is_in_stock = ?, css.qty = ?, css.stock_status = ?
               WHERE csi.product_id = ? AND csi.product_id = css.product_id";
    $connection->query($sql, array($newQty, $isInStock, $newQty, $stockStatus, $productId));
}

