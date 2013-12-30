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
class MT_Review_Block_Comments_List extends MT_Review_Block_Product_View
{
    protected $_reviewId;

    public function setReviewId($reviewId)
    {
        $this->_reviewId = $reviewId;
        return $this;
    }

    public function getReviewId()
    {
        return $this->_reviewId;
    }

    public function getCommentsCollection()
    {
        $collection = Mage::getModel('mtreview/comment')->getCollection()
                    ->addReviewFilter($this->_reviewId)
                    ->setDateOrder('DESC');
        return $collection;
    }
}
