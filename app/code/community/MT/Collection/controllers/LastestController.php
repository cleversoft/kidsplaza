<?php
/**
 * @category    MT
 * @package     MT_Collection
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Collection_LastestController extends Mage_Core_Controller_Front_Action{
    public function indexAction(){
        $this->loadLayout()->renderLayout();
    }
}