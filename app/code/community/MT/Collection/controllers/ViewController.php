<?php
/**
 * @category    MT
 * @package     MT_Collection
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Collection_ViewController extends Mage_Core_Controller_Front_Action{
    public function lastestAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Lastest Products'));
        Mage::register('current_collection', 'lastest');
        $this->renderLayout();
    }

    public function bestsellerAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Bestseller Products'));
        Mage::register('current_collection', 'bestseller');
        $this->renderLayout();
    }
}