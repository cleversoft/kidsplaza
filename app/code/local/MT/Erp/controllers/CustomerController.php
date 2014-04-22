<?php
/**
 * @category    MT
 * @package     MT_Erp
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Erp_CustomerController extends Mage_Core_Controller_Front_Action{
    public function queryAction(){
        if (!$this->_validateFormKey()) return;
        $phoneNumber = $this->getRequest()->getParam('value');
        if (!$phoneNumber) return;
        $service = Mage::getModel('mterp/observer');
        $service->setIsCron(false);
        $raw = $service->getErpCustomer($phoneNumber);
        $data = array();
        if ($raw){
            if (sqlsrv_fetch($raw)){
                $name = trim(sqlsrv_get_field($raw, 0, SQLSRV_PHPTYPE_STRING('UTF-8')));
                $data['firstname'] = strrpos($name, ' ') > 0 ? substr($name, 0, strrpos($name, ' ')) : '';
                $data['lastname'] = strrpos($name, ' ') > 0 ? substr($name, strrpos($name, ' ') + 1, strlen($name)) : $name;
                $data['gender'] = trim(sqlsrv_get_field($raw, 1, SQLSRV_PHPTYPE_STRING('UTF-8'))) == 'Nam' ? 1 : 2;
                $data['email'] = trim(sqlsrv_get_field($raw, 2, SQLSRV_PHPTYPE_STRING('UTF-8')));
            }
        }
        $this->getResponse()->setHeader('Content-Type', 'applicaion/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
    }
}