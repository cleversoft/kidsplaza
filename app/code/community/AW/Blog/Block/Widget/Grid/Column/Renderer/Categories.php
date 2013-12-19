<?php
/**
 * @category    MT
 * @package     KidsPlaza
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class AW_Blog_Block_Widget_Grid_Column_Renderer_Categories extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
    public function _getValue(Varien_Object $row){
        $html = array();
        $cats = Mage::getModel('blog/cat')->getCollection()->addPostFilter($row->getId());
        foreach ($cats as $cat){
            $html[] = Mage::helper('core')->escapeHtml($cat->getTitle());
        }
        return implode(', ', $html);
    }
}