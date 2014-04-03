<?php
/**
 * @category    MT
 * @package     MT_Attribute
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Attribute_Catalog_Product_AttributeController extends Mage_Adminhtml_Controller_Action{
    public function indexAction(){
        $this->loadLayout();
        $this->renderLayout();
    }

    public function editAction(){
        $optionId = $this->getRequest()->getParam('option_id');
        $attributeId = $this->getRequest()->getParam('attribute_id');
        if (is_numeric($optionId) && is_numeric($attributeId)) {
            $optionsModel = Mage::getResourceModel('eav/entity_attribute_option_collection');
            $optionsModel->setAttributeFilter($attributeId);
            $optionsModel->setStoreFilter();
            $optionsModel->addFieldToFilter('main_table.option_id', array('eq' => $optionId));
            Mage::register('attribute_option', $optionsModel->getFirstItem());
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _goBack(){
        return $this->_redirect('*/*/index');
    }

    public function saveAction(){
        if (!$this->getRequest()->isPost()) $this->_goBack();
        if (!$this->_validateFormKey()) $this->_goBack();
        $data = $this->getRequest()->getPost();
        var_dump($data);
        if (!isset($data['option_id']) && !$data['option_id']) $this->_goBack();
        $optionModel = Mage::getModel('eav/entity_attribute_option')->load($data['option_id']);
        if ($optionModel->getId()){
            $optionModel->setData('description', $data['description']);
            $optionModel->save();
        }
        $this->_goBack();
    }
}