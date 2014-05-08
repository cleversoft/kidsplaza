<?php
/**
 * @category    MT
 * @package     MT_OneStepCheckout
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */

include_once 'Mage/Checkout/controllers/OnepageController.php';
class MT_OneStepCheckout_OrderController extends Mage_Checkout_OnepageController {
    /**
     * Validate ajax request and redirect on failure
     */
    protected function _expireAjax(){
        if (!$this->getOnepage()->getQuote()->hasItems() ||
            $this->getOnepage()->getQuote()->getHasError() ||
            $this->getOnepage()->getQuote()->getIsMultiShipping()) {
            $this->_ajaxRedirectResponse();
            return true;
        }
        return false;
    }

    /**
     * Save checkout billing address
     */
    public function saveAddressAction(){
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $billingData = $this->getRequest()->getPost('billing', array());
            $shippingData = $this->getRequest()->getPost('shipping', array());
            $customerBillingAddressId = $this->getRequest()->getPost('billing_address_id', 0);
            $customerShippingAddressId = $this->getRequest()->getPost('shipping_address_id', 0);

            $quote = $this->getOnepage()->getQuote();

            $result = array();
            if ($customerBillingAddressId != 0){
                $customerAddress = Mage::getModel('customer/address')->load($customerBillingAddressId);
                if ($customerAddress->getId()) {
                    if ($customerAddress->getCustomerId() != $quote->getCustomerId()) {
                        $result = array(
                            'error' => 1,
                            'message' => Mage::helper('checkout')->__('Customer Address is not valid.')
                        );
                    }else{
                        $billingAddress = $quote->getBillingAddress();
                        $billingAddress
                            ->importCustomerAddress($customerAddress)
                            ->setSaveInAddressBook(0);

                        if (isset($billingData['use_for_shipping'])){
                            $shippingAddress = $quote->getShippingAddress();
                            $shippingAddress
                                ->setSameAsBilling($billingData['use_for_shipping'])
                                ->setSaveInAddressBook(0);

                            if ($billingData['use_for_shipping'] == 1){
                                $shippingAddress
                                    ->importCustomerAddress($customerAddress)
                                    ->setCollectShippingRates(true);
                            }
                        }
                    }
                }
            }elseif ($customerShippingAddressId != 0){
                $customerAddress = Mage::getModel('customer/address')->load($customerShippingAddressId);
                if ($customerAddress->getId()) {
                    if ($customerAddress->getCustomerId() != $quote->getCustomerId()) {
                        $result = array(
                            'error' => 1,
                            'message' => Mage::helper('checkout')->__('Customer Address is not valid.')
                        );
                    }else{
                        $shippingAddress = $quote->getShippingAddress();
                        $shippingAddress
                            ->importCustomerAddress($customerAddress)
                            ->setSaveInAddressBook(0)
                            ->setSameAsBilling(0)
                            ->setCollectShippingRates(true);
                    }
                }
            }else{
                foreach ($shippingData as $k => $v){
                    if (is_array($v)){
                        if (!implode('', $v)) unset($shippingData[$k]);
                    }elseif (!$v) unset($shippingData[$k]);
                }

                foreach ($billingData as $k => $v){
                    if (is_array($v)){
                        if (!implode('', $v)) unset($billingData[$k]);
                    }elseif (!$v) unset($billingData[$k]);
                }

                if (!$quote->isVirtual()) {
                    if (count($billingData)) {
                        $billingAddress = $quote->getBillingAddress();

                        $billingAddress->addData($billingData);
                        $billingAddress->setCustomerAddressId(null);
                        $billingAddress->implodeStreetAddress();

                        $shippingAddress = $quote->getShippingAddress();
                        if (isset($billingData['use_for_shipping']) && $billingData['use_for_shipping'] == 1){
                            unset($billingData['address_id']);
                            $shippingAddress->addData($billingData);
                            $shippingAddress->setSameAsBilling(1);
                        }

                        $shippingAddress
                            ->implodeStreetAddress()
                            ->setCustomerAddressId(null)
                            ->setShippingMethod($shippingAddress->getShippingMethod())
                            ->setCollectShippingRates(true);

                    }elseif (count($shippingData)){
                        $shippingAddress = $quote->getShippingAddress();
                        $shippingAddress->addData($shippingData)
                            ->setSameAsBilling(0)
                            ->setCustomerAddressId(null)
                            ->implodeStreetAddress()
                            ->setShippingMethod($shippingAddress->getShippingMethod())
                            ->setCollectShippingRates(true);
                    }
                }
            }

            $quote->collectTotals()->save();

            if (!isset($result['error'])){
                $result['shippingMethod'] = $this->_getShippingMethodsHtml();
                $result['payment'] = $this->_getPaymentMethodsHtml();
                $result['review'] = $this->_getReviewHtml();
            }

            if ($result) $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Shipping method save action
     */
    public function saveShippingMethodAction(){
        if ($this->_expireAjax()) {
            return;
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            $result = $this->getOnepage()->saveShippingMethod($data);
            // $result will have error data if shipping method is empty
            if(!$result) {
                Mage::dispatchEvent('checkout_controller_onepage_save_shipping_method',
                    array('request'=>$this->getRequest(),
                        'quote'=>$this->getOnepage()->getQuote()));
                $this->getOnepage()->getQuote()->collectTotals();
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));

                $result['review'] = $this->_getReviewHtml();
            }
            $this->getOnepage()->getQuote()->collectTotals()->save();
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
        }
    }

    /**
     * Save payment ajax action
     * Sets either redirect or a JSON response
     */
    public function savePaymentAction(){
        if ($this->_expireAjax()) {
            return;
        }
        try {
            if (!$this->getRequest()->isPost()) {
                $this->_ajaxRedirectResponse();
                return;
            }

            // set payment to quote
            $result = array();
            $data = $this->getRequest()->getPost('payment', array());
            $result = $this->getOnepage()->savePayment($data);

            // get section and redirect data
            $redirectUrl = $this->getOnepage()->getQuote()->getPayment()->getCheckoutRedirectUrl();
            if (empty($result['error'])) {
                $result['review'] = $this->_getReviewHtml();
            }
            if ($redirectUrl) {
                $result['redirect'] = $redirectUrl;
            }
        } catch (Mage_Payment_Exception $e) {
            if ($e->getFields()) {
                $result['fields'] = $e->getFields();
            }
            $result['error'] = $e->getMessage();
        } catch (Mage_Core_Exception $e) {
            $result['error'] = $e->getMessage();
        } catch (Exception $e) {
            Mage::logException($e);
            $result['error'] = $this->__('Unable to set Payment Method.');
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _getShippingMethodsHtml(){
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_index');
        $layout->generateXml();
        $layout->generateBlocks();
        return $layout->getBlock('checkout.onepage.shipping_method')->toHtml();
    }

    protected function _getPaymentMethodsHtml(){
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_index');
        $layout->generateXml();
        $layout->generateBlocks();
        return $layout->getBlock('checkout.onepage.payment')->toHtml();
    }

    protected function _getReviewHtml(){
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_index');
        $layout->generateXml();
        $layout->generateBlocks();
        return $layout->getBlock('checkout.onepage.review')->toHtml();
    }

    public function submitAction(){
        if ($this->_expireAjax()) {
            return;
        }
        $data = $this->getRequest()->getPost();
        $result = array();

        if (isset($data['billing'])){
            if (isset($data['billing']['customer_password']) && $data['billing']['customer_password'] &&
                isset($data['billing']['confirm_password']) && $data['billing']['confirm_password']){
                $this->getOnepage()->getQuote()->setIsNewCustomer(true);
                $this->getOnepage()->saveCheckoutMethod(Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER);
            }
            $customerBillingAddressId = $this->getRequest()->getPost('billing_address_id', false);
            $result += $this->getOnepage()->saveBilling($data['billing'], $customerBillingAddressId);
        }
        if (isset($data['shipping']) && isset($data['billing']['use_for_shipping']) && $data['billing']['use_for_shipping'] == 0){
            $customerShippingAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            $result += $this->getOnepage()->saveShipping($data['shipping'], $customerShippingAddressId);
        }
        if (isset($data['shipping_method'])){
            $result += $this->getOnepage()->saveShippingMethod($data['shipping_method']);
        }
        if (isset($data['payment'])){
            $result += $this->getOnepage()->savePayment($data['payment']);
        }

        if (empty($result)){
            try {
                if ($requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds()) {
                    $postedAgreements = array_keys($this->getRequest()->getPost('agreement', array()));
                    if ($diff = array_diff($requiredAgreements, $postedAgreements)) {
                        $result['success'] = false;
                        $result['error'] = true;
                        $result['error_messages'] = Mage::helper('checkout')->__('Please agree to all the terms and conditions before placing the order.');
                        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
                        return;
                    }
                }

                /**
                 * when there is redirect to third party, we don't want to save order yet.
                 * we will save the order in return action.
                 */
                $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
                if (isset($redirectUrl)) {
                    $result['redirect'] = $redirectUrl;
                }else{
                    $this->getOnepage()->saveOrder();
                    $this->getOnepage()->getQuote()->save();

                    //newsletter
                    if (isset($data['billing']['newsletter']) && $data['billing']['newsletter'] == 1){
                        Mage::getModel('newsletter/subscriber')->subscribe($data['billing']['email']);
                    }

                    $result['success'] = true;
                    $result['error']   = false;
                }
            } catch (Mage_Payment_Model_Info_Exception $e) {
                $message = $e->getMessage();
                if( !empty($message) ) {
                    $result['error_messages'] = $message;
                }
                $result['payment'] = $this->_getPaymentMethodsHtml();
            } catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
                $result['success'] = false;
                $result['error'] = true;
                $result['error_messages'] = $e->getMessage();

                if ($updateSection = $this->getOnepage()->getCheckout()->getUpdateSection()) {
                    if (isset($this->_sectionUpdateFunctions[$updateSection])) {
                        $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                        $result[$updateSection] = $this->$updateSectionFunction();
                    }
                }
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::helper('checkout')->sendPaymentFailedEmail($this->getOnepage()->getQuote(), $e->getMessage());
                $result['success']  = false;
                $result['error']    = true;
                $result['error_messages'] = $this->__('There was an error processing your order. Please contact us or try again later.');
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}