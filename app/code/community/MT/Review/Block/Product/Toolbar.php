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
        return null;
    }

    public function getAvailableLimit()
    {
        return $this->getReviews()->getAvailLimits();
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

    public function getLimit()
    {
        return $this->getRequest()->getParam($this->getLimitVarName());
    }
}