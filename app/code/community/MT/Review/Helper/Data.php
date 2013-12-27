<?php
 /**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_PhpStorm
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */
Class MT_Review_Helper_Data extends Mage_Core_Helper_Abstract {

    protected $_reviewOptions = array();

    /**
     * Get config allow to vote
     *
     * @return tring
     */
    public function confAllowOnlyLoggedToVote()
    {
        return Mage::getStoreConfig('mtreview/access_options/allow_only_logged_vote');
    }

    /**
     * Get config allow review to logged
     *
     * @return tring
     */
    public function confAllowOnlyLogged()
    {
        return Mage::getStoreConfig('mtreview/access_options/allow_only_logged');
    }

    /**
     * Get config allow vote to logged
     *
     * @return tring
     */
    public function confAllowOnlyLoggedToReport()
    {
        return Mage::getStoreConfig('mtreview/access_options/allow_only_logged_report');
    }

    /**
     * Get config allow reply to logged
     *
     * @return tring
     */
    public function confAllowOnlyLoggedToReply()
    {
        return Mage::getStoreConfig('mtreview/access_options/allow_only_logged_reply');
    }

    /**
     * Get config allow to show ordering
     *
     * @return tring
     */
    public function confShowOrdering()
    {
        return Mage::getStoreConfig('mtreview/display_options/show_ordering');
    }

    /**
     * Get config allow to show helpfulness
     *
     * @return tring
     */
    public function confShowHelpfulness()
    {
        return Mage::getStoreConfig('mtreview/display_options/show_helpfulness');
    }

    /**
     * Get config allow to show reply
     *
     * @return tring
     */
    public function confShowReply()
    {
        return Mage::getStoreConfig('mtreview/display_options/show_reply');
    }

    /**
     * Get config allow to show report
     *
     * @return tring
     */
    public function confShowReport()
    {
        return Mage::getStoreConfig('mtreview/display_options/show_report');
    }

    /**
     * Get config allow to show all my review link
     *
     * @return tring
     */
    public function confShowAllMyLinks()
    {
        return Mage::getStoreConfig('mtreview/display_options/show_allmylinks');
    }

    /**
     * Get customer logged
     *
     * @return tring
     */
    public function isUserLogged()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    /**
     * Get conf ordering selected
     *
     * @return array
     */
    public function confOrderingItemsArray()
    {
        return explode(',', Mage::getStoreConfig('mtreview/ordering_options/ordering_items'));
    }
    /**
     * Get conf ordering selected
     *
     * @return array
     */
    public function confItemsReviewPageCount()
    {
        return Mage::getStoreConfig('mtreview/ordering_options/items_per_page');
    }
    /**
     * Get conf default sorter
     *
     * @return array
     */
    public function confDefaultSort()
    {
        return Mage::getStoreConfig('mtreview/ordering_options/default_sorter');
    }

    /**
     * Check customer report
     *
     * @return tring
     */
    public function isReportRegistered($reviewId)
    {
        if($userId = Mage::getSingleton('customer/session')->getCustomer()->getId()){
            $report = Mage::getModel('mtreview/report')->getCollection()
                        ->addCustomerFilter($userId)
                        ->addReviewFilter($reviewId);
            return count($report);
        } else {
            return $this->isMarkedReport($reviewId);
        }

    }

    /**
     * Get review statuses with their codes
     *
     * @return array
     */
    public function getReviewStatuses()
    {
        return array(
            MT_Review_Model_Review::STATUS_APPROVED     => $this->__('Approved'),
            MT_Review_Model_Review::STATUS_PENDING      => $this->__('Pending'),
            MT_Review_Model_Review::STATUS_NOT_APPROVED => $this->__('Not Approved'),
        );
    }

    /**
     * Get review statuses as option array
     *
     * @return array
     */
    public function getReviewStatusesOptionArray()
    {
        $result = array();
        foreach ($this->getReviewStatuses() as $k => $v) {
            $result[] = array('value' => $k, 'label' => $v);
        }

        return $result;
    }

    /**
     * Get review parent with their codes
     *
     * @return array
     */
    public function getReviewParent()
    {
        $model = Mage::getModel('mtreview/review');
        $reviewData = Mage::registry('review_data');
        $collection = $model->getProductCollection();
        $collection->addEntityFilter($reviewData->getEntityPkValue());
        $review_options = array();
        foreach($collection as $review) {
            array_push($review_options, array($review->getReviewId(), $review->getTitle(), $review->getParentReviewId()));
        }
        $this->_reviewOptions[] = array('value' => '0', 'label' => 'No Parent');
        foreach($review_options as $option) {
            if($option[2] == 0 && $reviewData->getParentReviewId() != 0) {
                if($reviewData->getReviewId() != $option[0]){
                    $this->_reviewOptions[] = array('value' => $option[0], 'label' => $option[1]);
                    $this->recursive_options($review_options, 1, $option[0]);
                }
            }
        }
        return $this->_reviewOptions;
    }

    function recursive_options($review_options, $level, $parent)
    {
        $reviewData = Mage::registry('review_data');
        foreach($review_options as $option)
        {
            if($option[2] == $parent)
            {
                $level_string = '';
                for($i = 0; $i < $level; $i++) $level_string .= '- ';
                if($reviewData->getReviewId() != $option[0]){
                    $this->_reviewOptions[] = array('value' => $option[0], 'label' => $level_string . $option[1]);
                    $this->recursive_options($review_options, $level+1, $option[0]);
                }
            }
        }
    }
    /**
     * Get review parent as option array
     *
     * @return array
     */
    public function getReviewParentOptionArray()
    {
        return $this->getReviewParent();
    }

    public function loggedHelpfulness($reviewId)
    {
        $session = Mage::getSingleton('customer/session', array('name'=>'frontend'))->start();
        $reviews = $session->getHelpfulnessReviews();

        if ( count($reviews) )
        {
            $reviews[] = $reviewId;
        }
        else
        {
            $reviews = array($reviewId);
        }
        $session->setHelpfulnessReviews($reviews);

        return $this;
    }

    public function isHelpfulnessLogged($reviewId)
    {
        $session = Mage::getSingleton('customer/session', array('name'=>'frontend'))->start();
        $reviews = $session->getHelpfulnessReviews();
        if ( isset($reviews) )
        {
            return in_array($reviewId, $reviews);
        }
        else
        {
            return false;
        }
    }


    public function getCustomerNicknameById($customerId)
    {
        if ($customerId)
        {
            if ( $customer = Mage::getModel('customer/customer')->load($customerId) )
            {
                return $customer->getFirstname()." ".$customer->getLastname();
            }
            return null;
        }
        else
        {
            return null;
        }
    }

    public function replyReview($reviewId, $comment,$customerId = null)
    {
        if ($customerId === null)
        {
            $customerName = $this->__('Guest');
        }
        else
        {
            $customerName = $this->getCustomerNicknameById($customerId);
        }
        $reply = Mage::getModel('mtreview/comment');
        $reply->setReviewId($reviewId)->setCustomerName($customerName)
            ->setCustomerId($customerId)
            ->setComments($comment)
            ->setStoreId( Mage::helper('core')->getStoreId()  )
            ->setCreatedAt(now())
            ->save();

        return $reply;
    }

    public function addReport($reviewId, $customerId = null)
    {
        if ($customerId === null)
        {
            $customerName = $this->__('Guest');
        }
        else
        {
            $customerName = $this->getCustomerNicknameById($customerId);
        }
        $report = Mage::getModel('mtreview/report');
        $report->setReviewId($reviewId)->setCustomerName($customerName)
            ->setCustomerId($customerId)
            ->setStoreId( Mage::helper('core')->getStoreId()  )
            ->setReportAt(now())
            ->save();

        return $this;
    }

    public function markReport($reviewId)
    {
        $cookies = Mage::getModel('core/cookie');
        $report = $cookies->get('report');
        $report = explode(',',$report);
        if(!in_array($reviewId, $report)){
            $report = implode(',',$report);
            $cookies->set('report',$report.($report ? ',' : '').$reviewId, '86400');
        }
    }

    public function isMarkedReport($reviewId)
    {
        $cookies = Mage::getModel('core/cookie');
        $abuse = explode(',',$cookies->get('report'));

        return in_array($reviewId, $abuse);
    }

    public function yesHelpfulness($reviewId)
    {
        $helpful = Mage::getModel('mtreview/helpfulness');
        $helpful->setReviewId($reviewId)
                ->setValue(1)
                ->save();
        return $this;
    }

    public function noHelpfulness($reviewId)
    {
        $helpful = Mage::getModel('mtreview/helpfulness');
        $helpful->setReviewId($reviewId)
            ->setValue(0)
            ->save();
        return $this;
    }

}