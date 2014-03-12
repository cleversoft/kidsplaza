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
class MT_ProductQuestions_QuestionsController extends Mage_Core_Controller_Front_Action
{
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

    public function indexAction()
    {
        $this->loadLayout();

        $this->getLayout()->createBlock('catalog/breadcrumbs');

        if($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs'))
        {
            $breadcrumbsBlock->addCrumb('questions', array(
                'label' => $this->__('Questions'),
                'link' => Mage::getUrl('*/*/index')
            ));
            $categoryId = Mage::app()->getRequest()->getParam('category', false);
            if($categoryId){
                $categories = $this->_loadCategory((int) $categoryId);
                $breadcrumbsBlock->addCrumb('categories', array(
                    'label' => $this->__('Category ').$categories->getName(),
                    'title' => $this->__('Category ').$categories->getName(),
                ));
            }
        }
        $this->getLayout();
        $this->getLayout()->getBlock('head')->setTitle('Questions');
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

    protected function _loadCategory($categoryId)
    {
        if (!$categoryId) {
            return false;
        }
        $model = Mage::getModel('productquestions/categories')->load($categoryId);
        if (!$model->getCatId()) {
            return false;
        }
        Mage::register('current_category', $model);

        return $model;
    }

    public function viewAction()
    {
        $question = $this->_loadQuestion((int) $this->getRequest()->getParam('id'));
        if (!$question) {
            $this->_forward('noroute');
            return;
        }
        if($this->getRequest()->getParam('id')){
            $model = Mage::getModel('productquestions/productquestions');
            $model->load($this->getRequest()->getParam('id'));
            $model->setHits($model->getHits()+1)
                ->save();
        }

        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    public function questionAction()
    {
        $this->loadLayout();
        $this->getLayout()->createBlock('catalog/breadcrumbs');

        if($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs'))
        {
            $breadcrumbsBlock->addCrumb('questions', array(
                'label' => $this->__('Questions Form'),
            ));
        }
        $this->getLayout();
        $this->getLayout()->getBlock('head')->setTitle('Questions');
        $this->renderLayout();
    }

    public function postAction()
    {
        $session = Mage::getSingleton('core/session');
        if($customer = Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerId = Mage::getSingleton('customer/session')->getId();
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
                $urlKey = Mage::getSingleton('catalog/product/url')->formatUrlKey($data['question_text']) ;
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
                $questions->setQuestionProductId(0)
                    ->setQuestionParentId($parent_id)
                    ->setCategoryId($data['category_id'])
                    ->setQuestionAuthorName($data['question_author_name'])
                    ->setQuestionAuthorEmail($data['question_author_email'])
                    ->setIdentifier($urlKey)
                    ->setQuestionText($data['question_text'])
                    ->setQuestionStatus(MT_ProductQuestions_Model_Source_Question_Status::STATUS_PUBLIC)
                    ->setQuestionDate(now())
                    ->setQuestionStoreId($storeId)
                    ->setQuestionStoreIds($storeId);
                if($customerId){
                    $questions->setQuestionAuthorId($customerId);
                    $questions->setQuestionAuthorType('customer');
                }
                $questions->save();
                $questionId = $questions->getQuestionId();
                //Save url rewrite

                $urlQuestionsParam = MT_ProductQuestions_Helper_Data::QUESTIONS_URI_PARAM;
                $urlRewriteKey = preg_replace('/\s+?(\S+)?$/', '', substr($questions->getIdentifier(), 0, 80));
                $urlRewriteSub = substr($urlRewriteKey, 0, -1);
                $id_path = "{$urlQuestionsParam}/{$questionId}";
                $request_path = "{$urlQuestionsParam}/{$urlRewriteSub}.html";
                $target_path = "productquestions/questions/view/id/{$questionId}";
                $rewrite = Mage::getModel('core/url_rewrite');
                $rewrite->setStoreId($storeId)
                    ->setIdPath($id_path)
                    ->setRequestPath($request_path)
                    ->setTargetPath($target_path)
                    ->setIsSystem(false)
                    ->save();
                $session->addSuccess(Mage::helper('productquestions')->__('Your question has been accepted for moderation'));
                $session->setProductquestionsData(false);
            }else{
                $session->addError(Mage::helper('productquestions')->__('Unable to post question. Please, try again later.'));
            }
        }
        if($data['answer_view']){
            $this->_redirectReferer();
        }else{
            $url =  substr(Mage::getUrl($request_path), 0, -1);
            $this->_redirectUrl($url);
        }

    }

}