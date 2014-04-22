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
    protected $_stockStatus = array();

    public function getFieldValue($field){
        if ($this->getStockItem()){
            $productId = $this->getProduct()->getId();
            $storeId = (int)$this->getRequest()->getParam('store', 0);
            if ($productId && $storeId){
                if (!isset($this->_stockStatus[$productId])){
                    $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
                    $stockStatus = Mage::getSingleton('cataloginventory/stock_status');
                    $this->_stockStatus = $stockStatus->getProductData($productId, $websiteId);
                }
                if (isset($this->_stockStatus[$productId])){
                    switch ($field){
                        case 'qty':
                            return $this->_stockStatus[$productId]['qty'];
                            break;
                        case 'is_in_stock':
                            return $this->_stockStatus[$productId]['stock_status'];
                            break;
                    }
                }
            }
            return $this->getStockItem()->getDataUsingMethod($field);
        }
        return Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_ITEM . $field);
    }
}