<?php
/**
 * @category    MT
 * @package     MT_Erp
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
interface MT_Erp_Model_Adapter_Interface{
    public function getProductsPromotion($date=null, $storeId);
    public function getStockByStores($productId, $stores);
    public function getProductBySku($sku);
    public function getProductCount();
    public function getProducts($page, $limit);
    public function getCustomerByTelephone($phoneNumber);
    public function addCustomer($customer);
    public function close();
}