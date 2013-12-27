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
class MT_Review_Block_Reply extends Mage_Core_Block_Template
{
    protected $_reviewId;

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

    public function getReplyUrl()
    {
        if ( $this->_reviewId )
        {
            return Mage::getUrl('mtreview/review/reply/');
        }
        else
        {
            return '#';
        }
    }
}
