<?php
/**
 * @category    MT
 * @package     KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class AW_Blog_Block_Widget_Grid_Column_Renderer_Thumb extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
    public function _getValue(Varien_Object $row){
        $thumb = $row->getData('thumb');
        if ($thumb){
            return sprintf('<img src="%s" width="100"/>', strpos($thumb, 'http') === 0 ? $thumb : Mage::getBaseUrl('media') . $thumb);
        }
    }
}