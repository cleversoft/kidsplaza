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
            $collection = Mage::getResourceModel('catalog/product_collection')
                ->addAttributeToFilter('brand', array('eq' => '3'));
            $this->prepareProductCollection($collection);
            $this->_productCollections[0] = $collection;
        }
        return $this->_productCollections[0];
    }
}