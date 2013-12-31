<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_Block_Customer_Point extends Mage_Core_Block_Template{
    protected function _construct(){
        parent::_construct();
        $this->setTemplate('mt/point/customer/point.phtml');
    }

    public function getTitle(){
        return $this->__('My Points');
    }

    public function getPoint(){
        return Mage::getModel('mtpoint/point')->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer());
    }
}