<?php
/**
 * @category    MT
 * @package     MT_OneStepCheckout
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_OneStepCheckout_Helper_Data extends Mage_Core_Helper_Abstract{
    public function getEmail(){
        if ($this->isLoggedIn()){
            return $this->getCustomer()->getEmail();
        }
    }

    public function getName(){
        if ($this->isLoggedIn()){
            return $this->getCustomer()->getName();
        }
    }

    public function getTelephone(){
        if ($this->isLoggedIn()){
            return $this->getCustomer()->getTelephone();
        }
    }

    public function getCustomer(){
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    public function isLoggedIn(){
        $customer = $this->getCustomer();
        if ($customer->getId()) return true;
        else return false;
    }
}