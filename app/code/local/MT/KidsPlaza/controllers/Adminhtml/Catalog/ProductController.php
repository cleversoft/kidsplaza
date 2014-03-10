<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
require_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';
class MT_KidsPlaza_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController{
    public function videoTabAction(){
        $this->_initProduct();
        $this->loadLayout();
        $this->renderLayout();
    }
}