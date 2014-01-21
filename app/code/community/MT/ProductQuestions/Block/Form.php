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
Class MT_ProductQuestions_Block_Form extends Mage_Core_Block_Template
{
    protected $str_key;
    protected $cptch_time;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mt/productquestions/form.phtml');
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getAction()
    {
        $productId = Mage::app()->getRequest()->getParam('id', false);
        return Mage::getUrl('productquestions/index/post', array('id' => $productId));
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

        $product = Mage::helper('productquestions')->getCurrentProduct();
        if(!($product instanceof Mage_Catalog_Model_Product)) return '';

        $this->setProduct($product);

        $data = Mage::getSingleton('core/session')->getProductquestionsData(true);

        if(is_array($data))
            $this->setData(array_merge($this->getData(), $data));

        return parent::_toHtml();
    }

}