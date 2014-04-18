<?php
/**
 * @category    MT
 * @package     MT_Filter
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Filter_Model_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item{
    public function isActive(){
        if ($this->getFilter()->getRequestVar() == 'price'){
            if ($this->getFilter()->getRequestValue() && $this->getValue()) {
                list($p1, $p2) = explode('-', $this->getFilter()->getRequestValue());
                list($p3, $p4) = explode('-', $this->getValue());
                if ($p1 == $p3) if ($p2 && $p4 == '') return true;
            }
        }
        return $this->getFilter()->getRequestValue() == $this->getValue();
    }
}