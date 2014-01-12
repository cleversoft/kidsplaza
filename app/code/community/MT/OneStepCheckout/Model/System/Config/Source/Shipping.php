<?php
/**
 * @category    MT
 * @package     MT_OneStepCheckout
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_OneStepCheckout_Model_System_Config_Source_Shipping {
    protected $_methods;

    public function toOptionArray(){
        if (!$this->_methods){
            $methods[] = array(
                'value' => '',
                'label' => Mage::helper('mtonestepcheckout')->__('-- Choose --')
            );
            $collection = Mage::getSingleton('shipping/config')->getActiveCarriers();
            foreach ($collection as $code => $item){
                if (!($title = Mage::getStoreConfig("carriers/$code/title"))){
                    $title = $code;
                }
                $methods[] = array(
                    'value' => $code,
                    'label' => $title
                );
            }
            $this->_methods = $methods;
        }
        return $this->_methods;
    }
}