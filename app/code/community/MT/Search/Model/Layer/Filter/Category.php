<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Model_Layer_Filter_Category extends Mage_Catalog_Model_Layer_Filter_Category {
	/**
	 * Get data array for building category filter items
	 * TODO: speed improvement
	 *
	 * @return array
	 */
	protected function _getItemsData() {
		$attrField = 'category_ids';
		$params = array(
			'facet' => 'on',
			'facet.limit' => -1,
			'facet.field' => $attrField
		);
		list($q, $filters) = Mage::helper('mtsearch')->getCurrentFilters();
		$filters['store_id'] = Mage::app()->getStore()->getStoreId();

		try{
			$result = Mage::getModel('mtsearch/service')->query($q, $filters, null, 0, 0, $params);

			$childs = array();
			foreach($this->getCategory()->getChildrenCategories() as $child){
				$childs[$child->getId()] = $child;
			}

			$data = $result->getFacetCounts();
			if ($data){
				if (isset($data->facet_fields)){
					$categories = array();
					foreach ($data->facet_fields->$attrField as $value => $count){
						if (isset($childs[$value])){
							$model = $childs[$value];
						 	if ($model->getId() && $model->getIsActive() && $count){
								$categories[] = array(
									'label' => $model->getName(),
									'value' => $value,
									'count' => $count
								);
							}
						}
					}
					return $categories;
				}
			}
		}catch(Exception $e){
			return array();
		}
	}
}