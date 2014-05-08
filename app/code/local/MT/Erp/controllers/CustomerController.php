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
        $customer = $service->getErpCustomer($phoneNumber);
        $data = array();
        if (is_array($customer)){
            $name               = isset($customer['customerName']) ? trim($customer['customerName']) : '';
            $data['firstname']  = strrpos($name, ' ') > 0 ? substr($name, 0, strrpos($name, ' ')) : '';
            $data['lastname']   = strrpos($name, ' ') > 0 ? substr($name, strrpos($name, ' ') + 1, strlen($name)) : $name;
            $sex                = isset($customer['customerGender']) ? trim($customer['customerGender']) : '';
            $data['gender']     = $sex == 'Nam' ? 1 : ($sex == 'Nữ' ? 2 : '');
            $data['email']      = isset($customer['customerEmail']) ? trim($customer['customerEmail']) : '';
        }
        $this->getResponse()->setHeader('Content-Type', 'applicaion/json');
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
    }
}