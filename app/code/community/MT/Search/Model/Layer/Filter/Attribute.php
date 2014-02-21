<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Model_Layer_Filter_Attribute extends MT_Filter_Model_Layer_Filter_Attribute {
	/**
	 * Retrieve resource instance
	 *
	 * @return $this
	 */
	protected function _getResource() {
		if (is_null($this->_resource)){
			$this->_resource = Mage::getResourceModel('mtsearch/layer_filter_attribute');
		}
		return $this->_resource;
	}

    /**
     * Check whether specified attribute can be used in LN
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute  $attribute
     * @return bool
     */
    protected function _getIsFilterableAttribute($attribute) {
        return $attribute->getIsFilterableInSearch();
    }
}