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
class MT_Review_Block_Adminhtml_Catalog_Product_Edit_Tab_Reviews extends MT_Review_Block_Adminhtml_Review_Grid
{

    /**
     * Hide grid mass action elements
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Reviews
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Determine ajax url for grid refresh
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/mtreview', array('_current' => true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('mtreview/adminhtml_review/edit', array(
            'id' => $row->getReviewId(),
            'productId' => $this->getProductId(),
            'customerId' => $this->getCustomerId(),
            'ret'       => ( Mage::registry('usePendingFilter') ) ? 'pending' : null,
        ));
    }

}