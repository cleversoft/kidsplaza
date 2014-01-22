<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Model_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Price {
	const RANGE_CALCULATION_IMPROVED = 'improved';

	/**
	 * Retrieve resource instance
	 *
	 * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Price
	 */
	protected function _getResource() {
		if (is_null($this->_resource)) {
			$this->_resource = Mage::getResourceModel('mtsearch/layer_filter_price');
		}
		return $this->_resource;
	}

	/**
	 * Get maximum price from layer products set
	 *
	 * @return int
	 */
	public function getMaxPriceInt() {
		$maxPrice = $this->getData('max_price_int');
		if (is_null($maxPrice)) {
			list($q, $filters) = Mage::helper('mtsearch')->getCurrentFilters();
			$filters['store_id'] = Mage::app()->getStore()->getStoreId();
			$priceName = Mage::helper('mtsearch')->getCurrentPriceName();
			$params['sort'] = $priceName . ' desc';

			try{
				$result = Mage::getModel('mtsearch/service')->query($q, $filters, array($priceName), 1, 0, $params);

				foreach($result->getDocs() as $doc){
					$maxPrice = $doc->$priceName;
				}
			}catch(Exception $e){}

			$maxPrice = floor($maxPrice);
			$this->setData('max_price_int', $maxPrice);
		}

		return $maxPrice;
	}

	/**
	 * Get data for build price filter items
	 *
	 * @return array
	 */
	protected function _getItemsData() {
		if (Mage::app()->getStore()->getConfig(self::XML_PATH_RANGE_CALCULATION) == self::RANGE_CALCULATION_IMPROVED) {
			return array();
		} elseif ($this->getInterval()) {
			return array();
		}

		$range      = $this->getPriceRange();
		$dbRanges   = $this->getRangeItemCounts($range);
		$data       = array();

		if (!empty($dbRanges)) {
			$lastIndex = array_keys($dbRanges);
			$lastIndex = $lastIndex[count($lastIndex) - 1];

			foreach ($dbRanges as $index => $count) {
				if ($count){
					$fromPrice = ($index == 1) ? '' : (($index - 1) * $range);
					$toPrice = ($index == $lastIndex) ? '' : ($index * $range);

					$data[] = array(
						'label' => $this->_renderRangeLabel($fromPrice, $toPrice),
						'value' => $fromPrice . '-' . $toPrice,
						'count' => $count,
					);
				}
			}
		}

		return $data;
	}

	/**
	 * Prepare text of range label
	 *
	 * @param float|string $fromPrice
	 * @param float|string $toPrice
	 * @return string
	 */
	protected function _renderRangeLabel($fromPrice, $toPrice) {
		$store = Mage::app()->getStore();
		$formattedFromPrice  = $store->formatPrice($fromPrice);
		if ($toPrice === '') {
			return Mage::helper('catalog')->__('%s and above', $formattedFromPrice);
		} elseif ($fromPrice == $toPrice && Mage::app()->getStore()->getConfig(self::XML_PATH_ONE_PRICE_INTERVAL)) {
			return $formattedFromPrice;
		} else {
			if ($fromPrice != $toPrice) {
				$toPrice -= .01;
			}
			return Mage::helper('catalog')->__('%s - %s', $formattedFromPrice, $store->formatPrice($toPrice));
		}
	}

	/**
	 * Apply price range filter
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 * @param $filterBlock
	 *
	 * @return Mage_Catalog_Model_Layer_Filter_Price
	 */
	public function apply(Zend_Controller_Request_Abstract $request, $filterBlock) {
		if (version_compare(Mage::getVersion(), '1.7') == -1){
			/**
			 * Filter must be string: $index,$range
			 */
			$filter = $request->getParam($this->getRequestVar());
			if (!$filter) {
				return $this;
			}

			$filter = explode('-', $filter);
			if (count($filter) != 2) {
				return $this;
			}

			list($index, $range) = $filter;

			$this->setPriceRange((int)$range);

			$this->_applyToCollection($range, $index);
			$this->getLayer()->getState()->addFilter(
				$this->_createItem($this->_renderRangeLabel($index, $range), $filter)
			);

			$this->_items = array();

			return $this;
		}else return parent::apply($request, $filterBlock);
	}
}