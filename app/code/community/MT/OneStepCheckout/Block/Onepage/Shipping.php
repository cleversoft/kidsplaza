<?php
/**
 * @category    MT
 * @package     MT_OneStepCheckout
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_OneStepCheckout_Block_Onepage_Shipping extends Mage_Checkout_Block_Onepage_Shipping{
    public function getAddressesHtmlSelect($type){
        if ($this->isCustomerLoggedIn()) {
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                );
            }
            $options[] = array(
                'value' => '0',
                'label' => Mage::helper('checkout')->__('New Address')
            );

            $addressId = (int)$this->getAddress()->getCustomerAddressId();
            if (empty($addressId)) {
                $address = $this->getCustomer()->getPrimaryShippingAddress();
                if ($address) {
                    $addressId = $address->getId();
                }
            }

            $select = $this->getLayout()->createBlock('core/html_select')
                ->setName($type.'_address_id')
                ->setId($type.'-address-select')
                ->setClass('address-select input-sm form-control')
                ->setExtraParams('onchange="'.$type.'.newAddress(this.value)"')
                ->setValue($addressId)
                ->setOptions($options);

            return $select->getHtml();
        }
        return '';
    }
}