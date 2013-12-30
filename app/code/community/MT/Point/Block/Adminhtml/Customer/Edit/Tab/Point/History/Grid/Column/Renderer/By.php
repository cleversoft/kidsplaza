<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_Block_Adminhtml_Customer_Edit_Tab_Point_History_Grid_Column_Renderer_By extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract{
    public function _getValue(Varien_Object $row){
        if ($row->getBy()){
            $user = Mage::getModel('admin/user')->load($row->getBy());
            return $user->getId() ? $user->getUsername() : '';
        }
    }
}
