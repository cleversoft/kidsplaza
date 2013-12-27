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
class MT_Review_Adminhtml_ReportController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->_title($this->__('Reviews Report'));

        $this->loadLayout();
        $this->_setActiveMenu('mt/review');
        $this->_addContent($this->getLayout()->createBlock('mtreview/adminhtml_report_report'));

        $this->renderLayout();
    }
    
    public function deleteAbuseAction()
    {
        if ( $id = $this->getRequest()->getParam('id') )
        {
            try
            {
                $model = Mage::getModel('mtreview/report');
                $model->setId($id)->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')
                                                       ->__('Report was successfully deleted') );
            }
            catch ( Exception $e )
            {
                Mage::getSingleton('adminhtml/session')->addError( $e->getMessage() );
            }
        }
        $this->_redirect('*/*/');
    }

    public function reportGridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('mtreview/adminhtml_report_grid')->toHtml() );
    }
}
