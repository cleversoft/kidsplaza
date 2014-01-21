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
class MT_ProductQuestions_Block_Summary extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mt/productquestions/summary.phtml');
    }

    protected function _toHtml()
    {
        $product = Mage::helper('productquestions')->getCurrentProduct();
        if(!($product instanceof Mage_Catalog_Model_Product)) return '';

        $productId = $product->getId();
        $category = Mage::registry('current_category');
        if($category instanceof Mage_Catalog_Model_Category)
            $categoryId = $category->getId();
        else $categoryId = false;

        $questionCount = Mage::getResourceModel('productquestions/productquestions_collection')
            ->addProductFilter($productId)
            ->addVisibilityFilter()
            ->addAnsweredFilter()
            ->addStoreFilter()
            ->getSize();

        $params = array('id' => $productId);
        if($categoryId) $params['category'] = $categoryId;
        $this->setQuestionsPageUrl(Mage::getUrl('productquestions/index/index/', $params));
        $this->setQuestionCount($questionCount);
        return parent::_toHtml();
    }
}