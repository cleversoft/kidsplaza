<?php
 /**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_ProductQuestions
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */
class MT_ProductQuestions_IndexController extends Mage_Core_Controller_Front_Action
{
    protected $_product = null;

    protected $_category = null;

    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::helper('productquestions')->confAllowOnlyLogged() && !Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            if (!Mage::getSingleton('customer/session')->getBeforeQuestionUrl()) {
                Mage::getSingleton('customer/session')->setBeforeQuestionUrl($this->_getRefererUrl());
            }
            Mage::getSingleton('customer/session')->setBeforeQuestionRequest($this->getRequest()->getParams());
        }
    }

    protected function _initProduct($registerObjects = false)
    {
        $product = Mage::helper('productquestions')->getCurrentProduct();

        if(!($product instanceof Mage_Catalog_Model_Product))
        { throw new Exception($this->__('No product selected')); }

        $categoryId = (int) $this->getRequest()->getParam('category', false);
        if($categoryId)
        {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            if( $category
                &&  $category instanceof Mage_Catalog_Model_Category
                &&  $categoryId == $category->getId()
            ) {
                $product = $product->setCategory($category);
                $this->_category = $category;
                if($registerObjects) Mage::register('current_category', $category);
            }
        }
        if($registerObjects)
        {
            Mage::register('product', $product);
            Mage::register('current_product',  $product);
        }
        $this->_product = $product;

        return $this;
    }

    public function indexAction()
    {
        try
        {
            $this->_initProduct(true)->loadLayout();
        }
        catch (Exception $ex)
        {
            Mage::getSingleton('core/session')->addError($ex->getMessage());
            $this->_redirect('/');
            return;
        }
        $this->getLayout()->createBlock('catalog/breadcrumbs');

        if($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs'))
        {
            $breadcrumbsBlock->addCrumb('product', array(
                'label'    => $this->_product->getName(),
                'link'     => $this->_product->getProductUrl(),
                'readonly' => true,
            ));
            $breadcrumbsBlock->addCrumb('questions', array(
                'label' => $this->__('Product Questions'),
            ));
        }
        $title = $this->__('Questions on %s', $this->_product->getName());

        $this->getLayout()->getBlock('productquestions')->setQuestionId($this->getRequest()->getParam('qid'));

        $this->getLayout()->getBlock('head')->setTitle($title);

        $this->renderLayout();
    }

    protected function _loadQuestion($questionId)
    {
        if (!$questionId) {
            return false;
        }
        $model = Mage::getModel('productquestions/productquestions')->load($questionId);
        if (!$model->getQuestionId()) {
            return false;
        }
        Mage::register('current_question', $model);

        return $model;
    }

    protected function _loadProduct($productId)
    {
        if (!$productId) {
            return false;
        }

        $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);
        /* @var $product Mage_Catalog_Model_Product */
        if (!$product->getId() || !$product->isVisibleInCatalog() || !$product->isVisibleInSiteVisibility()) {
            return false;
        }

        Mage::register('current_product', $product);
        Mage::register('product', $product);

        return $product;
    }

    public function postAction()
    {
        $session = Mage::getSingleton('core/session');
        if($customer = Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerId = Mage::getSingleton('customer/session')->getId();
        }
        try
        {
            $this->_initProduct();
        }catch (Exception $ex)
        {
            Mage::getSingleton('core/session')->addError($ex->getMessage());
        }
        $data = $this->getRequest()->getPost();
        if(!empty($data))
        {
            $questions    = Mage::getModel('productquestions/productquestions')->setData($data);
            $validate = $questions->validate();
            if($validate === true)
            {
                $store = Mage::app()->getStore();
                $storeId = $store->getId();
                if(Mage::helper('productquestions')->confDisplayCaptcha()>0){
                    $str_key = $session->getCptchStrKey();
                    $cptch_result = $this->getRequest()->getParam('cptch_result');
                    $cptch_number = $this->getRequest()->getParam('cptch_number');
                    $cptch_time = $this->getRequest()->getParam('cptch_time');

                    if ( isset( $cptch_result ) && isset( $cptch_number ) && isset( $cptch_time )
                        && 0 != strcasecmp(
                        trim(Mage::helper('productquestions')->decode( $cptch_result, $str_key, $cptch_time ) ),
                        $cptch_number )
                    ) {
                        Mage::getSingleton('core/session')->setProductquestionsData($data);
                        $session->addError($this->__('Captcha invalid.'));
                        return $this->_redirectReferer();
                    }
                }
                if($data['parent_question_id']){
                    $parent_id = $data['parent_question_id'];
                }else{
                    $parent_id = 0;
                }
                $questions->setQuestionProductId($this->_product->getId())
                          ->setQuestionParentId($parent_id)
                          ->setQuestionAuthorName($data['question_author_name'])
                          ->setQuestionAuthorEmail($data['question_author_email'])
                          ->setQuestionProductName($this->_product->getName())
                          ->setQuestionText($data['question_text'])
                          ->setQuestionStatus(MT_ProductQuestions_Model_Source_Question_Status::STATUS_PUBLIC)
                          ->setQuestionDate(now())
                          ->setQuestionStoreId($storeId)
                          ->setQuestionStoreIds($storeId)
                          ;
                if($customerId){
                    $questions->setQuestionAuthorId($customerId);
                    $questions->setQuestionAuthorType('customer');
                }
                $questions->save();

                $session->addSuccess($this->__('Your question has been accepted for moderation'));
                $session->setProductquestionsData(false);
            }else{
                Mage::getSingleton('core/session')->setProductquestionsData($data);
                if(is_array($validate))
                    foreach ($validate as $errorMessage)
                        $session->addError($errorMessage);
                else
                    $session->addError($this->__('Unable to post question. Please, try again later.'));
            }
        }
        $this->_redirectReferer();
    }

    /**
     * Show details of one question
     *
     */
    public function viewAction()
    {
        $question = $this->_loadQuestion((int) $this->getRequest()->getParam('qid'));
        if (!$question) {
            $this->_forward('noroute');
            return;
        }
        if($this->getRequest()->getParam('qid')){
            $model = Mage::getModel('productquestions/productquestions');
            $model->load($this->getRequest()->getParam('qid'));
            $model->setHits($model->getHits()+1)
                  ->save();
        }
        $product = $this->_loadProduct($question->getQuestionProductId());
        if (!$product) {
            $this->_forward('noroute');
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

}