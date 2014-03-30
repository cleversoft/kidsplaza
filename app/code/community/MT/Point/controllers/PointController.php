<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_PointController extends Mage_Core_Controller_Front_Action{
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession(){
        return Mage::getSingleton('customer/session');
    }

    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch(){
        parent::preDispatch();

        if (!$this->_getSession()->isLoggedIn()){
            $this->_redirect('customer/account/login');
            return;
        }
    }

    public function indexAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
}