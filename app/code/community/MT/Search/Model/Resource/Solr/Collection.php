<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Model_Resource_Solr_Collection extends Mage_CatalogSearch_Model_Resource_Fulltext_Collection {
	protected $_ids;

	/**
	 * Add search query filter
	 *
	 * @param string $query
	 * @return Mage_CatalogSearch_Model_Resource_Fulltext_Collection
	 */
	public function addSearchFilter($query) {
		$limit = Mage::getStoreConfig('catalog/frontend/grid_per_page');
		$page = Mage::app()->getRequest()->getParam('p', 1);
		$limit = Mage::app()->getRequest()->getParam('limit', $limit);
		$offset = $limit * ($page - 1);

		list($q, $filters, $order) = Mage::helper('mtsearch')->getCurrentFilters();
		$filters['store_id'] = Mage::app()->getStore()->getStoreId();

		try{
			$result = Mage::getModel('mtsearch/service')->query(
                $query,
                $filters,
                array('name', 'id'),
                $limit,
                $offset,
                array('sort' => $order)
            );

			if ($result->hasProduct()) {
				$this->_ids = $result->getProductsFromResult();
				$this->_totalRecords = $result->getNumFound();
			}

			$words = $result->getSuggestions();
			if (count($words)){
				Mage::helper('catalogsearch')->addNoteMessage(Mage::helper('mtsearch')->getSuggestionsHtml($words));
			}

			$this->getSelect()->where('e.entity_id IN (?)', $this->_ids);
			$this->getSelect()->reset(Zend_Db_Select::LIMIT_OFFSET);
		}catch(Exception $e){
			$this->getSelect()->where('e.entity_id IN (0)');
		}

		return $this;
	}

	/**
	 * Return number of product on this page
	 *
	 * @return int
	 */
	public function count() {
		return count($this->_ids);
	}

	/**
	 * Set Order field
	 *
	 * @param string $attribute
	 * @param string $dir
	 * @return Mage_CatalogSearch_Model_Resource_Fulltext_Collection
	 */
	public function setOrder($attribute, $dir = 'desc') {
		return $this;
	}

	/**
	 * Add tax class id attribute to select and join price rules data if needed
	 *
	 * @return Mage_Catalog_Model_Resource_Product_Collection
	 */
	protected function _beforeLoad() {
		if ($this->_ids) $this->getSelect()->order(new Zend_Db_Expr(sprintf("FIELD(e.entity_id, %s)", implode(',', $this->_ids))));

		return parent::_beforeLoad();
	}
}