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

class MT_ProductQuestions_Block_View extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mt/productquestions/view.phtml');
    }

    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $title = $this->__('Answer Question');
            $data = Mage::registry('current_question');
            $categoryId = (int) $this->getRequest()->getParam('category', false);
            $params = array('id' => $data->getQuestionProductId());
            if($categoryId) $params['category'] = $categoryId;
            $truncate = Mage::helper('core/string')->truncate($data->getQuestionText(), $length = 50, $etc = '...', $remainder = '', $breakWords = true);
            $breadcrumbs->addCrumb('home', array(
                'label' => $this->__('Home'),
                'title' => $this->__('Go to Home Page'),
                'link' => Mage::getBaseUrl()
            ))->addCrumb('qid', array(
                'label'=>$truncate,
                'title'=>$truncate,
                'link'=>Mage::getUrl('productquestions/index/index/', $params)
            ))->addCrumb('answer', array(
                'label' => $title,
                'title' => $title,
            ));
        }
        return $this;
    }

    /**
     * Retrieve current question model from registry
     *
     * @return MT_ProducQuestion_Model_Productquestions
     */
    public function getQuestionData()
    {
        return Mage::registry('current_question');
    }

    /**
     * Retrieve current product model from registry
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProductData()
    {
        return Mage::registry('current_product');
    }

    /**
     * Prepare link to review list for current product
     *
     * @return string
     */
    public function getBackUrl()
    {
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $params = array('id' => $this->getProductData()->getId());
        if($categoryId) $params['category'] = $categoryId;
        return Mage::getUrl('*/*/index', $params);
    }

    /**
     * Format date in long format
     *
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_LONG);
    }
}
