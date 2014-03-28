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
        return $stock->getIsInStock() ? $this->__('In stock (%d)', $stock->getQty()) : $this->__('Out of stock');
    }
}