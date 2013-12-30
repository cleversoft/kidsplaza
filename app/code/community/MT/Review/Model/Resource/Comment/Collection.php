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
class MT_Review_Model_Resource_Comment_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    /**
     * Review detail table
     *
     * @var string
     */
    protected $_reviewDetailTable;

    protected function _construct()
    {
        $this->_init('mtreview/comment');
        $this->_reviewDetailTable   = $this->getTable('mtreview/review_detail');

    }

    /**
     * init select
     *
     *
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinFields();
        return $this;
    }

    /**
     * join fields to review detail
     *
     *
     */
    protected function _joinFields()
    {
        $this->getSelect()
            ->join(array('rdt' => $this->_reviewDetailTable),
                'rdt.review_id = main_table.review_id',
                array('rdt.title','rdt.nickname', 'rdt.detail', 'rdt.customer_id', 'rdt.store_id'));
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
     * Set date order
     *
     * @param string $dir
     *
     */
    public function setDateOrder($dir = 'DESC')
    {
        $this->setOrder('main_table.created_at', $dir);
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
