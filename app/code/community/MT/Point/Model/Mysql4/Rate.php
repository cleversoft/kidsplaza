<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_Model_Mysql4_Rate extends Mage_Core_Model_Mysql4_Abstract{
    public function _construct(){
        $this->_init('mtpoint/rate', 'id');
    }
}