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
class MT_Review_Model_Resource_Helpfulness_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('mtreview/helpfulness');

    }

    /**
     * init select
     *
     *
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        return $this;
    }
    /**
     * Add customer filter
     *
     * @param int $customerId
     *
     */
    public function addCustomerFilter($customerId)
    {
        $this->getSelect()
            ->where('main_table.customer_id = ?', $customerId);
        return $this;
    }

    /**
     * Add reivew filter
     *
     * @param int $reviewId
     */
    public function addReviewFilter($reviewId)
    {
        $this->getSelect()
            ->where('main_table.review_id = ?', $reviewId);
        return $this;
    }
}
