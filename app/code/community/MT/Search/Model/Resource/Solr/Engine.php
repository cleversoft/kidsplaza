<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Model_Resource_Solr_Engine {
	protected $_adapter;
	protected $_attributes;
	protected $_attributeOptions;

	/**
	 * Get Solr Adapter Client
	 *
	 * @return Apache_Solr_Service
	 */
	public function getAdapter() {
		if (!$this->_adapter){
			$this->_adapter = Mage::getModel('mtsearch/service');
		}
		return $this->_adapter;
	}

	/**
	 * Define if engine is avaliable
	 *
	 * @return bool
	 */
	public function test() {
		return $this->getAdapter()->ping();
	}

	/**
	 * Add entity data to Solr data
	 *
	 * @param int $entityId
	 * @param int $storeId
	 * @param array $index
	 * @param string $entity 'product'|'cms'
	 * @return $this
	 */
	public function saveEntityIndex($entityId, $storeId, $index, $entity = 'product') {
		$this->saveEntityIndexes($storeId, array($entityId => $index), $entity);
		return $this;
	}

	/**
	 * Multi add entities data to Solr data
	 *
	 * @param int $storeId
	 * @param array $entityIndexes
	 * @param string $entity 'product'|'cms'
	 * @return $this
	 */
	public function saveEntityIndexes($storeId, $entityIndexes, $entity = 'product') {
		$storeId = (int) $storeId;
		$documents = array();

		foreach ($entityIndexes as $entityId => $index) {
			$documents[] = $this->_prepareDocumentData($storeId, $entityId, $index);
		}

		$this->getAdapter()->addDocuments($documents);
		$this->getAdapter()->commit();
		$this->getAdapter()->optimize();

		return $this;
	}

	/**
	 * Prepare Solr document data
	 *
	 * @param int $storeId
	 * @param int $entityId
	 * @param array $data
	 * @return MT_Search_Model_Document
	 */
	protected function _prepareDocumentData($storeId, $entityId, $data) {
		$document = Mage::getModel('mtsearch/document');
		
		$fulltext = array();
		foreach ($data as $attr => $value){
			if ($attr == 'price') continue;
			elseif ($attr == 'visibility') $document->addField($attr, $value[$entityId]);
			elseif ($attr == 'status') $document->addField($attr, $value[$entityId]);
			elseif ($attr == 'in_stock') $document->addField($attr, $value);
			else{
				$valueCalculated = $this->_calculateAttributeValue($storeId, $attr, $value);
				$fulltext[] = $valueCalculated;
				
				$document->addField('attr_' . $attr, $valueCalculated);
				
				$filterValue = $this->_calculateAttributeValue($storeId, $attr, $value, false);
				if ($filterValue){
					if (is_array($filterValue)){
						foreach ($filterValue as $fv){
							$document->addField('attr_' . $attr . '_value', $fv);
						}
					}else{
						$document->addField('attr_' . $attr . '_value', $filterValue);
					}
				}
			}
		}

		// add image field
		$imgSize = Mage::getStoreConfig('mtsearch/utils/img', $storeId);
		if (!$imgSize){
			$imgSizeH = $imgSizeW = 135;
		}else{
			if (!is_numeric($imgSize)){
				list($imgSizeW, $imgSizeH) = explode('x', $imgSize);
				if (!is_numeric($imgSizeW) || !is_numeric($imgSizeH)){
					$imgSizeH = $imgSizeW = 135;
				}
			}else{
				$imgSizeH = $imgSizeW = $imgSize;
			}
		}
		$productModel = Mage::getModel('catalog/product')->load($entityId, array('image'));
		if ($productModel->getData('image') && $productModel->getData('image') != 'no_selection'){
			try{
				$img = (string) Mage::helper('catalog/image')->init($productModel, 'small_image')->resize($imgSizeW, $imgSizeH);
				$document->addField('attr_img', $img);
			}catch(Exception $e){}
		}

        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');

		// add category_ids field
		$select = $connection->select()
			->from(
				array($resource->getTableName('catalog/category_product_index')),
				array('category_id', 'product_id', 'store_id'))
			->where('store_id = ?', $storeId)
			->where('product_id = ?', $entityId);

		foreach ($connection->fetchAll($select) as $row){
			$document->addField('category_ids', $row['category_id']);
		}
        unset($select);

		// add price fields
		$select = $connection->select()
			->from(
				array($resource->getTableName('catalog/product_index_price')),
				array('entity_id', 'customer_group_id', 'website_id', 'price', 'final_price'))
			->where('entity_id = ?', $entityId);

		foreach ($connection->fetchAll($select) as $row){
			$document->addField(
				sprintf('price_%d_%d_f', $row['website_id'], $row['customer_group_id']),
				$row['final_price']
            );
            // for discount filter
            $document->addField(
                sprintf('price_%d_%d_b', $row['website_id'], $row['customer_group_id']),
                $row['price'] > $row['final_price'] ? 1 : 0
            );
		}
        unset($select);

        // add rating field
        $select = $connection->select()
            ->from(
                array($resource->getTableName('rating/rating_vote_aggregated')),
                array('entity_pk_value', 'percent_approved', 'store_id'))
            ->where('entity_pk_value = ?', $entityId)
            ->where('store_id = ?', 0);

        foreach ($connection->fetchAll($select) as $row){
            $document->addField('rating_i', $row['percent_approved']);
        }
        unset($select);

		// add some important fields
		$document->addField('id', $entityId);
		$document->addField('store_id', $storeId);
		$document->addField('unique', $storeId . '|' . $entityId);
		$document->addField('fulltext', implode('|', $fulltext));

        //Mage::dispatchEvent('mtsearch_prepare_document_data', array('document' => $document));

		return $document;
	}

	/**
	 * Calculate attribute value per type
	 *
	 * @param int $storeId
	 * @param string $attributeCode
	 * @param mixed $values
	 * @param bool $returnLabel
	 * @return string
	 */
	protected function _calculateAttributeValue($storeId, $attributeCode, $values, $returnLabel = true) {
		$output = '';
		
		if (!$this->_attributes){
			$attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
				->addVisibleFilter()
				->load();

			foreach ($attributeCollection as $attributeModel){
				$this->_attributes[$attributeModel->getAttributeCode()] = $attributeModel;
			}
		}

		if (isset($this->_attributes[$attributeCode])){
			$attributeModel = $this->_attributes[$attributeCode];
			$attributeModel->setStoreId($storeId);

			if ($attributeModel->getIsSearchable() || $attributeModel->getIsFilterable() || $attributeModel->getIsFilterableInSearch()){
				$input = $attributeModel->getFrontendInput();
				if ($input == 'textarea' || $input == 'text'){
					if (is_array($values)){
						if ($attributeCode == 'name'){
							$output = (string) array_shift($values);
						}else $output = implode('|', $values);
					}else $output = $values;
				}elseif ($input == 'select' || $input == 'multiselect'){
					if ($attributeModel->getIsFilterable() || $attributeModel->getIsFilterableInSearch()){
						if (!$returnLabel){
							if ($input == 'multiselect'){
								return explode(',', array_pop($values));
							}else return array_unique(array_values($values));
						}
					}

					if (!isset($this->_attributeOptions[$attributeCode])){
						$options = $attributeModel->getSource()->getAllOptions();
						$tmp = array();
						foreach ($options as $option){
							$tmp[$option['value']] = $option['label'];
						}
						$this->_attributeOptions[$attributeCode] = $tmp;
					}

					$ops = array();
					foreach ($values as $value){
						if ($input == 'multiselect'){
							foreach (explode(',', $value) as $val){
								$ops[] = isset($this->_attributeOptions[$attributeCode][$val]) ? $this->_attributeOptions[$attributeCode][$val] : '';
							}
						}else{
							$ops[] = isset($this->_attributeOptions[$attributeCode][$value]) ? $this->_attributeOptions[$attributeCode][$value] : '';
						}
					}
					$output = implode('|', $ops);
				}else{
					$output = implode('|', $values);
				}
			}
		}

		if ($returnLabel) return $output;
	}

	/**
	 * Prepare index array data
	 *
	 * @param array $index
	 * @param string $separator
	 * @return array
	 */
	public function prepareEntityIndex($index, $separator = ' ') {
		return $index;
	}

	/**
	 * Remove entity data from Solr data, only process if get valid $storeId
	 *
	 * @param int $storeId
	 * @param int $entityId
	 * @param string $entity 'product'|'cms'
	 * @return $this
	 */
	public function cleanIndex($storeId = null, $entityId = null, $entity = 'product') {
		if (is_numeric($storeId) && $storeId > 0) {
			if (is_null($entityId)) {
				$this->getAdapter()->deleteByQuery('*:* AND store_id:' . $storeId);
				$this->getAdapter()->commit();
			}elseif (is_numeric($entityId) && $entityId > 0) {
				$this->getAdapter()->deleteByQuery('unique:' . $storeId . '|' . $entityId);
				$this->getAdapter()->commit();
			}
		}

		return $this;
	}

	/**
	 * Support advanced index with Solr
	 *
	 * @return bool
	 */
	public function allowAdvancedIndex() {
		return false;
	}

	/**
	 * Retrieve allowed visibility values for current engine
	 *
	 * @return array
	 */
	public function getAllowedVisibility() {
		return Mage::getSingleton('catalog/product_visibility')->getVisibleInSiteIds();
	}

	/**
	 * Define if Layered Navigation is allowed
	 *
	 * @return bool
	 */
	public function isLeyeredNavigationAllowed() {
		return true;
	}
}