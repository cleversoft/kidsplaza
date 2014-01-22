<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Model_Resource_Layer_Filter_Price extends Mage_Catalog_Model_Resource_Layer_Filter_Price {
	/**
	 * Retrieve array with products counts per price range
	 *
	 * @param MT_Search_Model_Layer_Filter_Price $filter
	 * @param int $range
	 * @return array
	 */
	public function getCount($filter, $range) {
		$attribute = $filter->getAttributeModel();
		$storeId = Mage::app()->getStore()->getId();
		$websiteId = Mage::app()->getStore()->getWebsiteId();
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		$customerGroupId = $customer->getId() ? $customer->getGroupId() : 0;
		$attrField = sprintf('price_%d_%d_f', $websiteId, $customerGroupId);
		$maxPrice = $filter->getMaxPriceInt();
		$lastIndex = floor($maxPrice / $range);

		$params = array(
			'facet' => 'on',
			'facet.limit' => -1);

		for($i = 0; $i <= $lastIndex; $i++){
			if ($i == $lastIndex) $params['facet.query'][] = sprintf('%s:[%d TO *]', $attrField, $range * $i, $range * ($i + 1));
			else $params['facet.query'][] = sprintf('%s:[%d TO %d]', $attrField, $range * $i, $range * ($i + 1));
		}

		list($q, $filters) = Mage::helper('mtsearch')->getCurrentFilters();
		$filters['store_id'] = Mage::app()->getStore()->getStoreId();

		try{
			$result = Mage::getModel('mtsearch/service')->query($q, $filters, null, 0, 0, $params);

			$data = $result->getFacetCounts();
			$out = array();
			if (isset($data->facet_queries)){
				$i = 1;
				foreach ($data->facet_queries as $r => $count){
					$out[$i] = $count;
					$i++;
				}
			}
			return $out;
		}catch(Exception $e){
			return array();
		}
	}
}