<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Model_Layer_Filter_Attribute extends Mage_CatalogSearch_Model_Layer_Filter_Attribute {
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
	 * Apply attribute option filter to product collection
	 *
	 * @param   Zend_Controller_Request_Abstract $request
	 * @param   Varien_Object $filterBlock
	 * @return  $this
	 */
	public function apply(Zend_Controller_Request_Abstract $request, $filterBlock) {
		$filter = $request->getParam($this->_requestVar);
		if (is_array($filter)) {
			return $this;
		}
		$text = $this->_getOptionText($filter);
		if (!is_null($filter) && strlen($text)) {
			$this->_getResource()->applyFilterToCollection($this, $filter);
			$this->getLayer()->getState()->addFilter($this->_createItem($text, $filter));
			$this->_items = array();
		}
		return $this;
	}
}