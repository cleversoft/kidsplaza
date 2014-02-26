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

class MT_ProductQuestions_Block_Mainview extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mt/productquestions/mainview.phtml');
    }

    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $title = $this->__('Questions');
            $data = Mage::registry('current_question');
            $truncate = Mage::helper('core/string')->truncate($this->escapeHtml($data->getQuestionText()), 100);

            $breadcrumbs->addCrumb('home', array(
                'label' => $this->__('Home'),
                'title' => $this->__('Go to Home Page'),
                'link' => Mage::getBaseUrl()
            ))->addCrumb('questions', array(
                'label'=>$title,
                'title'=>$title,
                'link'=>Mage::getUrl('productquestions/questions/index/')
            ))->addCrumb('id', array(
                'label'=>$truncate,
                'title'=>$truncate,
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

    public function getCountAnswers()
    {
        $data = $this->getQuestionData();
        $questionId = $data->getId();
        $collection = Mage::getResourceModel('productquestions/productquestions_collection')
            ->addProductFilter(0)
            ->addVisibilityFilter()
            ->addQuestionFilter($questionId)
            ->addStoreFilter()
            ->setDateOrder();
        return count($collection);
    }

    /**
     * Prepare link to review list for current product
     *
     * @return string
     */
    public function getBackUrl()
    {
        return Mage::getUrl('*/*/index');
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
