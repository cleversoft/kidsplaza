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

class MT_Review_Model_System_Config_Source_Ordering_Sorter
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'desc',
                'label' => Mage::helper('adminhtml')->__('Newest first'),
            ),
            array(
                'value' => 'asc',
                'label' => Mage::helper('adminhtml')->__('Older first'),
            ),
        );
    }
}