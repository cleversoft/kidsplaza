<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Model_Service extends Apache_Solr_Service {
	protected $_response;

	/**
	 * Constructor for Solr server connection
	 */
	public function __construct() {
		$host = Mage::getStoreConfig('mtsearch/solr/host');
		$port = Mage::getStoreConfig('mtsearch/solr/port');
		$path = Mage::getStoreConfig('mtsearch/solr/path');
		
		parent::__construct($host, $port, $path);
	}

	/**
	 * Prepare params for search
	 */
	public function query($queryText, $filters = array(), $attributes = array(), $limit = 100, $offset = 0, $params = array()) {
		$params['fl'] = is_array($attributes) ? implode(',', $attributes) . ',score' : 'id,score';
		$params['sort'] = isset($params['sort']) ? $params['sort'] : 'score desc';

		if (is_array($filters)){
			if (Mage::getStoreConfig('cataloginventory/options/show_out_of_stock') == 0) $filters['in_stock'] = 1;
			$tmpFilters = array();
			foreach ($filters as $attr => $val){
				if (is_array($val)){
					$tmpFilter = array();
					foreach ($val as $v){
						$tmpFilter[] = $attr . ':' . $v;
					}
					$tmpFilters[] = '(' . implode(' OR ', $tmpFilter) . ')';
				}else $tmpFilters[] = $attr . ':' . $val;
			}
			$params['fq'] = implode(' AND ', $tmpFilters);
		}

		// search weight
		if (Mage::getStoreConfigFlag('mtsearch/utils/weight')){
			$weightParam = Mage::app()->loadCache('WEIGHT_PARAM');
			if (!$weightParam){
				$attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
					->addVisibleFilter()
					->addIsSearchableFilter()
					->load();

				$tmp = array();
				foreach ($attributeCollection as $attribute){
					if ($attribute->getSearchWeight()){
						$tmp[] = 'attr_' . $attribute->getAttributeCode() . '^' . $attribute->getSearchWeight();
					}
				}
				$weightParam = implode(' ', $tmp);
				Mage::app()->saveCache($weightParam, 'WEIGHT_PARAM', array(Mage_Core_Model_Translate::CACHE_TAG));
			}
			$params['defType'] = 'dismax';
			$params['qf'] = $weightParam;
		}

		// spellcheck
		if (Mage::getStoreConfigFlag('mtsearch/utils/spell') && !isset($params['facet'])){
			$response = $this->search($queryText, $offset, $limit, $params, 'POST', true);
		}else{
			$response = $this->search($queryText, $offset, $limit, $params, 'POST');
		}

		$this->_response = $response;
		return $this;
	}

	/**
	 * Check if founds any id
	 *
	 * @return int
	 */
	public function hasProduct() {
		if ($this->_response && $this->_response->response){
			return count($this->_response->response->docs);
		}
	}

	/**
	 * Extract data from response
	 *
	 * @return array
	 */
	public function getProductsFromResult() {
		if ($this->_response && $this->_response->response){
			$data = array();
			foreach ($this->_response->response->docs as $doc){
				$data[] = $doc->id;
			}
			return $data;
		}
	}

	/**
	 *
	 */
	public function getNumFound() {
		if ($this->_response){
			return (int) $this->_response->response->numFound;
		}
	}

	/**
	 * Return raw docs
	 *
	 * @return array
	 */
	public function getDocs() {
		if ($this->_response && $this->_response->response){
			return $this->_response->response->docs;
		}
	}

	/**
	 * Get suggestions words
	 *
	 * @return array
	 */
	public function getSuggestions() {
		$words = array();

		if ($this->_response && $this->_response->spellcheck){
			foreach ($this->_response->spellcheck->suggestions as $suggest){
				if (is_object($suggest) && isset($suggest->numFound)){
					foreach ($suggest->suggestion as $word){
						$words[] = $word->word;
					}
				}
			}
		}

		return $words;
	}

	/**
	 * Get facet responso
	 *
	 * @return object
	 */
	public function getFacetCounts(){
		if ($this->_response){
			return $this->_response->facet_counts;
		}
	}
}