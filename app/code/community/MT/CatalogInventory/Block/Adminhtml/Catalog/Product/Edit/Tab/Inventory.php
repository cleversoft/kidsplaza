<?php
/**
 * @category    MT
 * @package     MT_CatalogInventory
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_CatalogInventory_Block_Adminhtml_Catalog_Product_Edit_Tab_Inventory
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory{

    public function getFieldValue($field){
        if ($this->getStockItem()){
            if ($field == 'qty'){
                $productId = $this->getProduct()->getId();
                if ($productId){
                    $storeId = (int)$this->getRequest()->getParam('store', 0);
                    if ($storeId){
                        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
                        $stockStatus = Mage::getSingleton('cataloginventory/stock_status');
                        $productsData = $stockStatus->getProductData($productId, $websiteId);
                        if (isset($productsData[$productId])){
                            return $productsData[$productId]['qty'];
                        }
                    }
                }
            }
            return $this->getStockItem()->getDataUsingMethod($field);
        }
        return Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_ITEM . $field);
    }
}