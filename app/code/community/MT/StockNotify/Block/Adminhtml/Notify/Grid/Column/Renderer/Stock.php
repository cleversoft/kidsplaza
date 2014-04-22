<?php
/**
 * @category    MT
 * @package     MT_StockNotify
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_StockNotify_Block_Adminhtml_Notify_Grid_Column_Renderer_Stock extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
    public function _getValue(Varien_Object $row){
        $product = $row->getProduct();
        if (!$product) {
            $productId = $row->getData('product_id');
            if (!$productId) return '';
            $product = Mage::getModel('catalog/product')->load($productId, array('name'));
            if (!$product->getId()) return '';
        }
        $stock = $product->getStockItem();
        if (!$stock) return '';
        $stockStatus = Mage::getSingleton('cataloginventory/stock_status');
        $productsData = $stockStatus->getProductData($product->getId(), $product->getStore()->getWebsiteId());
        return isset($productsData[$product->getId()]) ? $this->__('In stock (%d)', $productsData[$product->getId()]['qty']) : $this->__('Out of stock');
    }
}