<?php
/**
 * @category    MT
 * @package     MT_Collection
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Collection_Block_Product_List extends Mage_Catalog_Block_Product_List{
    protected function _getProductCollection(){
        if (is_null($this->_productCollection)){
            $layer = $this->getLayer();
            $this->_productCollection = $layer->getProductCollection();
        }
        return $this->_productCollection;
    }

    public function getLayer(){
        return Mage::registry('current_layer');
    }
}