<?php
 /**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_PhpStorm
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */
class MT_ProductQuestions_Adminhtml_CategoriesController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Init actions
     *
     * @return Mage_Adminhtml_Cms_PageController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('mt/productquestions')
            ->_title(Mage::helper('productquestions')->__('Manage Categories'))
            ->_addBreadcrumb(Mage::helper('productquestions')->__('Manage Categories'), Mage::helper('productquestions')->__('Manage Categories'));
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('productquestions/adminhtml_categories'));
        $this->renderLayout();
    }

    /**
     * Display the edit/add form
     *
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('productquestions/categories');
        $this->loadLayout();
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productquestions')->__('This categories no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        Mage::register('productquestions_categories', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb($id ? Mage::helper('productquestions')->__('Edit Categories') : '', $id ? Mage::helper('productquestions')->__('Edit Categories') : '');
        $this->_setActiveMenu('mt/productquestions');
        $this->_addContent($this->getLayout()->createBlock('productquestions/adminhtml_categories_edit'));
        $this->renderLayout();
    }

    /**
     * Reply action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Save action
     */
    public function saveAction(){
        // check if data sent
        $data = $this->getRequest()->getPost();
        if ($data) {
            // try to save it
            try {
                $data['stores'] = implode(',', $data['stores']);
                $urlKey = $data['identifier'] ?
                    Mage::getSingleton('catalog/product/url')->formatUrlKey($data['identifier']) :
                    Mage::getSingleton('catalog/product/url')->formatUrlKey($data['name']) ;
                $model = Mage::getModel('productquestions/categories')
                    ->setData($data)
                    ->setIdentifier($urlKey)
                    ->setId($this->getRequest()->getParam('id'))
                    ->save();
                // display success message
                $this->_getSession()->addSuccess($this->__('Category was saved'));
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                Mage::logException($e);
            }
            // check if 'Save and Continue'
            if ($this->getRequest()->getParam('back') && $model->getCatId()) {
                $this->_redirect('*/*/edit', array('id' => $model->getCatId()));
                return;
            }
        }
        else {
            // display error message
            $this->_getSession()->addError($this->__('There was no data to save'));
        }
        // go to grid
        $this->_redirect('*/*');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                // init model and delete
                $model = Mage::getModel('productquestions/categories');
                $model->load($id);
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('productquestions')->__('The question has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // go back to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productquestions')->__('Unable to find a question to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }
    /**
     * Mass Delete action
     */
    public function massDeleteAction() {
        $catIds = $this->getRequest()->getParam('productquestions');
        if(!is_array($catIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($catIds as $catId) {
                    // init model and delete
                    $model = Mage::getModel('productquestions/categories')->load($catId);
                    $model->delete();
                    $urlCatParam = MT_ProductQuestions_Helper_Data::CATEGORY_URI_PARAM;
                    $id_path = "{$urlCatParam}/{$catId}";
                    $Collections = Mage::getModel('core/url_rewrite')->getCollection()
                        ->addFilter('id_path', $id_path);
                    foreach($Collections as $Collection){
                        $mainUrlRewrite = Mage::getModel('core/url_rewrite')->loadByIdPath($Collection->getIdPath());
                        $mainUrlRewrite->delete();
                    }
                }
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($catId)
                    )
                );
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        // go to grid
        $this->_redirect('*/*/index');
    }

    /**
     * Mass Status action
     */
    public function massStatusAction()
    {
        $catIds = $this->getRequest()->getParam('productquestions');
        if(!is_array($catIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($catIds as $catId) {
                    $model = Mage::getSingleton('productquestions/categories');
                    $model->load($catId)
                          ->setStatus($this->getRequest()->getParam('status'))
                          ->setIsMassupdate(true)
                          ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($catIds))
                );
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        // go to grid
        $this->_redirect('*/*/index');
    }
}