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
    public function toOptionArray()
    {
        return array(
            array('value'=>'created_at','label'=>'Date'),
            array('value'=>'rating','label'=>'Average rating'),
            array('value'=>'helpfulness','label'=>'Helpfulness')
        );
    }
}

