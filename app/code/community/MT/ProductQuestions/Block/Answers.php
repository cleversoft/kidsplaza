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
Class MT_ProductQuestions_Block_Answers extends Mage_Core_Block_Template
{
    protected $_collection = null;

    protected $_pagerName = 'productquestions_pager';

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mt/productquestions/answers.phtml');
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
            ->addQuestionFilter($this->getRequest()->getParam('qid'))
            ->addStoreFilter()
            ->setDateOrder();
        return $this;
    }

    protected function _toHtml()
    {
        $this->setShowPager('productquestions' == $this->getRequest()->getModuleName());
        $this->_prepareCollection();
        $pager = $this->getLayout()->getBlock($this->_pagerName);

        $this->_collection = $pager
            ->setCollection($this->_collection)
            ->getCollection(); 
        return parent::_toHtml();
    }

}