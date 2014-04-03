<?php
/**
 * @category    MT
 * @package     MT_Attribute
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Attribute_Block_Adminhtml_Catalog_Product_Attribute_Options extends Mage_Adminhtml_Block_Widget_Grid_Container{
    public function __construct(){
        $this->_headerText = Mage::helper('mtattribute')->__('Manage Attribute Options');
        $this->_blockGroup = 'mtattribute';
        $this->_controller = 'adminhtml_catalog_product_attribute_options';
        parent::__construct();
        $this->_updateButton('add', 'label', Mage::helper('mtattribute')->__('Edit New Attribute Option'));
    }
}