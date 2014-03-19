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
class MT_Review_Block_Helper extends Mage_Review_Block_Helper
{
    protected $_availableTemplates = array(
        'default'   => 'mt/review/helper/summary.phtml',
        'short'     => 'mt/review/helper/summary_short.phtml',
        'mini'      => 'mt/review/helper/summary_mini.phtml'
    );

    public function getReviewsUrl()
    {
        return $this->getProduct()->getProductUrl().'#customer-reviews';
    }

    public function getSummaryHtml($product, $templateType, $displayIfNoReviews)
    {
        // pick template among available
        if (empty($this->_availableTemplates[$templateType])) {
            $templateType = 'default';
        }
        $this->setTemplate($this->_availableTemplates[$templateType]);

        $this->setDisplayIfEmpty($displayIfNoReviews);

        //if (!$product->getRatingSummary()) {
            Mage::getModel('mtreview/review')
                ->getEntitySummary($product, Mage::app()->getStore()->getId());
        //}
        $this->setProduct($product);

        return $this->toHtml();
    }

    public function getReviewsCount()
    {
        return $this->getProduct()->getRatingSummary()->getReviewsCount();
    }

}
