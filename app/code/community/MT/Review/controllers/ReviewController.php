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
class MT_Review_ReviewController extends Mage_Core_Controller_Front_Action
{
    protected $_totalHelpful;
    /**
     * helpful review
     *
     */
    public function helpfulAction(){
        $val = $this->getRequest()->getParam('val');
        $reviewId   = $this->getRequest()->getParam('reviewId');
        if ( Mage::getSingleton('customer/session')->isLoggedIn() )
        {
            $customerId = Mage::getSingleton('customer/session')->getId();
        }else{
            $customerId = null;
        }
        if (!Mage::helper('mtreview')->confAllowOnlyLoggedToVote()
            && !Mage::helper('mtreview')->isUserLogged() )
        {
            return $this->getResponse()->setBody(Zend_Json::encode(array('status'=>'error','message'=>$this->__('You need log in to vote review'))));
        }
        elseif (Mage::helper('mtreview')->isHelpfulnessLogged($reviewId))
        {
            return $this->getResponse()->setBody(Zend_Json::encode(array('status'=>'error','message'=>$this->__('You can vote only once for the same review'))));
        }
        if($val>0){
            $helpful = Mage::helper('mtreview')->yesHelpfulness( $reviewId, $customerId );
            Mage::helper('mtreview')->loggedHelpfulness( $reviewId );
            if ( ( $review = Mage::helper('mtreview')->getTotalReviewHelpfull( $reviewId ) ) !== null )
            {
                $this->_totalHelpful = $this->__('Have %s Helpfulness', $review->getYesCount());
            }else{
                $this->_totalHelpful = 0;
            }
            return $this->getResponse()->setBody(Zend_Json::encode(array('status'=>'success', 'total' => $this->_totalHelpful,'message'=>$this->__('Thank you for your vote.'))));
        }else{
            Mage::helper('mtreview')->noHelpfulness( $reviewId,$customerId );
            Mage::helper('mtreview')->loggedHelpfulness( $reviewId );
            return $this->getResponse()->setBody(Zend_Json::encode(array('status'=>'success','message'=>$this->__('Thank you for your vote.'))));
        }
    }

    /**
     * Report it
     */
    public function reportAction(){
        if ($reviewId = $this->getRequest()->getParam('reviewId'))
        {
            if ( Mage::getSingleton('customer/session')->isLoggedIn() )
            {
                $customerId = Mage::getSingleton('customer/session')->getId();
                Mage::helper('mtreview')->addReport($reviewId, $customerId);
            }
            else
            {
                Mage::helper('mtreview')->addReport($reviewId);
                Mage::helper('mtreview')->markReport($reviewId);
            }
            return $this->getResponse()->setBody(Zend_Json::encode(array('status'=>'success','message'=>$this->__('Reported.'))));
        }
    }

    /**
     * Reply action
     */
    public function replyAction(){
        if ($reviewId = $this->getRequest()->getParam('reviewId'))
        {
            $content = $this->getRequest()->getParam('content');
            if ( Mage::getSingleton('customer/session')->isLoggedIn() )
            {
                $customerId = Mage::getSingleton('customer/session')->getId();
                $reply = Mage::helper('mtreview')->replyReview($reviewId, $content, $customerId);
            }
            else
            {
                $reply = Mage::helper('mtreview')->replyReview($reviewId, $content);
            }
            return $this->getResponse()->setBody(Zend_Json::encode(array('status'=>'success','customer'=>$reply->getCustomerName(),'content'=>$reply->getComments(),'message'=>$this->__('Thank you for reply.'))));
        }
    }
}