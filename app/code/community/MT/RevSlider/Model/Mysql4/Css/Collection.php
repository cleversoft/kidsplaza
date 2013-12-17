<?php
/**
 * @category    MT
 * @package     MT_RevSlider
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_RevSlider_Model_Mysql4_Css_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract{
    public function _construct(){
        parent::_construct();
        $this->_init('revslider/css');
    }

    public function toOptionArray(){
        $res = array();
        foreach ($this as $item) {
            $style = $item->getPrettyName();
            $res[] = array(
                'value' => $item->getId(),
                'label' => $style
            );
        }
        return $res;
    }
}