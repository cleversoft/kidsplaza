<?php
/**
 * @category    MT
 * @package     MT_OneStepCheckout
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
include_once 'Mage/Checkout/controllers/CartController.php';

class MT_OneStepCheckout_CartController extends Mage_Checkout_CartController {
    /**
     * Delete shoping cart item action
     * Version: 1.7.0.2
     */
    public function deleteAction()
    {
        if (!$this->_isAjax()) return;

        $id = (int) $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->_getCart()->removeItem($id)->save();
                $out = array('error' => 0, 'msg' => $this->__('Cart updated.'), 'count' => $this->_getCart()->getItemsCount());
            } catch (Exception $e) {
                $out = array('error' => 1, 'msg' => $e->getMessage());
            }
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($out));
        }
    }

    /**
     * Update shopping cart data action
     * Version: 1.7.0.2
     */
    public function updatePostAction()
    {
        if (!$this->_isAjax()) return;

        $updateAction = (string)$this->getRequest()->getParam('update_cart_action');

        switch ($updateAction) {
            case 'empty_cart':
                $out = $this->_emptyShoppingCart();
                break;
            case 'update_qty':
                $out = $this->_updateShoppingCart();
                break;
            default:
                $out = $this->_updateShoppingCart();
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($out));
    }

    /**
     * Update customer's shopping cart
     * Version: 1.7.0.2
     */
    protected function _updateShoppingCart()
    {
        try {
            $cartData = $this->getRequest()->getParam('cart');
            if (is_array($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                $cart = $this->_getCart();
                if (!$cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }

                $cartData = $cart->suggestItemsQty($cartData);
                $cart->updateItems($cartData)->save();
            }
            $this->_getSession()->setCartWasUpdated(true);
            if ($cart->getQuote()->getHasError()){
                $messages = $cart->getQuote()->getMessages();
                $msg = array();
                foreach ($messages as $message) $msg[] = $message->getCode();
                $out = array('error' => 1, 'msg' => $msg);
            }else{
                $out = array('error' => 0, 'msg' => $this->__('Cart updated.'), 'count' => $cart->getItemsCount(), 'items' => $this->_getCartItemsHtml($cartData));
            }
        } catch (Mage_Core_Exception $e) {
            $out = array('error' => 1, 'msg' => $e->getMessage());
        } catch (Exception $e) {
            $out = array('error' => 1, 'msg' => $this->__('Cannot update shopping cart.'));
        }
        return $out;
    }

    /**
     * Empty customer's shopping cart
     * Version: 1.7.0.2
     */
    protected function _emptyShoppingCart()
    {
        try {
            $this->_getCart()->truncate()->save();
            $this->_getSession()->setCartWasUpdated(true);
            $out = array('error' => 0);
        } catch (Mage_Core_Exception $e) {
            $out = array('error' => 1, 'msg' => $e->getMessage());
        } catch (Exception $e) {
            $out = array('error' => 1, 'msg' => $this->__('Cannot update shopping cart.'));
        }
        return $out;
    }

    /**
     * Initialize coupon
     * Version 1.7.0.2
     */
    public function couponPostAction()
    {
        if (!$this->_isAjax()) return;

        $out = array();
        /**
         * No reason continue with empty shopping cart
         */
        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            $out['error'] = 1;
        }

        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
        $oldCouponCode = $this->_getQuote()->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            $out['error'] = 1;
        }

        try {
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                ->collectTotals()
                ->save();

            if (strlen($couponCode)) {
                if ($couponCode == $this->_getQuote()->getCouponCode()) {
                    $out = array(
                        'error' => 0,
                        'msg' => $this->__('Coupon code "%s" was applied.', Mage::helper('core')->escapeHtml($couponCode)),
                        'review' => $this->_getReviewHtml()
                    );
                } else {
                    $out = array(
                        'error' => 1,
                        'msg' => $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode))
                    );
                }
            } else {
                $out = array(
                    'error' => 0,
                    'msg' => $this->__('Coupon code was canceled.'),
                    'review' => $this->_getReviewHtml()
                );
            }

        } catch (Mage_Core_Exception $e) {
            $out = array(
                'error' => 1,
                'msg' => $e->getMessage()
            );
        } catch (Exception $e) {
            $out = array(
                'error' => 1,
                'msg' => $this->__('Cannot apply the coupon code.')
            );
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($out));
    }

    protected function _getReviewHtml(){
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_index');
        $layout->generateXml();
        $layout->generateBlocks();
        return $layout->getBlock('checkout.onepage.review')->toHtml();
    }

    protected function _isAjax(){
        return $this->getRequest()->isXmlHttpRequest();
    }

    protected function _getCartItemsHtml($data){
        /* @var $cart Mage_Checkout_Block_Cart */
        $cart = $this->getLayout()->createBlock('checkout/cart');
        $cart->chooseTemplate();
        $html = array();
        if ($items = $cart->getItems()){
            foreach ($items as $item){
                /* @var $item Mage_Sales_Model_Quote_Item */
                foreach ($data as $id => $qty){
                    if ($id == $item->getId()){
                        $html[$id] = $cart->getItemHtml($item);
                    }
                }
            }
        }
        return $html;
    }
}