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
Class MT_ProductQuestions_Block_Mainform extends Mage_Core_Block_Template
{
    protected $str_key;
    protected $cptch_time;

    public function __construct()
    {
        parent::__construct();
        $data =  Mage::getSingleton('productquestions/session')->getFormData(true);
        $data = new Varien_Object($data);
        $questionText = $this->getRequest()->getParam('question_text');
        $data->setQuestionText($questionText);
        $this->setTemplate('mt/productquestions/mainform.phtml')
            ->assign('data', $data);
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getAction()
    {
        return Mage::getUrl('productquestions/questions/post');
    }

    protected function _toHtml()
    {
        if( !$this->getQuestionAuthorName()
            ||  !$this->getQuestionAuthorEmail()
        ) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if($customer && $customer->getId())
            {
                if(!$this->getQuestionAuthorName())
                    $this->setQuestionAuthorName($customer->getFirstname());

                if(!$this->getQuestionAuthorEmail())
                    $this->setQuestionAuthorEmail($customer->getEmail());
            }
        }

        return parent::_toHtml();
    }

    public function getCategories()
    {
        $collections = Mage::getResourceModel('productquestions/categories_collection')
            ->addVisibilityFilter();
        return $collections;
    }

}