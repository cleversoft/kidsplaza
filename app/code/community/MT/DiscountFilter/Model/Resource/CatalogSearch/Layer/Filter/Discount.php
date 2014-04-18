<?php
/**
 * @category    MT
 * @package     MT_DiscountFilter
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_DiscountFilter_Model_Resource_CatalogSearch_Layer_Filter_Discount extends MT_DiscountFilter_Model_Resource_Catalog_Layer_Filter_Discount{
    /**
     * Retrieve array with products counts
     *
     * @param MT_Search_Model_Layer_Filter_Price $filter
     * @return array
     */
    public function getCount($filter) {
        list($q, $filters) = Mage::helper('mtsearch')->getCurrentFilters();
        $priceName = Mage::helper('mtsearch')->getCurrentPriceName('b');
        $filters[$priceName] = 1;

        try{
            $result = Mage::getModel('mtsearch/service')->query($q, $filters);
            return array('count' => $result->getNumFound());
        }catch(Exception $e){
            Mage::logException($e);
            return array();
        }
    }
}