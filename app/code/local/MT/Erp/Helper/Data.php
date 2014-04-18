<?php
/**
 * @category    MT
 * @package     MT_Erp
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Erp_Helper_Data extends Mage_Core_Helper_Abstract{
    protected $_MSSQLConnection;

    public function getConnection(){
        if ($this->_MSSQLConnection){
            return $this->_MSSQLConnection;
        }

        if (!function_exists('sqlsrv_connect')){
            return null;
        }

        $mssql_host = Mage::getStoreConfig('kidsplaza/erp/host');
        $mssql_user = Mage::getStoreConfig('kidsplaza/erp/user');
        $mssql_pass = Mage::getStoreConfig('kidsplaza/erp/pass');
        $mssql_db   = Mage::getStoreConfig('kidsplaza/erp/db');

        $mssql_info = array('UID' => $mssql_user, 'PWD' => $mssql_pass, 'Database' => $mssql_db);
        $this->_MSSQLConnection = sqlsrv_connect($mssql_host, $mssql_info);

        if (!$this->_MSSQLConnection){
            return null;
        }

        return $this->_MSSQLConnection;
    }
}