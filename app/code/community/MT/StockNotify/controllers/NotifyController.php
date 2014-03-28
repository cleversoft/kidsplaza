<?php
/**
 * @category    MT
 * @package     MT_StockNotify
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_StockNotify_NotifyController extends Mage_Core_Controller_Front_Action{
    public function submitAction(){
        $response = array();
        if ($this->_validateFormKey()){
            $session = Mage::getSingleton('core/session');
            if ($session->getStockNotify()){
                $response = array('error' => 1, 'message' => $this->__('Stock Notified!'));
            }else {
                $productId = $this->getRequest()->getParam('product');
                if (!$productId) $response = array('error' => 1, 'message' => $this->__('Product Invalid!'));
                $product = Mage::getModel('catalog/product')->load($productId, array('entity_id'));
                if (!$product->getId()) $response = array('error' => 1, 'message' => $this->__('Product Invalid!'));
                if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                    /* @var $customer Mage_Customer_Model_Customer */
                    $customer = Mage::getSingleton('customer/session')->getCustomer();
                    $notify = Mage::getModel('mtstocknotify/notify');
                    $notify->setData('customer_email', $customer->getEmail());
                    $notify->setData('product_id', $productId);
                    $notify->setData('store_id', Mage::app()->getStore()->getId());
                    $notify->setData('created_at', time());
                    $notify->setData('status', 1);
                    try {
                        $notify->save();
                        $response = array('error' => 0, 'message' => $this->__('Save successful!'));
                        $session->addSuccess(Mage::helper('mtstocknotify')->__('Thank you for ordering.'));
                        $session->setStockNotify(true);
                    } catch (Exception $e) {
                        $response = array('error' => 1, 'message' => $this->__('Save error!'));
                    }
                } else {
                    $value = $this->getRequest()->getParam('value');
                    $emailValidator = new Zend_Validate_EmailAddress();
                    $numberValidator = new Zend_Validate_Digits();
                    $isValid = false;
                    if ($emailValidator->isValid($value)) {
                        $isValid = true;
                    } elseif ($numberValidator->isValid($value)) {
                        $isValid = true;
                    } else {
                        $response = array('error' => 1, 'message' => $this->__('Information Invalid!'));
                    }
                    if ($isValid) {
                        $notify = Mage::getModel('mtstocknotify/notify');
                        $notify->setData('customer_email', $value);
                        $notify->setData('product_id', $productId);
                        $notify->setData('store_id', Mage::app()->getStore()->getId());
                        $notify->setData('created_at', time());
                        $notify->setData('status', 1);
                        try {
                            $notify->save();
                            $response = array('error' => 0, 'message' => $this->__('Save successful!'));
                            $session->addSuccess(Mage::helper('mtstocknotify')->__('Thank you for ordering.'));
                            $session->setStockNotify(true);
                        } catch (Exception $e) {
                            $response = array('error' => 1, 'message' => $this->__('Save error!'));
                        }
                    }
                }
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }
}