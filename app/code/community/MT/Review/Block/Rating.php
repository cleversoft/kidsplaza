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
class MT_Review_Block_Rating extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mt/review/rating.phtml');
    }

    protected function _toHtml()
    {
        $entityId = $this->getProduct()->getId();
        if (intval($entityId) <= 0) {
            return '';
        }

        $reviewsCount = Mage::getModel('mtreview/review')
            ->getTotalReviews($entityId, true);
        if ($reviewsCount == 0) {
            #return Mage::helper('rating')->__('Be the first to review this product');
            $this->setTemplate('rating/empty.phtml');
            return parent::_toHtml();
        }

        $ratingCollection = Mage::getModel('rating/rating')
            ->getResourceCollection()
            ->addEntityFilter('product') # TOFIX
            ->setPositionOrder()
            ->setStoreFilter(Mage::app()->getStore()->getId())
            ->addRatingPerStoreName(Mage::app()->getStore()->getId())
            ->load();

        if ($entityId) {
            //filter store ratting
            //$ratingCollection->addEntitySummaryToItem($entityId, Mage::app()->getStore()->getId());
            $ratingCollection->addEntitySummaryToItem($entityId, 0);
        }

        $this->assign('collection', $ratingCollection);
        return parent::_toHtml();
    }

    public function getProduct()
    {
        return Mage::registry('current_product');
    }
}
