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
     * Add store data flag
     *
     * @var boolean
     */
    protected $_addStoreDataFlag     = false;

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
        $this->_reviewStoreTable = Mage::getSingleton('core/resource')->getTableName('mtreview/review_store');
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
                array('rdt.title','rdt.nickname', 'rdt.detail', 'rdt.customer_id'));
        return $this;
    }

    /**
     * Add stores data
     *
     */
    public function addStoreData()
    {
        $this->_addStoreDataFlag = true;
        return $this;
    }

    /**
     * Action after load
     *
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->_addStoreDataFlag) {
            $this->_addStoreData();
        }
        return $this;
    }

    /**
     * Add store data
     *
     */
    protected function _addStoreData()
    {
        $adapter = $this->getConnection();
        $reviewsIds = $this->getColumnValues('review_id');
        $storesToReviews = array();
        if (count($reviewsIds)>0) {
            $reviewIdCondition = $this->_getConditionSql('review_id', array('in' => $reviewsIds));
            $storeIdCondition = $this->_getConditionSql('store_id', array('gt' => 0));
            $select = $adapter->select()
                ->from($this->_reviewStoreTable)
                ->where($reviewIdCondition)
                ->where($storeIdCondition);
            $result = $adapter->fetchAll($select);
            foreach ($result as $row) {
                if (!isset($storesToReviews[$row['review_id']])) {
                    $storesToReviews[$row['review_id']] = array();
                }
                $storesToReviews[$row['review_id']][] = $row['store_id'];
            }
        }

        foreach ($this as $item) {
            if (isset($storesToReviews[$item->getReviewId()])) {
                $item->setData('stores', $storesToReviews[$item->getReviewId()]);
            } else {
                $item->setData('stores', array());
            }

        }
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

    /**
     * Adds specific store id into array
     *
     * @param array $storeId
     */
    /**
     * Add store filter
     *
     * @param int|array $storeId
     * @return Mage_Review_Model_Resource_Review_Collection
     */
    public function addStoreFilter($storeId)
    {
        $inCond = $this->getConnection()->prepareSqlCondition('store.store_id', array('in' => $storeId));
        $this->getSelect()->join(array('store'=>$this->_reviewStoreTable),
            'main_table.review_id=store.review_id',
            array());
        $this->getSelect()->where($inCond);
        return $this;
    }

}
