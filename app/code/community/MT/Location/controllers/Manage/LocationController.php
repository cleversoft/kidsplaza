<?php
/**
 * @category    MT
 * @package     MT_Location
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Location_Manage_LocationController extends Mage_Adminhtml_Controller_Action{
    protected function _initAction(){
        $this->loadLayout()->_setActiveMenu('mt');
        return $this;
    }

    public function indexAction(){
        $this->_initAction();
        $this->_title(Mage::helper('mtlocation')->__('Location'));
        $this->renderLayout();
    }

    public function newAction(){
        $this->_forward('edit');
    }

    public function editAction(){
        $id = $this->getRequest()->getParam('id');
        $location = Mage::getModel('mtlocation/location')->load($id);
        Mage::register('mtlocation', $location);
        $this->_initAction();
        $this->renderLayout();
    }

    public function saveAction(){
        if ($this->_validateFormKey() && $this->getRequest()->isPost()){
            $data = $this->getRequest()->getPost();
            $location = Mage::getModel('mtlocation/location');
            if (isset($data['id']) && $data['id']){
                $location->load((int)$data['id']);
            }else{
                unset($data['id']);
            }
            $location->setData($data);
            try{
                $location->save();
                $this->_getSession()->addSuccess($this->__('Location save successfully'));
                return $this->_redirect('*/*/index');
            }catch (Exception $e){
                $this->_getSession()->addError($this->__('Try again!'));
                Mage::logException($e);
            }
        }
    }

    public function deleteAction(){
        if ($this->_validateFormKey()) {
            $id = $this->getRequest()->getParam('id');
            $location = Mage::getModel('mtlocation/location')->load($id);
            if ($location->getId()) {
                $location->delete();
                $this->_getSession()->addSuccess($this->__('Location deleted sucessfully'));
            }
        }
        return $this->_redirect('*/*/index');
    }
}