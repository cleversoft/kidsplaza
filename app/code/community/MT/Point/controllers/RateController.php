<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_RateController extends Mage_Adminhtml_Controller_Action{
    protected function _initAction(){
        $this->loadLayout()->_setActiveMenu('promo/mtpoint');
        return $this;
    }

    public function indexAction(){
        $this->_initAction();
        $this->_title(Mage::helper('mtpoint')->__('Promotions'))->_title(Mage::helper('mtpoint')->__('MT Point Rate'));
        $this->renderLayout();
    }

    public function newAction(){
        $this->_forward('edit');
    }

    public function editAction(){
        $id = $this->getRequest()->getParam('id');
        $rate = Mage::getModel('mtpoint/rate')->load($id);
        Mage::register('mtpoint_rate', $rate);
        $this->_initAction();
        $this->renderLayout();
    }

    public function saveAction(){
        if ($this->getRequest()->isPost()){
            $rates = Mage::getModel('mtpoint/rate')->getCollection()->getSize();
            if ($rates > 0){
                $this->_getSession()->addError($this->__('Only one rate should available'));
                return $this->_redirect('*/*/index');
            }
            $data = $this->getRequest()->getPost();
            $rate = Mage::getModel('mtpoint/rate');
            if (isset($data['id'])) $rate->load((int)$data['id']);
            $rate->setData($data);
            try{
                $rate->save();
                $this->_getSession()->addSuccess($this->__('Rate save successfully'));
                return $this->_redirect('*/*/index');
            }catch (Exception $e){
                $this->_getSession()->addError($this->__('Try again'));
                Mage::logException($e);
            }
        }
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        $rate = Mage::getModel('mtpoint/rate')->load($id);
        if ($rate->getId()) $rate->delete();
        $this->_getSession()->addSuccess($this->__('Rate deleted sucessfully'));
        return $this->_redirect('*/*/index');
    }
}