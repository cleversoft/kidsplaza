<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Block_Layer extends Mage_CatalogSearch_Block_Layer {
	/**
	 * Initialize blocks names
	 */
	protected function _initBlocks() {
		parent::_initBlocks();
		if (Mage::helper('mtsearch')->usingSolrFrontend()){
			$this->_categoryBlockName			= 'mtsearch/layer_filter_category';
			$this->_priceFilterBlockName		= 'mtsearch/layer_filter_price';
			$this->_attributeFilterBlockName	= 'mtsearch/layer_filter_attribute';
		}
	}

	/**
	 * Get layer object
	 *
	 * @return $this
	 */
	public function getLayer() {
		if (Mage::helper('mtsearch')->usingSolrFrontend()){
			return Mage::getSingleton('mtsearch/layer');
		}else return Mage::getSingleton('catalogsearch/layer');
	}
}