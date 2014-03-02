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
class MT_ProductQuestions_Adminhtml_QuestionsController extends Mage_Adminhtml_Controller_Action
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
            ->_title(Mage::helper('productquestions')->__('Manage Questions'))
            ->_addBreadcrumb(Mage::helper('productquestions')->__('Manage Questions'), Mage::helper('productquestions')->__('Manage Questions'));
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('productquestions/adminhtml_questions'));
        $this->renderLayout();
    }

    /**
     * Display the edit/add form
     *
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('productquestions/productquestions');
        $this->loadLayout();
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productquestions')->__('This questions no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }
        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        Mage::register('productquestions_questions', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb($id ? Mage::helper('productquestions')->__('Edit Questions') : '', $id ? Mage::helper('productquestions')->__('Edit Questions') : '');
        $this->_setActiveMenu('mt/productquestions');
        $this->_addContent($this->getLayout()->createBlock('productquestions/adminhtml_questions_edit'));
        $this->renderLayout();
    }

    /**
     * Reply action
     */
    public function replyAction()
    {
        $this->_forward('edit');
    }

    /**
     * Save action
     */
    public function saveAction(){
        // check if data sent
        $data = $this->getRequest()->getPost();
        if ($data && ($questionId = $this->getRequest()->getParam('id'))) {
            // try to save it
            try {
                $data['question_store_ids'] = implode(',', $data['question_store_ids']);
                $locale = Mage::app()->getLocale();
                $urlKey = $data['identifier'] ?
                    Mage::getSingleton('catalog/product/url')->formatUrlKey($data['identifier']) :
                    Mage::getSingleton('catalog/product/url')->formatUrlKey($data['question_text']) ;

                $model = Mage::getModel('productquestions/productquestions')
                    ->setData($data)
                    ->setQuestionDate($locale->date(
                            $data['question_date'], $locale->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT), null, false
                        )
                            ->addTime(substr($data['question_datetime'], 10))
                            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
                    )
                    ->setIdentifier($urlKey)
                    ->setId($questionId)
                    ->save();
                // display success message
                $this->_getSession()->addSuccess($this->__('Questions was saved'));
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                Mage::logException($e);
            }
            // check if 'Save and Continue'
            if ($this->getRequest()->getParam('back') && $model->getQuestionId()) {
                $this->_redirect('*/*/edit', array('id' => $model->getQuestionId()));
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
                $model = Mage::getModel('productquestions/productquestions');
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
        $questionIds = $this->getRequest()->getParam('productquestions');
        if(!is_array($questionIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($questionIds as $questionId) {
                    // init model and delete
                    $model = Mage::getModel('productquestions/productquestions')->load($questionId);
                    $model->delete();
                    $urlQuestionParam = MT_ProductQuestions_Helper_Data::QUESTIONS_URI_PARAM;
                    $id_path = "{$urlQuestionParam}/{$questionId}";
                    $rewrite = Mage::getModel('core/url_rewrite')->loadByIdPath($id_path);
                    $rewrite->delete();
                }
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($questionIds)
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
        $questionIds = $this->getRequest()->getParam('productquestions');
        if(!is_array($questionIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($questionIds as $questionId) {
                    $model = Mage::getSingleton('productquestions/productquestions');
                    $model->load($questionId)
                          ->setQuestionStatus($this->getRequest()->getParam('question_status'))
                          ->setIsMassupdate(true)
                          ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($questionIds))
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