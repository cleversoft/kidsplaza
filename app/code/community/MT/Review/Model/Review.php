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
class MT_Review_Model_Review extends Mage_Core_Model_Abstract {

    /**
     * Event prefix for observer
     *
     * @var string
     */
    protected $_eventPrefix = 'review';

    /**
     * @deprecated after 1.3.2.4
     *
     */
    const ENTITY_PRODUCT = 1;

    /**
     * Review entity codes
     *
     */
    const ENTITY_PRODUCT_CODE   = 'product';
    const ENTITY_CUSTOMER_CODE  = 'customer';
    const ENTITY_CATEGORY_CODE  = 'category';

    const STATUS_APPROVED       = 1;
    const STATUS_PENDING        = 2;
    const STATUS_NOT_APPROVED   = 3;

    protected function _construct()
    {
        $this->_init('mtreview/review');
    }

    public function getProductCollection()
    {
        return Mage::getResourceModel('mtreview/review_product_collection');
    }

    public function getStatusCollection()
    {
        return Mage::getResourceModel('mtreview/review_status_collection');
    }

    public function getTotalReviews($entityPkValue, $approvedOnly=false, $storeId=0)
    {
        return $this->getResource()->getTotalReviews($entityPkValue, $approvedOnly, $storeId);
    }

    public function aggregate()
    {
        $this->getResource()->aggregate($this);
        return $this;
    }

    public function getEntitySummary($product, $storeId=0)
    {
        $summaryData = Mage::getModel('review/review_summary')
            ->setStoreId($storeId)
            ->load($product->getId());
        $summary = new Varien_Object();
        $summary->setData($summaryData->getData());
        $product->setRatingSummary($summary);
    }

    public function getPendingStatus()
    {
        return self::STATUS_PENDING;
    }

    public function getReviewUrl()
    {
        return Mage::getUrl('mtreview/product/view', array('id' => $this->getReviewId()));
    }

    public function validate()
    {
        $errors = array();

        /*if (!Zend_Validate::is($this->getTitle(), 'NotEmpty')) {
            $errors[] = Mage::helper('review')->__('Review summary can\'t be empty');
        }*/

        if (!Zend_Validate::is($this->getNickname(), 'NotEmpty')) {
            $errors[] = Mage::helper('review')->__('Nickname can\'t be empty');
        }

        if (!Zend_Validate::is($this->getDetail(), 'NotEmpty')) {
            $errors[] = Mage::helper('review')->__('Review can\'t be empty');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Check if current review approved or not
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->getStatusId() == self::STATUS_APPROVED;
    }

    /**
     * Check if current review available on passed store
     *
     * @param int|Mage_Core_Model_Store $store
     * @return bool
     */
    public function isAvailableOnStore($store = null)
    {
        $store = Mage::app()->getStore($store);
        if ($store) {
            return in_array($store->getId(), (array)$this->getStores());
        }

        return false;
    }

    /**
     * Get review entity type id by code
     *
     * @param string $entityCode
     * @return int|bool
     */
    public function getEntityIdByCode($entityCode)
    {
        return $this->getResource()->getEntityIdByCode($entityCode);
    }

}