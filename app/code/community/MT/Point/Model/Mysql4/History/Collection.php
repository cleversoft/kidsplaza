<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_Model_Mysql4_History_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract{
    protected function _construct(){
        parent::_construct();
        $this->_init('mtpoint/history');
    }

    protected function _joinPointTable(){
        $this->getSelect()->join(
            array('point_table' => $this->getTable('mtpoint/point')),
            'point_table.id = main_table.point_id',
            array('customer_id')
        );
    }

    public function addCustomerFilter($customer){
        if ($customer instanceof Mage_Customer_Model_Customer){
            $this->_joinPointTable();
            $this->getSelect()->where('point_table.customer_id = ?', $customer->getId());
        }elseif (is_numeric($customer)){
            $this->_joinPointTable();
            $this->getSelect()->where('point_table.customer_id = ?', $customer);
        }
        return $this;
    }
}