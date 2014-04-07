<?php
/**
 * @category    MT
 * @package     MT_StockNotify
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_StockNotify_Block_Adminhtml_Notify_Grid_Column_Renderer_Customer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
    public function _getValue(Varien_Object $row){
        $value = $row->getData('customer_email');
        $customerId = $row->getData('customer_id');
        //$emailValidator = new Zend_Validate_EmailAddress();
        if ($customerId){
            $customer = Mage::getModel('customer/customer')->load($customerId);
            if ($customer->getId()){
                return sprintf('<a href="%s" target="_blank">%s</a>',
                    $this->getUrl('adminhtml/customer/edit', array('id' => $customer->getId())),
                    $value
                );
            }
        }
        return $value;
    }
}