<?php
/**
 * @category    MT
 * @package     MT_Location
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Location_LocationController extends Mage_Core_Controller_Front_Action{
    public function indexAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('mtlocation')->__('Our Locations'));
        $this->renderLayout();
    }
}