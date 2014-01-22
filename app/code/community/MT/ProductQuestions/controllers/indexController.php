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
class MT_ProductQuestions_indexController extends Mage_Core_Controller_Front_Action
{
    protected $_product = null;

    protected $_category = null;

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

    public function postAction()
    {
        $session = Mage::getSingleton('core/session');
        if(!Mage::helper('productquestions')->confAllowOnlyLogged()){
            $session->addSuccess($this->__('Please login and question'));
            return $this->_redirect('customer/account/login/');
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
                $questions->setQuestionProductId($this->_product->getId())
                          ->setQuestionAuthorName($data['question_author_name'])
                          ->setQuestionAuthorEmail($data['question_author_email'])
                          ->setQuestionProductName($this->_product->getName())
                          ->setQuestionText($data['question_text'])
                          ->setQuestionDate(now())
                          ->setQuestionStoreId($storeId)
                          ->setQuestionStoreIds($storeId)
                          ->save();

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
}