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
class MT_KidsPlaza_Model_Catalog_Product_Type
{
    const TYPE_YES   = 1;

    public function getOptionArray()
    {
        return array(
            self::TYPE_YES   => Mage::helper('catalog')->__('Yes')
        );
    }
}

