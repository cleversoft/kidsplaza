<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Model_Observer{
    public function modelSaveAfter($observer){
        $object = $observer->getEvent()->getObject();
        Mage::helper('kidsplaza')->cleanCacheByObject($object);
    }
}