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
Class MT_ProductQuestions_Block_Main extends Mage_Core_Block_Template
{
    protected $_collection = null;

    protected $_pagerName = 'productquestions_pager';

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mt/productquestions/main.phtml');
    }

    protected function _prepareCollection()
    {
        $this->_collection = Mage::getResourceModel('productquestions/productquestions_collection')
            ->addProductFilter(0)
            ->addVisibilityFilter()
            ->addStoreFilter()
            ->setDateOrder();
        return $this;
    }

    protected function _toHtml()
    {
        $this->setShowPager('productquestions' == $this->getRequest()->getModuleName());
        $this->_prepareCollection();
        $pager = $this->getLayout()->getBlock($this->_pagerName);
        $categoryId = Mage::app()->getRequest()->getParam('category', false);
        $this->_collection->addQuestionFilter(0);
        if($categoryId){
            $this->_collection->addCategoryFilter($categoryId);
        }
        $this->_collection = $pager
            ->setCollection($this->_collection)
            ->getCollection();
        return parent::_toHtml();
    }

    public function countAnswers($qid)
    {
        $this->_prepareCollection();
        $this->_collection->addQuestionFilter($qid);
        return count($this->_collection);
    }

    public function questionByCategory($categoryId)
    {
        if (!$categoryId) {
            return false;
        }
        $model = Mage::getModel('productquestions/categories')->load($categoryId);
        if (!$model->getCatId()) {
            return false;
        }

        return $model;
    }

    public function getAction()
    {
        return Mage::getUrl('productquestions/questions/question');
    }

    public function getQuestionUrl($id)
    {
        $helper = Mage::helper('productquestions');
        $url = $helper->renderLinkRewriteUrl($id,$type='view');
        return $url;
    }

    public function getCategoryUrl($id)
    {
        $helper = Mage::helper('productquestions');
        $url = $helper->renderLinkRewriteUrl($id,$type='cat');
        return $url;
    }
}