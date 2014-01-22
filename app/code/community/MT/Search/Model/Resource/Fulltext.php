<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Model_Resource_Fulltext extends Mage_CatalogSearch_Model_Resource_Fulltext {
	/**
	 * Prepare Fulltext index value for product
	 *
	 * @param array $indexData
	 * @param array $productData
	 * @param int $storeId
	 * @return string
	 */
	protected function _prepareProductIndex($indexData, $productData, $storeId) {
		$index = array();

		foreach ($this->_getSearchableAttributes('static') as $attribute) {
			$attributeCode = $attribute->getAttributeCode();

			if (isset($productData[$attributeCode])) {
				$value = $this->_getAttributeValue($attribute->getId(), $productData[$attributeCode], $storeId);
				if ($value) {
					//For grouped products
					if (isset($index[$attributeCode])) {
						if (!is_array($index[$attributeCode])) {
							$index[$attributeCode] = array($index[$attributeCode]);
						}
						$index[$attributeCode][] = $value;
					}
					//For other types of products
					else {
						$index[$attributeCode] = $value;
					}
				}
			}
		}

		foreach ($indexData as $entityId => $attributeData) {
			foreach ($attributeData as $attributeId => $attributeValue) {
				$value = $this->_getAttributeValue($attributeId, $attributeValue, $storeId);
				if (!is_null($value) && $value !== false) {
					$attributeCode = $this->_getSearchableAttribute($attributeId)->getAttributeCode();

					if (isset($index[$attributeCode])) {
						$index[$attributeCode][$entityId] = $value;
					} else {
						$index[$attributeCode] = array($entityId => $value);
					}
				}
			}
		}

		if (!$this->_engine->allowAdvancedIndex()) {
			$product = $this->_getProductEmulator()
				->setId($productData['entity_id'])
				->setTypeId($productData['type_id'])
				->setStoreId($storeId);
			$typeInstance = $this->_getProductTypeInstance($productData['type_id']);
			if ($data = $typeInstance->getSearchableData($product)) {
				$index['options'] = $data;
			}
		}

		if (isset($productData['in_stock'])) {
			$index['in_stock'] = $productData['in_stock'];
		}

		if ($this->_engine) {
			return $this->_engine->prepareEntityIndex($index, $this->_separator);
		}

		return Mage::helper('catalogsearch')->prepareIndexdata($index, $this->_separator);
	}

	/**
	 * Retrieve attribute source value for search
	 *
	 * @param int $attributeId
	 * @param mixed $value
	 * @param int $storeId
	 * @return mixed
	 */
	protected function _getAttributeValue($attributeId, $value, $storeId) {
		$attribute = $this->_getSearchableAttribute($attributeId);
		if (!$attribute->getIsSearchable()) {
			if ($this->_engine->allowAdvancedIndex()) {
				if ($attribute->getAttributeCode() == 'visibility') {
					return $value;
				} elseif (!($attribute->getIsVisibleInAdvancedSearch()
					|| $attribute->getIsFilterable()
					|| $attribute->getIsFilterableInSearch()
					|| $attribute->getUsedForSortBy())
				) {
					return null;
				}
			} else {
				return null;
			}
		}

		if ($attribute->usesSource()) {
			//if ($this->_engine->allowAdvancedIndex()) {
				return $value;
			//}

			$attribute->setStoreId($storeId);
			$value = $attribute->getSource()->getOptionText($value);

			if (is_array($value)) {
				$value = implode($this->_separator, $value);
			} elseif (empty($value)) {
				$inputType = $attribute->getFrontend()->getInputType();
				if ($inputType == 'select' || $inputType == 'multiselect') {
					return null;
				}
			}
		} elseif ($attribute->getBackendType() == 'datetime') {
			$value = $this->_getStoreDate($storeId, $value);
		} else {
			$inputType = $attribute->getFrontend()->getInputType();
			if ($inputType == 'price') {
				$value = Mage::app()->getStore($storeId)->roundPrice($value);
			}
		}

		$value = preg_replace("#\s+#siu", ' ', trim(strip_tags($value)));

		return $value;
	}
}