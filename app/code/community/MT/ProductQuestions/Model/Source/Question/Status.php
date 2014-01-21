<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_ProductQuestions
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */
Class MT_ProductQuestions_Model_Source_Question_Status extends Mage_Core_Model_Abstract {

    const STATUS_PUBLIC     = 1;
    const STATUS_PRIVATE    = 2;

    public static function toShortOptionArray()
    {
        return array(
            self::STATUS_PUBLIC    => Mage::helper('productquestions')->__('Public'),
            self::STATUS_PRIVATE   => Mage::helper('productquestions')->__('Private')
        );
    }

    public static function toOptionArray()
    {
        $res = array();

        foreach(self::toShortOptionArray() as $key => $value)
            $res[] = array(
                'value' => $key,
                'label' => $value);

        return $res;
    }

}