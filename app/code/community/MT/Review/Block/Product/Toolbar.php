<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_Review
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */
class MT_Review_Block_Product_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
    public function setCollection($collection)
    {
        parent::setCollection($collection);
        if ($this->getCurrentOrder() && $this->getCurrentDirection()) {

            $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());

            if($this->getCurrentOrder() == 'helpfulness')
            {
                $this->_collection->sortHelpfulnessReview($this->getCurrentOrder(), $this->getCurrentDirection());
            }else if($this->getCurrentOrder() == 'rating'){
                $this->_collection->sortRatingReview($this->getCurrentOrder(), $this->getCurrentDirection());
            }
        }

        return $this;
    }

    public function getCurrentOrder()
    {
        $order = $this->getRequest()->getParam($this->getOrderVarName());

        if (!$order) {
            return $this->_orderField;
        }

        if (array_key_exists($order, $this->getAvailableOrders())) {
            return $order;
        }

        return $this->_orderField;
    }

    public function getCurrentMode()
    {
        $mode = $this->_getData('_current_grid_mode');
        if ($mode) {
            return $mode;
        }
        $mode = 'list';
        $this->setData('_current_grid_mode', $mode);
        return $mode;
    }

    protected function _getAvailableLimit($mode)
    {
        if (isset($this->_availableLimit[$mode])) {
            return $this->_availableLimit[$mode];
        }
        $perPageConfigKey = 'mtreview/ordering_options/items_per_page';
        $perPageValues = (string)Mage::getStoreConfig($perPageConfigKey);
        $perPageValues = explode(',', $perPageValues);
        $perPageValues = array_combine($perPageValues, $perPageValues);
        return $perPageValues;
    }

    public function isEnabledViewSwitcher()
    {
        return true;
    }

    public function getCurrentDirection()
    {
        $dir = $this->getRequest()->getParam($this->getDirectionVarName());
        if (in_array($dir, array('asc', 'desc'))) {
            return $dir;
        }

        return Mage::helper('mtreview')->confDefaultSort();
    }

    public function setDefaultOrder($field)
    {
        $this->_orderField = $field;
    }

    public function getDefaultPerPageValue()
    {
        return null;
    }
}