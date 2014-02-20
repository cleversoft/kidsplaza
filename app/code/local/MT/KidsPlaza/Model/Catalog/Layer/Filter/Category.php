<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2008-2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Model_Catalog_Layer_Filter_Category extends MT_Filter_Model_Layer_Filter_Category{
    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData() {
        $key = $this->getLayer()->getStateKey().'_SUBCATEGORIES';
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {
            $module = Mage::app()->getFrontController()->getRequest()->getModuleName();
            $category = $this->getCategory();
            /** @var $category Mage_Catalog_Model_Category */
            if ($category->hasChildren()){
                $categories = $category->getChildrenCategories();
                $this->getLayer()->getProductCollection()->addCountToCategories($categories);
            }else{
                $parent = $category->getParentCategory();
                $categories = $parent->getChildrenCategories();
                $collection = clone $this->getLayer()->getProductCollection();
                $select = $collection->getSelect();
                $fromPart = $select->getPart('from');
                foreach ($fromPart as $key => $part){
                    if ($key === 'cat_index'){
                        $conditions = explode('AND', $part['joinCondition']);
                        $newConditions = array();
                        foreach ($conditions as $condition){
                            if (strpos($condition, 'cat_index.category_id') > -1){
                                list($k, $v) = explode('=', $condition);
                                $newConditions[] = sprintf("%s=%d", trim($k), $parent->getId());
                            }else{
                                $newConditions[] = trim($condition);
                            }
                        }
                        $fromPart[$key]['joinCondition'] = join(' AND ', $newConditions);
                    }
                }
                $select->setPart('from', $fromPart);
                $collection->addCountToCategories($categories);
                unset($parent, $collection, $select, $fromPart);
            }

            $data = array();
            foreach ($categories as $cat) {
                if ($cat->getIsActive() && $cat->getProductCount()) {
                    $data[] = array(
                        'label' => Mage::helper('core')->escapeHtml($cat->getName()),
                        'value' => $cat->getId(),
                        'count' => $cat->getProductCount(),
                        'href'  => $module == 'catalog' ? $cat->getUrl() : '',
                        'isActive' => $cat->getId() == $category->getId()
                    );
                }
            }
            $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
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
                $itemData['href'],
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
     * @param   string $href
     * @param   bool $isActive
     * @return  Mage_Catalog_Model_Layer_Filter_Item
     */
    protected function _createItem($label, $value, $count=0, $href='', $isActive=false) {
        return Mage::getModel('kidsplaza/catalog_layer_filter_item')
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count)
            ->setHref($href)
            ->setIsActive($isActive);
    }
}