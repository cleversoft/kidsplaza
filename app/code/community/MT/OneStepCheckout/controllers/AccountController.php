<?php
/**
 * @category    MT
 * @package     MT_OneStepCheckout
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
include_once 'Mage/Customer/controllers/AccountController.php';
class MT_OneStepCheckout_AccountController extends Mage_Customer_AccountController {
    /**
     * Login post action
     */
    public function loginPostAction() {
        $session = $this->_getSession();
        $out = array();

        if ($session->isLoggedIn()) return $this->getResponse()->setBody('');

        if ($this->getRequest()->isPost()){
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        $this->_welcomeCustomer($session->getCustomer(), true);
                    }
                    $out['success'] = 1;
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
                            $out['message'] = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s" target="_blank">Click here</a> to resend confirmation email.', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            $out['message'] = $e->getMessage();
                            break;
                        default:
                            $out['message'] = $e->getMessage();
                            break;
                    }
                } catch (Exception $e) {
                    $out['message'] = $this->__('Unkown Error');
                }
            } else {
                $out['message'] = $this->__('Login and password are required.');
            }
        }

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($out));
    }

    /**
     * Forgot customer password action
     */
    public function forgotPasswordPostAction(){
        $out = array();
        $login = $this->getRequest()->getPost('login');
        if ($login['email']) {
            if (!Zend_Validate::is($login['email'], 'EmailAddress')) {
                $out['message'] = $this->__('Invalid email address.');
            }

            /** @var $customer Mage_Customer_Model_Customer */
            $customer = $this->_getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($login['email']);

            if ($customer->getId()) {
                try {
                    $newResetPasswordLinkToken =  $this->_getHelper('customer')->generateResetPasswordLinkToken();
                    $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                    $customer->sendPasswordResetConfirmationEmail();
                } catch (Exception $exception) {
                    $out['message'] = $exception->getMessage();
                }
            }

            $out['success'] = 1;
            $out['forgot'] = 1;
            $out['message'] = $this->_getHelper('customer')->__(
                'If there is an account associated with %s you will receive an email with a link to reset your password.',
                $this->_getHelper('customer')->escapeHtml($login['email'])
            );
        } else {
            $out['message'] = $this->__('Please enter your email.');
        }

        return $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($out));
    }
}