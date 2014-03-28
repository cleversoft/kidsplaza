<?php
/**
 * @category    MT
 * @package     MT_StockNotify
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_StockNotify_Manage_IndexController extends Mage_Adminhtml_Controller_Action{
    protected function _initAction(){
        $this->loadLayout()->_setActiveMenu('catalog');
        return $this;
    }

    public function indexAction(){
        $this->_initAction();
        $this->_title(Mage::helper('mtstocknotify')->__('Catalog'))->_title(Mage::helper('mtpoint')->__('Stock Notify'));
        $this->renderLayout();
    }

    public function massStatusAction(){
        $ids = $this->getRequest()->getParam('ids');
        $status = $this->getRequest()->getParam('status');
        if (!count($ids) || !$status) $this->_getSession()->addError(Mage::helper('mtstocknotify')->__('Data invalid'));
        $count = 0;
        foreach ($ids as $id){
            if (!is_numeric($id)) continue;
            $notify = Mage::getModel('mtstocknotify/notify')->load($id);
            if (!$notify->getId()) continue;
            $notify->setData('status', $status);
            try{
                $notify->save();
                $count++;
            }catch (Exception $e){
                $this->_getSession()->addError(Mage::helper('mtstocknotify')->__('Change status error for Id: %d', $id));
            }
        }
        $this->_getSession()->addSuccess(Mage::helper('mtstocknotify')->__('Total of %d record(s) have been updated.', $count));
        return $this->_redirect('*/*/index');
    }
}