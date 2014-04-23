<?php
/**
 * @category    MT
 * @package     MT_RatingFilter
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_RatingFilter_Model_Resource_CatalogSearch_Layer_Filter_Rating extends MT_RatingFilter_Model_Resource_Catalog_Layer_Filter_Rating{
    /**
     * @param $filter Mage_Catalog_Model_Layer_Filter_Abstract
     * @return array
     */
    public function getCount($filter){
        $params = array(
            'facet' => 'on',
            'facet.limit' => -1
        );

        $attrField = 'rating_i';
        for ($i = 0; $i < 5; $i++){
            if ($i == 0) $params['facet.query'][] = sprintf('%s:[%d TO %d]', $attrField, 20 * $i, 20 * ($i + 1));
            else $params['facet.query'][] = sprintf('%s:[%d TO %d]', $attrField, 20 * $i + 1, 20 * ($i + 1));
        }

        list($q, $filters) = Mage::helper('mtsearch')->getCurrentFilters();
        if (isset($filters[$attrField])) unset($filters[$attrField]);

        try{
            $result = Mage::getModel('mtsearch/service')->query($q, $filters, null, 0, 0, $params);

            $data = $result->getFacetCounts();
            $out = array();
            if (isset($data->facet_queries)){
                $i = 1;
                foreach ($data->facet_queries as $count){
                    $out[$i] = $count;
                    $i++;
                }
            }
            return $out;
        }catch(Exception $e){
            Mage::logException($e);
            return array();
        }
    }
}