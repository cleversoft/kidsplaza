<?php
/**
 * @category    MT
 * @package     MT_RevSlider
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_RevSlider_Helper_Data extends Mage_Core_Helper_Abstract{
    public function getCssHref($data){
        if ($data) return sprintf('http://fonts.googleapis.com/css?family=%s', $data);
    }
}