<?php
/**
 * @category    MT
 * @package     MT_Collection
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Collection_Model_Layer extends Mage_Catalog_Model_Layer{
    public function getProductCollection(){
        if (!isset($this->_productCollections[0])){
            switch (Mage::registry('current_collection')){
                case 'bestseller':
                    $collection = $this->_getBestsellerCollection();
                    break;
                case 'lastest':
                default:
                    $collection = $this->_getLastestCollection();
                    break;
            }
            $this->_productCollections[0] = $collection;
        }
        return $this->_productCollections[0];
    }

    protected function _getBestsellerCollection(){

    }

    protected function _getLastestCollection(){
        $resource = Mage::getSingleton('core/resource')->getConnection('core_read');
        $select = $resource->select()
            ->from('catalog_product_entity', array('eid' => 'entity_id', 'eua' => 'updated_at'))
            ->order('eua desc')
            ->limit(100);

        $conditions = array('e2.eid = e.entity_id');
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->getSelect()->join(
            array('e2' => $select),
            join(' AND ', $conditions),
            array()
        );
        $this->prepareProductCollection($collection);
        unset($select, $result);
        return $collection;
    }
}