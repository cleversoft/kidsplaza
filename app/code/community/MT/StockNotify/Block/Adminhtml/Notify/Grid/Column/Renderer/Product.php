<?php
/**
 * @category    MT
 * @package     MT_StockNotify
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_StockNotify_Block_Adminhtml_Notify_Grid_Column_Renderer_Product extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
    public function _getValue(Varien_Object $row){
        $product = $row->getProduct();
        if (!$product) {
            $productId = $row->getData('product_id');
            if (!$productId) return '';
            $product = Mage::getModel('catalog/product')->load($productId, array('name'));
            if (!$product->getId()) return '';
        }
        return sprintf('<a href="%s" target="_blank">%s</a>',
            $this->getUrl('adminhtml/catalog_product/edit', array('id' => $productId, 'tab' => 'product_info_tabs_inventory')),
            $product->getName()
        );
    }
}