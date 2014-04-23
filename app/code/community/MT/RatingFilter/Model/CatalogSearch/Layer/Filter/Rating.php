<?php
/**
 * @category    MT
 * @package     MT_RatingFilter
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_RatingFilter_Model_CatalogSearch_Layer_Filter_Rating extends MT_RatingFilter_Model_Catalog_Layer_Filter_Rating{
    /**
     * @return MT_RatingFilter_Model_Resource_Catalog_Layer_Filter_Rating
     */
    protected function _getResource(){
        if (is_null($this->_resource)){
            $this->_resource = Mage::getResourceModel('ratingfilter/catalogSearch_layer_filter_rating');
        }
        return $this->_resource;
    }
}