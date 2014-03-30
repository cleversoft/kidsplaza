<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2008-2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Model_Catalogsearch_Layer_Filter_Category extends MT_Search_Model_Layer_Filter_Category{
    /**
     * Get data array for building category filter items
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
        //if (isset($filters['category_ids'])) unset($filters['category_ids']);

        try{
            $result = Mage::getModel('mtsearch/service')->query($q, $filters, null, 0, 0, $params);

            $childs = array();
            if ($this->getCategory()->getLevel() == 1){
                foreach ($this->getCategory()->getChildrenCategories() as $child){
                    $childs[$child->getId()] = $child;
                }
            }else{
                foreach ($this->getCategory()->getParentCategory()->getChildrenCategories() as $child){
                    $childs[$child->getId()] = $child;
                }
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
                                    'count' => $count,
                                    'isActive' => $this->getCategory()->getId() == $value
                                );
                            }
                        }
                    }
                    return $categories;
                }
            }
        }catch(Exception $e){
            Mage::logException($e);
            return array();
        }
    }

    /**
     * Initialize filter items
     *
     * @return  Mage_Catalog_Model_Layer_Filter_Abstract
     */
    protected function _initItems() {
        $data = $this->_getItemsData();
        $items=array();
        foreach ($data as $itemData) {
            $items[] = $this->_createItem(
                $itemData['label'],
                $itemData['value'],
                $itemData['count'],
                $itemData['isActive']
            );
        }
        $this->_items = $items;
        return $this;
    }

    /**
     * Create filter item object
     *
     * @param   string $label
     * @param   mixed $value
     * @param   int $count
     * @param   bool $isActive
     * @return  Mage_Catalog_Model_Layer_Filter_Item
     */
    protected function _createItem($label, $value, $count=0, $isActive=false) {
        return Mage::getModel('kidsplaza/catalogsearch_layer_filter_item')
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count)
            ->setIsActive($isActive);
    }
}