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
        $collection = $filter->getLayer()->getProductCollection();
        $tableAlias = 'catalogrule_product_idx';
        $connection = $this->getReadConnection();
        $subQuery   = $connection->select()->distinct()->from($this->getMainTable(), array('product_id'));
        $conditions = "{$tableAlias}.product_id = e.entity_id";

        $collection->getSelect()->join(
            array($tableAlias => $subQuery),
            $conditions,
            array()
        );

        return $this;
    }
}