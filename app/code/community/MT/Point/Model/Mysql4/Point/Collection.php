<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_Model_Mysql4_Point_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract{
    protected function _construct(){
        parent::_construct();
        $this->_init('mtpoint/point');
    }

    public function addCustomerFilter($customer){
        if ($customer instanceof Mage_Customer_Model_Customer){
            $this->getSelect()->where('main_table.customer_id = ?', $customer->getId());
        }elseif (is_numeric($customer)){
            $this->getSelect()->where('main_table.customer_id = ?', $customer);
        }
        return $this;
    }
}