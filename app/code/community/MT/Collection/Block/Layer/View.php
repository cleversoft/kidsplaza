<?php
/**
 * @category    MT
 * @package     MT_Collection
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Collection_Block_Layer_View extends MT_Filter_Block_Catalog_Layer_View{
    public function _construct(){
        parent::_construct();
        Mage::register('current_layer', $this->getLayer(), true);
    }

    public function getLayer(){
        return Mage::getSingleton('mtcollection/layer');
    }
}