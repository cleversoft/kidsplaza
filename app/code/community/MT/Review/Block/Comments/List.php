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
                    ->addStoreFilter(Mage::app()->getStore()->getId())
                    ->setDateOrder('DESC');
        return $collection;
    }

    public function getTimeFormat($createdDate)
    {
        $helper = Mage::helper('mtreview');
        return $helper->renderFormatTime($createdDate);
    }

    public function getFormatDate($date)
    {
        return Mage::getModel('core/date')->date('d/m/Y', strtotime($date));
    }
}
