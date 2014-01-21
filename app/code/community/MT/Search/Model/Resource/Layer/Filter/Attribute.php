<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Model_Resource_Layer_Filter_Attribute extends Mage_Catalog_Model_Resource_Layer_Filter_Attribute {
	/**
	 * Retrieve array with products counts per attribute option via Solr request
	 *
	 * @param Mage_Catalog_Model_Layer_Filter_Attribute $filter
	 * @return array
	 */
	public function getCount($filter) {
		$attribute = $filter->getAttributeModel();

		$attrField = 'attr_' . $attribute->getAttributeCode() . '_value';
		$params = array(
			'facet' => 'on',
			'facet.limit' => -1,
			'facet.field' => $attrField
		);
		list($q, $filters) = Mage::helper('mtsearch')->getCurrentFilters();
		$filters['store_id'] = Mage::app()->getStore()->getStoreId();

		try{
			$result = Mage::getModel('mtsearch/service')->query($q, $filters, null, 0, 0, $params);

			$data = $result->getFacetCounts();
			if ($data){
				if (isset($data->facet_fields)){
					$countArray = array();
					foreach ($data->facet_fields->$attrField as $value => $count){
						$countArray[$value] = $count;
					}
					return $countArray;
				}
			}
		}catch(Exception $e){
			return array();
		}
	}
}