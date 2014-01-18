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
            Mage::dispatchEvent('catalog_block_product_list_collection', array(
                'collection' => $collection
            ));
            $this->_productCollections[0] = $collection;
        }
        return $this->_productCollections[0];
    }

    protected function _getBestsellerCollection(){

    }

    protected function _getLastestCollection(){
        $resource = Mage::getSingleton('core/resource')->getConnection('core_read');
        $select = $resource->select()
            ->from('catalog_product_entity', array('entity_id', 'updated_at'))
            ->order('updated_at desc')
            ->limit(100);

        $result = $resource->query($select);
        $ids = array();
        while ($row = $result->fetch()){
            $ids[] = $row['entity_id'];
        }
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addIdFilter($ids);
        $this->prepareProductCollection($collection);
        unset($select, $result);
        return $collection;
    }
}