<?php
/**
 * @category    MT
 * @package     MT_RevSlider
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_RevSlider_Block_Adminhtml_Widget_Grid_Column_Renderer_Slide_Title extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
    public function _getValue(Varien_Object $row){
        $params = Mage::helper('core')->jsonDecode($row->getParams());
        if (isset($params['title'])) return $params['title'];
        return '';
    }
}