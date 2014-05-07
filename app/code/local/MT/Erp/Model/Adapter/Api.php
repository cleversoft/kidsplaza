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
    protected $_productCollection;

    public function __construct($config=array()){
        $this->_url = isset($config[0]) ? $config[0] : null;

        return $this->_initConnection();
    }

    protected function query($uri='/', $params=array()){
        $client = new Zend_Http_Client($this->_url . $uri);

        if (count($params)) {
            $paramsJson = Mage::helper('core')->jsonEncode($params);
            $client->setRawData($paramsJson, 'application/json');
            $response = $client->request('POST');
        }else{
            $response = $client->request();
        }

        $data = $response->getBody();

        try{
            $data = Mage::helper('core')->jsonDecode($data);
        }catch (Exception $e){
            throw new Exception(Mage::helper('mterp')->__('API response error: %s', $e->getMessage()));
        }

        return $data;
    }

    protected function _initConnection(){
        $this->ping();

        return $this;
    }

    public function ping(){
        $uri = '?wsdl';
        if (file_get_contents($this->_url . $uri) === false){
            throw new Exception(Mage::helper('mterp')->__('API connection error'));
        }
    }

    public function getProductsPromotion($date=null, $storeId){
        $action = '/LoadProductByPromotionActive';
        $params = array(
            'Date'  => $date,
            'Khoid' => $storeId
        );
        $data = $this->query($action, $params);

        $output = array();
        foreach ($data as $item){
            $output[$item['productCode']] = $item;
        }

        return $output;
    }

    public function getPriceByStore($productId, $storeId){
        $price = 0;

        if ($this->_productCollection){
            $storeId = strtoupper($storeId);
            foreach ($this->_productCollection as $product){
                if ($product['productId'] == $productId){
                    if (isset($product['PriceAndQuantityInfo'])){
                        $infoArray = $product['PriceAndQuantityInfo'];
                        foreach ($infoArray as $info){
                            if (strtoupper($info['KHOID']) == $storeId){
                                $price = $info['PRICE'];
                            }
                        }
                    }
                }
            }
        }

        return $price;
    }

    public function getStockByStores($productId, $stores){
        $qty = 0;

        if ($this->_productCollection){
            foreach ($this->_productCollection as $product){
                if ($product['productId'] == $productId){
                    if (isset($product['PriceAndQuantityInfo'])){
                        $infoArray = $product['PriceAndQuantityInfo'];
                        foreach ($infoArray as $info){
                            if (in_array(strtoupper($info['KHOID']), $stores)){
                                $qty += (int)$info['QUANTITY'];
                            }
                        }
                    }
                }
            }
        }

        return $qty;
    }

    public function getProductBySku($sku){

    }

    public function getProductCount(){
        $action = '/GetCountProductActive';
        $data   = $this->query($action);

        if (isset($data['Counts'])){
            return (int)$data['Counts'];
        }

        return 0;
    }

    public function getProducts($page, $limit){
        $action = '/LoadProductListPaging';
        $params = array(
            'KHOID'     => null,
            'Page'      => $page,
            'PageLimit' => $limit
        );
        $data = $this->query($action, $params);
        $this->_productCollection = $data;

        $output = array();
        foreach ($data as $item){
            if (isset($item['PriceAndQuantityInfo'])){
                $infoArray = $item['PriceAndQuantityInfo'];
                foreach ($infoArray as $info){
                    if ($info['PRICE'] > 0){
                        $item['price'] = $info['PRICE'];
                    }
                }
                unset($item['PriceAndQuantityInfo']);
            }

            $output[] = $item;
        }

        return $output;
    }

    public function getCustomerByTelephone($phoneNumber){

    }

    public function addCustomer($customer){

    }

    public function close(){
        return true;
    }
}