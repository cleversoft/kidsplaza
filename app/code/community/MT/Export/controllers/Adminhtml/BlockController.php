<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_Export
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */
?>
<?php
class MT_Export_Adminhtml_BlockController extends Mage_Adminhtml_Controller_Action
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
            ->_setActiveMenu('mt/export')
            ->_title(Mage::helper('export')->__('Manage Export Static Block'))
            ->_addBreadcrumb(Mage::helper('export')->__('Manage Export Static Block'), Mage::helper('export')->__('Manage Export Static Block'));
        return $this;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     *  Export page grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName   = 'blocks.xml';
        $blockIds = $this->getRequest()->getParam('block_ids');
        if(!is_array($blockIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                $collection = Mage::getModel('cms/block')->getCollection()->addFieldToFilter('block_id',array('in'=>$blockIds));
                $xml = "<root>\n";
                    $xml.= "<blocks>\n";
                    foreach ($collection as $block) {
                        $xml.= "<cms_block>\n";
                        $xml.= "<title><![CDATA[{$block->getTitle()}]]></title>\n";
                        $xml.= "<identifier><![CDATA[{$block->getIdentifier()}]]></identifier>\n";
                        $xml.= "<content><![CDATA[{$block->getContent()}]]></content>\n";
                        $xml.= "<is_active>{$block->getIsActive()}</is_active>\n";
                        $xml.= "<stores><item>0</item></stores>\n";
                        $xml.= "</cms_block>\n";
                    }
                    $xml.= "</blocks>\n";
                $xml.= "</root>\n";
                $this->_sendUploadResponse($fileName, $xml);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    public function importAction(){
        $this->loadLayout();
        $this->renderLayout();
    }

    public function importPostAction(){
        try{
            $uploader = new Varien_File_Uploader('file');
            $uploader->setAllowedExtensions(array('xml'));
            $uploader->setAllowCreateFolders(true);
            $uploader->setAllowRenameFiles(false);
            $uploader->setFilesDispersion(false);
            $uploader->save('var/tmp/');

            $file = $uploader->getUploadedFileName();
            $path = Mage::getBaseDir('var') . DS . 'tmp' . DS . $file;

            $data = new Zend_Config_Xml($path);
            if (!$data) throw new Exception(Mage::helper('export')->__('File invalid.'));
            if (!$data->blocks || !$data->blocks->cms_block){
                throw new Exception(Mage::helper('export')->__('Data invalid.'));
            }

            foreach ($data->blocks->cms_block as $block){
                $model = Mage::getModel('cms/block');
                /* @var $model Mage_Cms_Model_Block */
                $model->load($block->identifier);
                if ($model->getId()){
                    $model->setTitle($block->title);
                    $model->setContent($block->content);
                    $model->save();
                    $this->_getSession()->addWarning(Mage::helper('export')->__('Updated %s', $model->getTitle()));
                }else{
                    $model->setData(array(
                        'title' => $block->title,
                        'identifier' => $block->identifier,
                        'stores' => array(0),
                        'is_active' => $block->is_active,
                        'content' => $block->content
                    ));
                    $model->save();
                    $this->_getSession()->addSuccess(Mage::helper('export')->__('Imported %s', $model->getTitle()));
                }
            }
        }catch (Exception $e){
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_redirect('*/*/import');
    }
}