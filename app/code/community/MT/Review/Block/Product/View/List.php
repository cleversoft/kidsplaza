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
                'orders'        => array('created_at' => $this->__('Created At'), 'helpfulness' => $this->__('Helpfulness'), 'rating' => $this->__('Average rating')),
                'default_order' => 'created_at',
                'dir'           => 'desc',
                'limits'        => $helper->confItemsReviewPageCount(),
                'method'        => 'getReviewsCollection'
            )
        );
        $this->getReviewsCollection()->load()->addRateVotes();
        return parent::_beforeToHtml();
    }

    public function getReviewUrl($id)
    {
        return Mage::getUrl('*/*/view', array('id' => $id));
    }
}
