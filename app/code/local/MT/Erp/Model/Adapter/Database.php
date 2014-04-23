<?php
/**
 * @category    MT
 * @package     MT_Erp
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Erp_Model_Adapter_Database implements MT_Erp_Model_Adapter_Interface{
    protected $_connection;
    protected $_host;
    protected $_user;
    protected $_pass;
    protected $_db;

    public function __construct($config=array()){
        $this->_host    = isset($config[0]) ? $config[0] : '';
        $this->_user    = isset($config[1]) ? $config[1] : '';
        $this->_pass    = isset($config[2]) ? $config[2] : '';
        $this->_db      = isset($config[3]) ? $config[3] : '';

        return $this->_initConnection();
    }

    protected function _initConnection(){
        if (!function_exists('sqlsrv_connect')){
            throw new Exception(Mage::helper('mterp')->__('Microsoft SQL Server Driver not found'));
        }

        $info = array('UID' => $this->_user, 'PWD' => $this->_pass, 'Database' => $this->_db);
        $this->_connection = sqlsrv_connect($this->_host, $info);

        if (!$this->_connection){
            throw new Exception(Mage::helper('mterp')->__('Microsoft SQL Server not found'));
        }

        return $this;
    }

    public function query($query, $params=array()){
        if (!$this->_connection){
            throw new Exception(Mage::helper('mterp')->__('Microsoft SQL Server not found'));
        }

        $result = sqlsrv_query($this->_connection, $query, $params);
        if (!$result) {
            Mage::log(sqlsrv_errors());
            throw new Exception(Mage::helper('mterp')->__("Microsoft SQL Server query:\n\t%s", $query));
        }

        return $result;
    }

    public function getProductsPromotion($date=null){
        if (!$this->_connection){
            throw new Exception(Mage::helper('mterp')->__('Microsoft SQL Server not found'));
        }

        $products = array();
        $sql = "
            SELECT km.[MHID], mh.[MHCODE], km.[GIABANLE], km.[GIAKM]
            FROM [dbo].[KHUYENMAICT] AS km
            LEFT JOIN [dbo].[MATHANG] AS mh ON km.[MHID] = mh.[MHID]
            WHERE km.[KMID] IN (
                SELECT [KMID]
                FROM [dbo].[KHUYENMAI]
                WHERE (GETDATE() BETWEEN [TUNGAY] AND [DENNGAY]) AND [APPLYTOALLKH] = 1)";

        $result = $this->query($sql);
        if (!$result) return $products;

        while (sqlsrv_fetch($result)){
            $id = sqlsrv_get_field($result, 0, SQLSRV_PHPTYPE_STRING('UTF-8'));
            $code = sqlsrv_get_field($result, 1, SQLSRV_PHPTYPE_STRING('UTF-8'));

            $products[$code] = array(
                'productId'     => $id,
                'productCode'   => $code,
                'price'         => sqlsrv_get_field($result, 2, SQLSRV_PHPTYPE_STRING('UTF-8')),
                'specialPrice'  => sqlsrv_get_field($result, 3, SQLSRV_PHPTYPE_STRING('UTF-8'))
            );
        }

        return $products;
    }

    public function getStockByStores($productId, $stores=array()){
        if (!$this->_connection){
            throw new Exception(Mage::helper('mterp')->__('Microsoft SQL Server not found'));
        }

        if (!$productId || !count($stores)) return null;

        //enclose store id string
        array_walk($stores, function(&$store){ $store = "'".$store."'"; });

        $sql = sprintf("
            SELECT SUM([SOLUONG]) AS [QTY]
            FROM [dbo].[KHOMATHANG]
            WHERE [KHOID] IN (%s) AND [MHID] = ?", implode(',', $stores));

        $result = $this->query($sql, array(
            array($productId, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8'))
        ));
        if (!$result) return null;

        $row = sqlsrv_fetch_object($result);
        return $row->QTY > 0 ? (int)$row->QTY : 0;
    }

    public function getProductBySku($sku){
        if (!$this->_connection){
            throw new Exception(Mage::helper('mterp')->__('Microsoft SQL Server not found'));
        }

        if (!$sku) return null;

        $sql = "SELECT [MHID],[GIABANLE] FROM [dbo].[MATHANG] WHERE [MHCODE] = ?";

        $result = $this->query($sql, array(
            array($sku, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8'))
        ));
        if (!$result) return null;
        sqlsrv_fetch($result);

        return array(
            'productCode'   => $sku,
            'productId'     => sqlsrv_get_field($result, 0, SQLSRV_PHPTYPE_STRING('UTF-8')),
            'price'         => sqlsrv_get_field($result, 1, SQLSRV_PHPTYPE_STRING('UTF-8'))
        );
    }

    public function getProductCount(){
        if (!$this->_connection){
            throw new Exception(Mage::helper('mterp')->__('Microsoft SQL Server not found'));
        }

        $sql = "SELECT COUNT(*) AS total FROM [dbo].[MATHANG]";
        $result = $this->query($sql);
        if (!$result) return 0;

        sqlsrv_fetch($result);
        return (int)sqlsrv_get_field($result, 0);
    }

    public function getProducts($page, $limit){
        if (!$this->_connection){
            throw new Exception(Mage::helper('mterp')->__('Microsoft SQL Server not found'));
        }

        $page = $page > 0 ? $page - 1 : 1;
        $limit = $limit > 0 ? $limit : 100;
        $products = array();

        $sql = "
            SELECT *
            FROM (SELECT Row_Number() OVER (ORDER BY [MHID]) AS [ROWINDEX],[MHID],[MHCODE],[MHTEN],[SOLUONG],[GIABANLE] FROM [dbo].[MATHANG]) AS SUB
            WHERE SUB.ROWINDEX > ? AND SUB.ROWINDEX <= ?";

        $result = $this->query($sql, array(
            array($limit*$page, SQLSRV_PARAM_IN),
            array($limit*$page + $limit, SQLSRV_PARAM_IN)
        ));

        if (!$result) return $products;

        while (sqlsrv_fetch($result)){
            $products[] = array(
                'productId'     => sqlsrv_get_field($result, 1, SQLSRV_PHPTYPE_STRING('UTF-8')),
                'productCode'   => sqlsrv_get_field($result, 2, SQLSRV_PHPTYPE_STRING('UTF-8')),
                'productName'   => sqlsrv_get_field($result, 3, SQLSRV_PHPTYPE_STRING('UTF-8')),
                'qty'           => sqlsrv_get_field($result, 4, SQLSRV_PHPTYPE_STRING('UTF-8')),
                'price'         => sqlsrv_get_field($result, 5, SQLSRV_PHPTYPE_STRING('UTF-8'))
            );
        }

        return $products;
    }

    public function getCustomerByTelephone($phoneNumber){
        if (!$phoneNumber) return null;

        if (!$this->_connection){
            throw new Exception(Mage::helper('mterp')->__('Microsoft SQL Server not found'));
        }

        $sql = "SELECT TOP 1 [KHTEN],[GIOITINH],[EMAIL],[DIACHI] FROM [dbo].[KHACHHANG] WHERE [DIENTHOAI] = ? OR [DIDONG] = ?";
        $result = $this->query($sql, array(
            array($phoneNumber, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8')),
            array($phoneNumber, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8'))
        ));
        if (!$result) return null;
        sqlsrv_fetch($result);

        return array(
            'customerName'      => sqlsrv_get_field($result, 0, SQLSRV_PHPTYPE_STRING('UTF-8')),
            'customerGender'    => sqlsrv_get_field($result, 1, SQLSRV_PHPTYPE_STRING('UTF-8')),
            'customerEmail'     => sqlsrv_get_field($result, 2, SQLSRV_PHPTYPE_STRING('UTF-8')),
            'customerAddress'   => sqlsrv_get_field($result, 3, SQLSRV_PHPTYPE_STRING('UTF-8'))
        );
    }

    public function addCustomer($customer){
        if (!$customer instanceof Mage_Customer_Model_Customer) return;

        if (!$this->_connection){
            throw new Exception(Mage::helper('mterp')->__('Microsoft SQL Server not found'));
        }

        $sql = "
            INSERT INTO [dbo].[KHACHHANG] ([KHID],[KHCODE],[KHCID],[KHNID],[KHTEN],[GIOITINH],[DIENTHOAI],[DIDONG],[EMAIL],[NGAYTAO],[CREATEDDATE])
            VALUES (NEWID(), ?, ?, ?, ?, ?, ?, ?, ?, CONVERT(date, GETDATE()), GETDATE())
        ";

        $params = array(
            array($customer->getPhoneNumber(), SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8')),
            array(Mage::getStoreConfig('kidsplaza/erp/khcid'), SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8')),
            array(Mage::getStoreConfig('kidsplaza/erp/khnid'), SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8')),
            array($customer->getName(), SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8')),
            array($customer->getGender() ? ($customer->getGender() == 1 ? "Nam" : "Nữ") : null, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8')),
            array($customer->getPhoneNumber(), SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8')),
            array($customer->getPhoneNumber(), SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8')),
            array($customer->getEmail(), SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STRING('UTF-8'))
        );

        return $this->query($sql, $params);
    }

    public function close(){
        if ($this->_connection) sqlsrv_close($this->_connection);
    }
}