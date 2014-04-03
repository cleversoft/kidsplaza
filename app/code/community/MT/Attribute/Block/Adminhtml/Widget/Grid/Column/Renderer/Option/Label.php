<?php
/**
 * @category    MT
 * @package     MT_Attribute
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Attribute_Block_Adminhtml_Widget_Grid_Column_Renderer_Option_Label
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function _getValue(Varien_Object $row){
        return $row->getValue();
    }
}