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
Class MT_ProductQuestions_Block_Categories extends Mage_Core_Block_Template
{
    protected $_collection = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mt/productquestions/categories.phtml');
    }

    protected function _prepareCollection()
    {
        $this->_collection = Mage::getResourceModel('productquestions/categories_collection')
            ->addVisibilityFilter();
        return $this;
    }

    protected function _toHtml()
    {
        $this->setShowPager('productquestions' == $this->getRequest()->getModuleName());
        $this->_prepareCollection();
        return parent::_toHtml();
    }

    public function getNumQuestionsByCat($catId)
    {
        $collection = Mage::getResourceModel('productquestions/productquestions_collection')
            ->addProductFilter(0)
            ->addQuestionFilter(0)
            ->addVisibilityFilter()
            ->addCategoryFilter($catId)
            ->addStoreFilter()
            ->setDateOrder();
        return count($collection);
    }

    public function getCountQuestions()
    {
        $collection = Mage::getResourceModel('productquestions/productquestions_collection')
            ->addProductFilter(0)
            ->addQuestionFilter(0)
            ->addVisibilityFilter()
            ->addStoreFilter()
            ->setDateOrder();
        return count($collection);
    }

    public function getCategoryUrl($id)
    {
        $helper = Mage::helper('productquestions');
        $url = $helper->renderLinkRewriteUrl($id,$type='cat');
        return $url;
    }
}