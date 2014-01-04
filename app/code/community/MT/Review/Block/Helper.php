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
        'default' => 'mt/review/helper/summary.phtml',
        'short'   => 'mt/review/helper/summary_short.phtml'
    );

    public function getReviewsUrl()
    {
        return Mage::getUrl('mtreview/product/list', array(
            'id'        => $this->getProduct()->getId(),
            'category'  => $this->getProduct()->getCategoryId()
        ));
    }
}
