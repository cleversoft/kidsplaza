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

        try {
            if (count($params)) {
                $paramsJson = Mage::helper('core')->jsonEncode($params);
                $client->setRawData($paramsJson, 'application/json');
                $response = $client->request('POST');
            } else {
                $response = $client->request();
            }

            $data = $response->getBody();
        }catch (Exception $e){
            $data = '';
        }

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

        if ($this->_productCollection && isset($this->_productCollection[$productId])){
            $infoArray = $this->_productCollection[$productId];
            if (!is_array($infoArray)) return $price;

            foreach ($infoArray as $info){
                if (strtoupper($info['KHOID']) == $storeId){
                    $price = $info['PRICE'];
                    break;
                }
            }
        }

        return $price;
    }

    public function getStockByStores($productId, $stores){
        $qty = 0;

        if ($this->_productCollection && isset($this->_productCollection[$productId])){
            $infoArray = $this->_productCollection[$productId];
            if (!is_array($infoArray)) return $qty;

            foreach ($infoArray as $info){
                if (in_array(strtoupper($info['KHOID']), $stores)){
                    $qty += (int)$info['QUANTITY'];
                }
            }
        }

        return $qty;
    }

    public function getProductBySku($sku){

    }

    public function getCustomerCount(){
        $action = '/GetCountAllCustomer';
        $data   = $this->query($action);

        if (isset($data['Counts'])){
            return (int)$data['Counts'];
        }

        return 0;
    }

    public function getProductCount(){
        $action = '/GetCountProductActive';
        $data   = $this->query($action);

        if (isset($data['Counts'])){
            return (int)$data['Counts'];
        }

        return 0;
    }

    public function getCustomers($page, $limit){
        $action = '/LoadAllCustomerListPaging';
        $params = array(
            'Page'      => $page,
            'PageLimit' => $limit
        );
        $data = $this->query($action, $params);

        return $data;
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
                $this->_productCollection[$item['productId']] = $item['PriceAndQuantityInfo'];
                unset($item['PriceAndQuantityInfo']);
            }

            $output[] = $item;
        }

        return $output;
    }

    public function getCustomerByTelephone($phoneNumber){
        $action = '/GetCustomerByMobile?mobile=' . $phoneNumber;
        $data = $this->query($action);

        $output = array();
        if ($data){
            $output['customerName']     = isset($data['name']) ? $data['name'] : '';
            $output['customerGender']   = isset($data['sex']) ? $data['sex'] : '';
            $output['customerEmail']    = isset($data['email']) ? $data['email'] : '';
        }

        return $output;
    }

    public function addCustomer($customer){
        $checkCustomer = $this->getCustomerByTelephone($customer->getPhoneNumber());
        if (isset($checkCustomer['customerName'])) return;

        $action = '/AddCustomer';
        $data = array(
            array(
                'type'      => $customer->getGroupId() ? $customer->getGroupId() : 1,
                'code'      => $customer->getPhoneNumber(),
                'email'     => $customer->getEmail(),
                'mobile'    => $customer->getPhoneNumber(),
                'name'      => $customer->getName(),
                'sex'       => $customer->getGender() == 1 ? 'Nam' : ($customer->getGender() == 2 ? 'Nữ' : null)
            )
        );
        $this->query($action, $data);
    }

    public function close(){
        return true;
    }

    public function getAllStores(){
        $action = '/GetAllListStore';
        $data = $this->query($action);
        return $data;
    }
}