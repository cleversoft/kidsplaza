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
