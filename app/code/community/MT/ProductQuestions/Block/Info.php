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

class MT_ProductQuestions_Block_Info extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mt/productquestions/info.phtml');
    }

    public function _prepareLayout()
    {
        parent::_prepareLayout();
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
