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
        $this->_init('catalog/product_index_price', 'entity_id');
    }

    /**
     * @param $filter Mage_Catalog_Block_Layer_View
     * @param $value mixed
     * @return $this
     */
    public function applyFilterToCollection($filter, $value){
        /* @var $session Mage_Customer_Model_Session */
        $session    = Mage::getSingleton('customer/session');
        $customerGroupId = $session->isLoggedIn() ? $session->getCustomer()->getGroupId() : 0;
        $websiteId = Mage::app()->getWebsite()->getId();
        $collection = $filter->getLayer()->getProductCollection();
        $tableAlias = 'catalog_product_price_idx';
        $connection = $this->_getReadAdapter();

        $subSelect  = $connection->select()->distinct()
            ->from($this->getMainTable(), array('entity_id'))
            ->where('price > final_price')
            ->where("customer_group_id = ?", $customerGroupId)
            ->where("website_id = ?", $websiteId);

        $conditions = array("{$tableAlias}.entity_id = e.entity_id");

        $collection->getSelect()->join(
            array($tableAlias => $subSelect),
            join(' AND ', $conditions),
            array()
        );

        return $this;
    }

    /**
     * @param $filter Mage_Catalog_Model_Layer_Filter_Abstract
     * @return array
     */
    public function getCount($filter){
        /* @var $session Mage_Customer_Model_Session */
        $session    = Mage::getSingleton('customer/session');
        $customerGroupId = $session->isLoggedIn() ? $session->getCustomer()->getGroupId() : 0;
        $websiteId = Mage::app()->getWebsite()->getId();
        $connection = $this->_getReadAdapter();
        $tableAlias = 'catalog_product_price_idx';

        /* @var $select Varien_Db_Select */
        $select = clone $filter->getLayer()->getProductCollection()->getSelect();
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.customer_group_id = ?", $customerGroupId),
            $connection->quoteInto("{$tableAlias}.website_id = ?", $websiteId),
            "{$tableAlias}.price > {$tableAlias}.final_price"
        );

        $select->join(
            array($tableAlias => $this->getMainTable()),
            join(' AND ', $conditions),
            array('count' => new Zend_Db_Expr("COUNT({$tableAlias}.entity_id)"))
        );

        return $connection->fetchRow($select);
    }
}