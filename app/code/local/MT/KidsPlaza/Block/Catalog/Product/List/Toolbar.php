<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Block_Catalog_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar{
    /**
     * Set collection to pager
     *
     * @param Varien_Data_Collection $collection
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;

        $this->_collection->setCurPage($this->getCurrentPage());

        // we need to set pagination only if passed value integer and more that 0
        $limit = (int)$this->getLimit();
        if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        if ($this->getCurrentOrder()) {
            if ($this->getCurrentOrder() == 'bestsell'){
                $connection = Mage::getSingleton('core/resource')->getConnection('core_read');

                $select = $connection->select()
                    ->from('sales_flat_order_item', array('product_id', 'count'=>'SUM(qty_ordered)'))
                    ->join(
                        'sales_flat_order',
                        'sales_flat_order_item.order_id = sales_flat_order.entity_id',
                        array()
                    )
                    ->where('sales_flat_order.status = ?', 'complete')
                    ->group('sales_flat_order_item.product_id')
                    ->order('count DESC');

                $this->_collection->getSelect()->joinLeft(
                    array('bestsell' => $select),
                    join(' AND ', array('bestsell.product_id = e.entity_id')),
                    array()
                );

                unset($connection, $select);
            }else{
                $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
            }
        }
        return $this;
    }
}