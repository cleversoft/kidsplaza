<?php
/**
 * @category    MT
 * @package     MT_DiscountFilter
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_DiscountFilter_Model_CatalogSearch_Layer_Filter_Discount extends MT_DiscountFilter_Model_Catalog_Layer_Filter_Discount{
    /**
     * @return MT_DiscountFilter_Model_Resource_Catalog_Layer_Filter_Discount
     */
    protected function _getResource(){
        if (is_null($this->_resource)){
            $this->_resource = Mage::getResourceModel('discountfilter/catalogSearch_layer_filter_discount');
        }
        return $this->_resource;
    }
}