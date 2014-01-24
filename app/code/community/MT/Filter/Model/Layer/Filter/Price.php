<?php
/**
 * @category    MT
 * @package     MT_Filter
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Filter_Model_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Price{
    protected $_request;

    public function getItemsCount(){
        if (Mage::helper('mtfilter')->isPriceEnable()){
            return true;
        }else{
            return parent::getItemsCount();
        }
    }

    /**
     * Retrieve resource instance
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Price
     */
    protected function _getResource(){
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getResourceModel('mtfilter/layer_filter_price');
        }
        return $this->_resource;
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

    /**
     * Get maximum price from layer products set
     * Override to reset all attribute filter applied
     *
     * @return float
     */
    public function getMaxPriceInt(){
        $maxPrice = $this->getData('max_price_int');
        if (is_null($maxPrice)) {
            $select = clone $this->getLayer()->getProductCollection()->getSelect();
            $wherePart = $select->getPart('where');
            foreach ($wherePart as $i => $where){
                if (strpos($where, 'price_index.') >= 0){
                    unset($wherePart[$i]);
                }
            }
            $fromPart = $select->getPart('from');
            foreach ($fromPart as $i => $from){
                if ($from['tableName'] == 'catalog_product_index_eav'){
                    unset($fromPart[$i]);
                }
            }
            $collection = Mage::getResourceModel('catalog/product_collection');
            $newSelect = $collection->getSelect();
            $newSelect->setPart('from', $fromPart);
            $newSelect->setPart('where', $wherePart);
            $maxPrice = $collection->getMaxPrice();
            $maxPrice = floor($maxPrice);
            $this->setData('max_price_int', $maxPrice);
            unset($select, $collection);
        }

        return $maxPrice;
    }

    /**
     * Get price range for building filter steps
     * Override to get only first range
     *
     * @return int
     */
    public function getPriceRange(){
        $range = $this->getData('price_range');
        if (!$range) {
            $currentCategory = Mage::registry('current_category_filter');
            if ($currentCategory) {
                $range = $currentCategory->getFilterPriceRange();
            } else {
                $range = $this->getLayer()->getCurrentCategory()->getFilterPriceRange();
            }

            $maxPrice = $this->getMaxPriceInt();
            if (!$range) {
                $calculation = Mage::app()->getStore()->getConfig(self::XML_PATH_RANGE_CALCULATION);
                if ($calculation == self::RANGE_CALCULATION_AUTO) {
                    // return now
                    $range = pow(10, (strlen(floor($maxPrice)) - 1));
                } else {
                    $range = (float)Mage::app()->getStore()->getConfig(self::XML_PATH_RANGE_STEP);
                }
            }

            $this->setData('price_range', $range);
        }

        return $range;
    }

    /**
     * Apply price range filter
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param $filterBlock
     *
     * @return Mage_Catalog_Model_Layer_Filter_Price
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock){
        $this->_request = $request;
        /**
         * Filter must be string: $fromPrice-$toPrice
         */
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter) {
            return $this;
        }

        //validate filter
        $filterParams = explode(',', $filter);
        $filter = $this->_validateFilter($filterParams[0]);
        if (!$filter) {
            return $this;
        }

        list($from, $to) = $filter;

        $this->setInterval(array($from, $to));

        $priorFilters = array();
        for ($i = 1; $i < count($filterParams); ++$i) {
            $priorFilter = $this->_validateFilter($filterParams[$i]);
            if ($priorFilter) {
                $priorFilters[] = $priorFilter;
            } else {
                //not valid data
                $priorFilters = array();
                break;
            }
        }
        if ($priorFilters) {
            $this->setPriorIntervals($priorFilters);
        }

        $this->_applyPriceRange();
        /*$this->getLayer()->getState()->addFilter($this->_createItem(
            $this->_renderRangeLabel(empty($from) ? 0 : $from, $to),
            $filter
        ));*/

        return $this;
    }

    /**
     * Create filter item object
     *
     * @param   string $label
     * @param   mixed $value
     * @param   int $count
     * @return  Mage_Catalog_Model_Layer_Filter_Item
     */
    protected function _createItem($label, $value, $count=0){
        return Mage::getModel('mtfilter/layer_filter_item')
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count);
    }

    /**
     * Return current filter
     *
     * @return mixed
     */
    public function getRequestValue(){
        return $this->_request->getParam($this->_requestVar);
    }
}