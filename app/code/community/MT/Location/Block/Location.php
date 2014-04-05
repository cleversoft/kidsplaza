<?php
/**
 * @category    MT
 * @package     MT_Location
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Location_Block_Location extends Mage_Core_Block_Template{
    protected function _construct(){
        parent::_construct();
        $this->setTemplate('mt/location/location.phtml');
    }

    public function getLocations(){
        return Mage::getModel('mtlocation/location')->getCollection();
    }
}