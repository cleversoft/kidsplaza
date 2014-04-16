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
        $host   = $this->getRequest()->getParam('host');
        $user   = $this->getRequest()->getParam('user');
        $pass   = $this->getRequest()->getParam('pass');
        $db     = $this->getRequest()->getParam('db');

        $data = array();

        if (!function_exists('sqlsrv_connect')){
            $data['msg'] = 'Microsoft SQL Server Driver not found';
            $data['error'] = 1;
        }else {
            $mssql_info = array('UID' => $user, 'PWD' => $pass, 'Database' => $db);
            $mssql_conn = sqlsrv_connect($host, $mssql_info);

            if (!$mssql_conn) {
                $data['msg'] = 'Microsoft SQL Server not found';
                $data['error'] = 1;
            } else {
                $data['msg'] = 'Microsoft SQL Server found';
                $data['error'] = 0;
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
    }
}