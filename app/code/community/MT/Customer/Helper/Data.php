<?php
/**
 * @category    MT
 * @package     MT_Customer
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Customer_Helper_Data extends Mage_Core_Helper_Abstract{
    public function loadByTelephone($phonenumber){
        if (!$phonenumber) return null;

        $customers = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect('phone_number')
            ->addAttributeToFilter('phone_number', array('eq' => $phonenumber));

        if ($customers->getSize()) return $customers->getFirstItem();
    }
}