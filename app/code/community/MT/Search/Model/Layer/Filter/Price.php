<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Model_Layer_Filter_Price extends MT_Filter_Model_Layer_Filter_Price {
	/**
	 * Retrieve resource instance
	 *
	 * @return MT_Search_Model_Resource_Layer_Filter_Price
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
			$priceName = Mage::helper('mtsearch')->getCurrentPriceName();
			$params['sort'] = $priceName . ' desc';
            if (isset($filters[$priceName])) unset($filters[$priceName]);

			try{
				$result = Mage::getModel('mtsearch/service')->query($q, $filters, array($priceName), 1, 0, $params);

				foreach($result->getDocs() as $doc){
					$maxPrice = $doc->$priceName;
				}
			}catch(Exception $e){
                Mage::logException($e);
            }

			$maxPrice = floor($maxPrice);
			$this->setData('max_price_int', $maxPrice);
		}

		return $maxPrice;
	}

    /**
     * Get data for build price filter items
     * Override to prevent skip price count if price filter applied
     *
     * @return array
     */
    protected function _getItemsData(){
        if (Mage::app()->getStore()->getConfig(self::XML_PATH_RANGE_CALCULATION) == self::RANGE_CALCULATION_IMPROVED) {
            return $this->_getCalculatedItemsData();
        } elseif ($this->getInterval()) {
            // dont skip me
            //return array();
        }

        $range      = $this->getPriceRange();
        $dbRanges   = $this->getRangeItemCounts($range);
        $data       = array();

        if (!empty($dbRanges)) {
            $lastIndex = array_keys($dbRanges);
            $lastIndex = $lastIndex[count($lastIndex) - 1];

            foreach ($dbRanges as $index => $count) {
                if (!$count) continue;
                $fromPrice = ($index == 1) ? '' : (($index - 1) * $range);
                $toPrice = ($index == $lastIndex && count($dbRanges) > 1) ? '' : ($index * $range);

                $data[] = array(
                    'label' => $this->_renderRangeLabel($fromPrice, $toPrice),
                    'value' => $fromPrice . '-' . $toPrice,
                    'count' => $count
                );
            }
        }

        return $data;
    }
}