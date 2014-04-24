<?php
/**
 * @category    MT
 * @package     MT_Erp
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Erp_Model_Adapter_Api implements MT_Erp_Model_Adapter_Interface{
    protected $_url;

    public function __construct($config=array()){
        $this->_url = isset($config[0]) ? $config[0] : null;
    }

    public function getProductsPromotion($date=null){

    }

    public function getStockByStores($productId, $stores){
        $action = 'GetAllListStore';
        $data = file_get_contents($this->_url.$action);
        Mage::log($data);
    }

    public function getProductBySku($sku){

    }

    public function getProductCount(){

    }

    public function getProducts($page, $limit){

    }

    public function getCustomerByTelephone($phoneNumber){

    }

    public function addCustomer($customer){

    }

    public function close(){
        return true;
    }
}