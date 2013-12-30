<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_Block_Adminhtml_Customer_Edit_Tab_Point_Balance extends Mage_Adminhtml_Block_Template{
    protected function _construct(){
        parent::_construct();
        $this->setTemplate('mt/point/customer/edit/tab/point/balance.phtml');
    }

    protected function _prepareLayout(){
        $this->setChild('grid', $this->getLayout()->createBlock('mtpoint/adminhtml_customer_edit_tab_point_balance_grid'));
        return parent::_prepareLayout();
    }
}