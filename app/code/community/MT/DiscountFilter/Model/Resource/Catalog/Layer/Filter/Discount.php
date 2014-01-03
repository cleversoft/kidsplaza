<?php
/**
 * @category    MT
 * @package     MT_DiscountFilter
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_DiscountFilter_Model_Resource_Catalog_Layer_Filter_Discount extends Mage_Core_Model_Resource_Db_Abstract{
    protected function _construct(){
        $this->_init('catalogrule/rule_product', 'rule_product_id');
    }

    public function applyFilterToCollection($filter, $value){
        $session    = Mage::getSingleton('customer/session');
        $customerGroupId = $session->isLoggedIn() ? $session->getCustomer()->getGroupId() : 0;
        $collection = $filter->getLayer()->getProductCollection();
        $tableAlias = 'catalogrule_product_idx';
        $connection = $this->_getReadAdapter();

        $subSelect  = $connection->select()->distinct()
            ->from($this->getMainTable(), array('product_id', 'customer_group_id'));

        $conditions = array(
            "{$tableAlias}.product_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.customer_group_id = ?", $customerGroupId)
        );

        $collection->getSelect()->join(
            array($tableAlias => $subSelect),
            join(' AND ', $conditions),
            array()
        );

        return $this;
    }

    public function getCount($filter){
        $session    = Mage::getSingleton('customer/session');
        $customerGroupId = $session->isLoggedIn() ? $session->getCustomer()->getGroupId() : 0;
        $connection = $this->_getReadAdapter();
        $tableAlias = 'catalogrule_product_idx';

        /* @var $select Varien_Db_Select */
        $select = clone $filter->getLayer()->getProductCollection()->getSelect();
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $conditions = array(
            "{$tableAlias}.product_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.customer_group_id = ?", $customerGroupId)
        );

        $select->join(
            array($tableAlias => $this->getMainTable()),
            join(' AND ', $conditions),
            array('count' => new Zend_Db_Expr("COUNT({$tableAlias}.product_id)"))
        );

        return $connection->fetchRow($select);
    }
}