<?php
/**
 * @category    MT
 * @package     MT_OneStepCheckout
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_OneStepCheckout_Model_Observer{
    public function checkoutCartSaveBefore($observer){
        $cart = $observer->getCart();

        $shipping = Mage::getStoreConfig('mtonestepcheckout/method/shipping');
        $countryCode = Mage::getStoreConfig('shipping/origin/country_id');
        if (!$shipping){
            $shippingMethods = Mage::getSingleton('shipping/config')->getActiveCarriers();
            foreach ($shippingMethods as $sCode => $method){
                $cart->getQuote()
                    ->getShippingAddress()
                    ->setCountryId($countryCode)
                    ->setShippingMethod($sCode .'_'. $sCode);
                break;
            }
        }else{
            $cart->getQuote()
                ->getShippingAddress()
                ->setCountryId($countryCode)
                ->setShippingMethod($shipping .'_'. $shipping);
        }

        $payment = Mage::getStoreConfig('mtonestepcheckout/method/payment');
        if (!$payment){
            $paymentMethods = Mage::getSingleton('payment/config')->getActiveMethods();
            foreach ($paymentMethods as $pCode => $method){
                $cart->getQuote()->getPayment()->setMethod($pCode);
                break;
            }
        }else{
            $cart->getQuote()->getPayment()->setMethod($payment);
        }
    }
}