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
class MT_Review_Model_System_Config_Source_Ordering_Items
{

    const CREATED_AT = 'created_at';
    const RATING = 'rating';
    const HELPFULNESS = 'helpfulness';

    public static function toShortOptionArray()
    {
        return array(
            self::CREATED_AT    => Mage::helper('mtreview')->__('Date'),
            self::RATING   => Mage::helper('mtreview')->__('Average rating'),
            self::HELPFULNESS   => Mage::helper('mtreview')->__('Helpfulness')
        );
    }
}

