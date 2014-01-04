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
class MT_Review_Block_Helpfulness extends Mage_Core_Block_Template
{
    protected $_reviewId;

    protected $_reviewsCollection;

    public function _construct() {
        parent::_construct();
    }

    public function setReviewId($reviewId)
    {
        $this->_reviewId = $reviewId;
        return $this;
    }

    public function getReviewId()
    {
        return $this->_reviewId;
    }

    public function getLinkHelpfulness($helpful = 'none')
    {
        if($helpful=='Yes'){
            return Mage::getUrl('mtreview/review/helpful', array('reviewId' => $this->_reviewId, 'val' => 1));
        }else{
            return Mage::getUrl('mtreview/review/helpful', array('reviewId' => $this->_reviewId, 'val' => 0));
        }
    }

    public function canShowHelpfulnessLink()
    {
        $helper = Mage::helper('mtreview');
        if( $helper->confAllowOnlyLoggedToVote())
            return (!($helper->isCustomerHelpful($this->_reviewId)));
        else
            return ($helper->isUserLogged() && !($helper->isCustomerHelpful($this->_reviewId)));
    }

    public function getCollection()
    {
        if (null === $this->_reviewsCollection) {
            $this->_reviewsCollection = Mage::getModel('mtreview/review')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter(MT_Review_Model_Review::STATUS_APPROVED)
                ->addEntityFilter('product', Mage::registry('product')->getId())
                ->addHelpfulnessSummary();
        }
        return $this->_reviewsCollection;
    }

    public function getFilledReviewById( $reviewId )
    {
        foreach ($this->getCollection() as $review)
        {
            if ( $review->getId() === $reviewId )
            {
                return $review;
            }
        }
        return null;
    }

    public function getYesCount()
    {
        if ( ( $review = $this->getFilledReviewById( $this->_reviewId ) ) !== null )
        {
            return $review->getYesCount();
        }
        return 0;
    }
}
