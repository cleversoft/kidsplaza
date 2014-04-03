<?php
/**
 * @category    MT
 * @package     MT_Attribute
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Attribute_Block_Adminhtml_Catalog_Product_Attribute_Options_Edit extends Mage_Adminhtml_Block_Widget_Form_Container{
    public function __construct(){
        $this->_blockGroup  = 'mtattribute';
        $this->_controller  = 'adminhtml_catalog_product_attribute_options';
        $this->_form        = 'edit';
        parent::__construct();
    }

    public function getHeaderText(){
        $optionModel = Mage::registry('attribute_option');
        if ($optionModel) {
            return Mage::helper('mtattribute')->__('Edit Description for %s', $optionModel->getValue());
        }
    }
}