<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_Model_Mysql4_Point extends Mage_Core_Model_Mysql4_Abstract{
    protected function _construct(){
        $this->_init('mtpoint/point', 'id');
    }

    public function loadByCustomer(MT_Point_Model_Point $point, $customer){
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from($this->getMainTable(), array('id'));
        if ($customer instanceof Mage_Customer_Model_Customer){
            $select->where('customer_id = ?', $customer->getId());
        }else if (is_numeric($customer)){
            $select->where('customer_id = ?', $customer);
        }
        $pointId = $adapter->fetchOne($select);
        if ($pointId){
            $this->load($point, $pointId);
        }else{
            $point->setData(array());
        }
        return $point;
    }

    protected function _afterDelete(Mage_Core_Model_Abstract $point){
        $adapter = $this->_getWriteAdapter();
        $adapter->delete(
            $this->getTable('mtpoint/history'),
            $adapter->quoteInto('point_id = ?', $point->getId())
        );
    }
}