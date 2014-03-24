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
     * Apply category filter to layer
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Mage_Core_Block_Abstract $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Category
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock){
        $filter = (int) $request->getParam($this->getRequestVar());
        if (!$filter) {
            $module = Mage::app()->getFrontController()->getRequest()->getModuleName();
            if ($module == 'catalog'){
                $this->_categoryId = $filter;
            }else return $this;
        }else $this->_categoryId = $filter;

        $this->_appliedCategory = $this->getCategory();
        Mage::register('current_category_filter', $this->_appliedCategory, true);

        if ($this->_isValidCategory($this->_appliedCategory)) {
            $this->getLayer()->getProductCollection()->addCategoryFilter($this->_appliedCategory);

            /*$this->getLayer()->getState()->addFilter(
                $this->_createItem($this->_appliedCategory->getName(), $filter)
            );*/
        }

        return $this;
    }

    protected function _cloneSelect(&$newSelect, $select){
        $parts = array('columns', 'straightjoin', 'distinct', 'union', 'from', 'where', 'group', 'having', 'order', 'limitcount', 'limitoffset', 'forupdate');
        foreach ($parts as $part){
            $newSelect->setPart($part, $select->getPart($part));
        }
        return $newSelect;
    }

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
            $expandCategoryId = $category->getId();
            /* @var $category Mage_Catalog_Model_Category */
            if ($category->getLevel() == 1 || $category->getLevel() == 2){
                $categories = $category->getChildrenCategories();
                $this->getLayer()->getProductCollection()->addCountToCategories($categories);
                $collection = $this->getLayer()->getProductCollection();
            }else{
                if ($category->hasChildren()){
                    $parent = $category->getParentCategory();
                }else{
                    $parent = $category->getParentCategory();
                    if ($category->getLevel() > 3){
                        $expandCategoryId = $parent->getId();
                        $parent = $parent->getParentCategory();
                    }
                }
                $categories = $parent->getChildrenCategories();
                $select = clone $this->getLayer()->getProductCollection()->getSelect();
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
                $collection = Mage::getResourceModel('catalog/product_collection');
                $newSelect = $collection->getSelect();
                $this->_cloneSelect($newSelect, $select);
                $collection->addCountToCategories($categories);
                unset($parent, $select, $fromPart, $newSelect);
            }

            $data = array();
            foreach ($categories as $cat) {
                if ($cat->getIsActive() && $cat->getProductCount()) {
                    $tmp = array(
                        'label' => Mage::helper('core')->escapeHtml($cat->getName()),
                        'value' => $cat->getId(),
                        'count' => $cat->getProductCount(),
                        'href'  => $module == 'catalog' ? $cat->getUrl() : '',
                        'isActive' => $cat->getId() == $expandCategoryId
                    );
                    if ($cat->getId() == $expandCategoryId){
                        $childs = $cat->getChildrenCategories();
                        $collection->addCountToCategories($childs);
                        foreach ($childs as $child){
                            if (!$child->getProductCount()) continue;
                            /* @var $child Mage_Catalog_Model_Category */
                            $tmp['child'][] = array(
                                'label' => Mage::helper('core')->escapeHtml($child->getName()),
                                'value' => $child->getId(),
                                'count' => $child->getProductCount(),
                                'href'  => $module == 'catalog' ? $child->getUrl() : '',
                                'isActive' => $child->getId() == $category->getId()
                            );
                        }
                    }
                    $data[] = $tmp;
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
                $itemData['isActive'],
                isset($itemData['child']) ? $itemData['child'] : array()
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
     * @param   array $child
     * @return  Mage_Catalog_Model_Layer_Filter_Item
     */
    protected function _createItem($label, $value, $count=0, $href='', $isActive=false, $child=array()) {
        $item = Mage::getModel('kidsplaza/catalog_layer_filter_item')
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count)
            ->setHref($href)
            ->setIsActive($isActive);

        if (count($child)){
            $tmp = array();
            foreach ($child as $c){
                $tmp[] = Mage::getModel('kidsplaza/catalog_layer_filter_item')
                    ->setFilter($this)
                    ->setLabel($c['label'])
                    ->setValue($c['value'])
                    ->setCount($c['count'])
                    ->setHref($c['href'])
                    ->setIsActive($c['isActive']);
            }
            $item->setChild($tmp);
        }

        return $item;
    }
}