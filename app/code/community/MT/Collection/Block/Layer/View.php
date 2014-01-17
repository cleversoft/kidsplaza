<?php
/**
 * @category    MT
 * @package     MT_Collection
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Collection_Block_Layer_View extends Mage_Catalog_Block_Layer_View{
    public function _construct(){
        parent::_construct();
        Mage::register('current_layer', $this->getLayer());
    }

    public function getLayer(){
        return Mage::getSingleton('mtcollection/layer');
    }
}