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
class MT_Review_Block_Footer extends Mage_Core_Block_Template
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

    public function showReply()
    {
        $helper = Mage::helper('mtreview');
        if($helper->confAllowOnlyLoggedToReply()){
            return ($helper->confShowReply());
        }else{
            return ($helper->isUserLogged() && $helper->confShowReply());
        }
    }

    public function showHelpfulness()
    {
        $helper = Mage::helper('mtreview');
        return $helper->confShowHelpfulness();
    }
}
