<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_Review
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */
class MT_Review_Adminhtml_CommentsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {

        $this->loadLayout();
        $this->_setActiveMenu('mt/review');
        $this->_addContent($this->getLayout()->createBlock('mtreview/adminhtml_comments_main'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('mtreview/comment')->load($id);
        $dataReview = Mage::getModel('mtreview/review');
        if (!$model->getId()){
            $this->_getSession()->addError(Mage::helper('mtpoint')->__('Comment not avaiable!'));
            $this->_redirect('*/*/index');
            return;
        }
        $dataReview->load($model->getReviewId());
        if (! $dataReview->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mtreview')->__('This review no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $model->setTitle($dataReview->getTitle());
        $model->setId($dataReview->getReviewId());
        $model->setDetail($dataReview->getDetail());
        $model->setCreatedDatetime(date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())));
        $this->_title($this->__('Edit Comment'));

        $this->loadLayout();
        Mage::register('review_data',$model);
        $this->_setActiveMenu('mt/review');
        $this->_addContent($this->getLayout()->createBlock('mtreview/adminhtml_comments_edit'));

        $this->renderLayout();
    }

    /**
     * Answer action
     */
    public function answerAction()
    {
        $this->_title($this->__('Answers'));
        $id = $this->getRequest()->getParam('rid');
        $user = Mage::getSingleton('admin/session')->getUser();
        $model = Mage::getModel('mtreview/review');
        $dataReview = Mage::getModel('mtreview/review');
        $this->_title($this->__('Edit Comment'));
        $this->loadLayout();
        if ($id) {
            $dataReview->load($id);
            if (! $dataReview->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mtreview')->__('This review no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            $data = array(
                'review_id'=>$id,
                'store_id'=>$dataReview->getStoreId(),
                'title'=>$dataReview->getTitle(),
                'detail'=>$dataReview->getDetail(),
                'customer_name'=>$user->getFirstname(),
                'customer_id'=>$user->getUserId(),
                'author_type'=>'admin',
                'created_datetime'=>date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()))
            );
        }
        $model->setData($data);
        Mage::register('review_data',$model);
        $this->_setActiveMenu('mt/mtreview');
        $this->_addContent($this->getLayout()->createBlock('mtreview/adminhtml_comments_edit'));
        $this->renderLayout();
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
                $locale = Mage::app()->getLocale();
                $model = Mage::getModel('mtreview/comment')
                    ->setData($data)
                    ->setCreatedAt($locale->date(
                            $data['created_at'], $locale->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT), null, false
                        )
                            ->addTime(substr($data['created_datetime'], 10))
                            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
                    )
                    ->setId($this->getRequest()->getParam('id'))
                    ->save();
                // display success message
                $this->_getSession()->addSuccess($this->__('Review answer was saved'));
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                Mage::logException($e);
            }
        }
        else {
            // display error message
            $this->_getSession()->addError($this->__('There was no data to save'));
        }
        // go to grid
        $this->_redirect('*/*');
    }

    public function deleteAction()
    {
        $commentId   = $this->getRequest()->getParam('id', false);
        $session    = Mage::getSingleton('adminhtml/session');

        try {
            Mage::getModel('mtreview/comment')->setId($commentId)
                ->delete();

            $session->addSuccess(Mage::helper('mtreview')->__('The comment has been deleted'));
            $this->getResponse()->setRedirect($this->getUrl('*/*/'));
            return;
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e){
            $session->addException($e, Mage::helper('mtreview')->__('An error occurred while deleting this comment.'));
        }

        $this->_redirect('*/*/edit/',array('id'=>$commentId));
    }

    public function massDeleteAction()
    {
        $commentsIds = $this->getRequest()->getParam('comments');
        $session    = Mage::getSingleton('adminhtml/session');

        if(!is_array($commentsIds)) {
            $session->addError(Mage::helper('adminhtml')->__('Please select comment(s).'));
        } else {
            try {
                foreach ($commentsIds as $commentId) {
                    $model = Mage::getModel('mtreview/comment')->load($commentId);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) have been deleted.', count($commentsIds))
                );
            } catch (Mage_Core_Exception $e) {
                $session->addError($e->getMessage());
            } catch (Exception $e){
                $session->addException($e, Mage::helper('adminhtml')->__('An error occurred while deleting record(s).'));
            }
        }

        $this->_redirect('*/*/');
    }
}
