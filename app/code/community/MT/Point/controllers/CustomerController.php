<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_CustomerController extends Mage_Adminhtml_Controller_Action{
    public function historyAction(){
        $customerId = $this->getRequest()->getParam('id');
        $grid = $this->getLayout()->createBlock('mtpoint/adminhtml_customer_edit_tab_point_history', '',
            array('customer_id', $customerId)
        );
        $this->getResponse()->setBody($grid->toHtml());
    }

    public function historyGridAction(){
        $grid = $this->getLayout()->createBlock('mtpoint/adminhtml_customer_edit_tab_point_history_grid');
        $this->getResponse()->setBody($grid->toHtml());
    }
}