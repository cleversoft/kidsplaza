<?php
/**
 * @category    MT
 * @package     MT_RevSlider
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_RevSlider_Model_Mysql4_Animation extends Mage_Core_Model_Mysql4_Abstract{
    public function _construct(){
        $this->_init('revslider/animation', 'id');
    }
}