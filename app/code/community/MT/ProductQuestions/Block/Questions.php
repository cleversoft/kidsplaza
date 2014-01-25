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
Class MT_ProductQuestions_Block_Questions extends Mage_Core_Block_Template
{
    protected $_collection = null;

    protected $_pagerName = 'productquestions_pager';

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mt/productquestions/questions.phtml');
    }

    protected function _prepareCollection()
    {
        $product = Mage::helper('productquestions')->getCurrentProduct(true);
        if(!($product instanceof Mage_Catalog_Model_Product)) return false;
        $productId = $product->getId();
        $this->setProduct($product);

        $this->_collection = Mage::getResourceModel('productquestions/productquestions_collection')
            ->addProductFilter($productId)
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
        $this->_collection->addQuestionFilter(0);
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

    public function getQuestionUrl($id)
    {
        $product = Mage::helper('productquestions')->getCurrentProduct(true);
        $productId = $product->getId();
        $category = Mage::registry('current_category');
        if($category instanceof Mage_Catalog_Model_Category)
            $categoryId = $category->getId();
        else $categoryId = false;
        $params = array('id' => $productId);
        if($categoryId) $params['category'] = $categoryId;
        $params['qid'] = $id;
        return Mage::getUrl('*/*/view', $params);
    }
}