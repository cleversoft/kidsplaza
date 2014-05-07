<?php
/**
 * @category    MT
 * @package     MT_Erp
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Erp_Adminhtml_ErpController extends Mage_Adminhtml_Controller_Action {
    /**
     * Test MSSQL Server Connection Action
     */
    public function pingAction() {
        $adapter = $this->getRequest()->getParam('adapter');
        $api    = $this->getRequest()->getParam('api');
        $host   = $this->getRequest()->getParam('host');
        $user   = $this->getRequest()->getParam('user');
        $pass   = $this->getRequest()->getParam('pass');
        $db     = $this->getRequest()->getParam('db');

        $data = array();
        switch ($adapter){
            case 'db':
                $data = $this->_testDBConnection($host, $user, $pass, $db);
                break;
            case 'api':
                $data = $this->_testAPIConnection($api);
                break;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
    }

    protected function _testDBConnection($host, $user, $pass, $db){
        $data = array();

        if (!function_exists('sqlsrv_connect')){
            $data['msg'] = Mage::helper('mterp')->__('Microsoft SQL Server Driver not found');
            $data['error'] = 1;
        }else {
            $info = array('UID' => $user, 'PWD' => $pass, 'Database' => $db);
            $conn = sqlsrv_connect($host, $info);

            if (!$conn) {
                $data['msg'] = Mage::helper('mterp')->__('Microsoft SQL Server not found');
                $data['error'] = 1;
            } else {
                $data['msg'] = Mage::helper('mterp')->__('Microsoft SQL Server found');
                $data['error'] = 0;
            }
        }

        return $data;
    }

    protected function _testAPIConnection($url){
        $data = array();

        if (!$url){
            $data['msg'] = Mage::helper('mterp')->__('URL not found');
            $data['error'] = 1;
        }elseif (file_get_contents($url) === false){
            $data['msg'] = Mage::helper('mterp')->__('Connection failed');
            $data['error'] = 1;
        }else{
            $data['msg'] = Mage::helper('mterp')->__('Connection successfully');
            $data['error'] = 0;
        }

        return $data;
    }
}