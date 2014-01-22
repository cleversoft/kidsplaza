<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Model_Layer extends Mage_CatalogSearch_Model_Layer {
	/**
	 * Get current layer product collection
	 *
	 * @return Mage_Catalog_Model_Resource_Eav_Resource_Product_Collection
	 */
	public function getProductCollection() {
		if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
			$collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
		} else {
			$collection = Mage::getResourceModel('mtsearch/solr_collection');
			$this->prepareProductCollection($collection);
			$collection->load();
			$this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
		}
		return $collection;
	}

	/**
	 * Get collection of all filterable attributes
	 *
	 * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute_Collection
	 */
	public function getFilterableAttributes() {
		/** @var $collection Mage_Catalog_Model_Resource_Product_Attribute_Collection */
		$collection = Mage::getResourceModel('catalog/product_attribute_collection');
		$collection
			->setItemObjectClass('catalog/resource_eav_attribute')
			->addStoreLabel(Mage::app()->getStore()->getId())
			->setOrder('position', 'ASC');
		$collection = $this->_prepareAttributeCollection($collection);
		$collection->load();

		return $collection;
	}
}