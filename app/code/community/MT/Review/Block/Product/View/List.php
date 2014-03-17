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
class MT_Review_Block_Product_View_List extends MT_Review_Block_Product_View
{
    protected $_forceHasOptions = false;

    public function getProductId()
    {
        return Mage::registry('product')->getId();
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

    }

    protected function _beforeToHtml()
    {
        $helper = Mage::helper('mtreview');
        Mage::helper('mtreview/toolbar')->create(
            $this,
            array(
                'orders'        => MT_Review_Model_System_Config_Source_Ordering_Items::toShortOptionArray(),
                'default_order' => 'created_at',
                'dir'           => 'desc',
                'limits'        => $helper->confItemsReviewPageCount(),
                'method'        => 'getReviewsCollection'
            )
        );
        $this->getReviewsCollection()->load()->addRateVotes();
        return parent::_beforeToHtml();
    }

    public function getTotalComments($reviewId)
    {
        $comments = Mage::getModel('mtreview/comment')->getCollection()
            ->addReviewFilter($reviewId)
            ->addStoreFilter(Mage::app()->getStore()->getId());
        return count($comments);
    }

    public function showReport()
    {
        $helper = Mage::helper('mtreview');
        if( $helper->confAllowOnlyLoggedToReport() )
        {
            return ($helper->confShowReport() );
        }
        else
        {
            return ($helper->isUserLogged()
                && $helper->confShowReport() );
        }
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

    public function getReviewUrl($id)
    {
        return Mage::getUrl('*/*/view', array('id' => $id));
    }
}
