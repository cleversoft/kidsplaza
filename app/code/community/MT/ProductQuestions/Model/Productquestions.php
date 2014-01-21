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
Class MT_ProductQuestions_Model_Productquestions extends Mage_Core_Model_Abstract {

    public function _construct()
    {
        parent::_construct();
        $this->_init('productquestions/productquestions');
    }

    public function validate()
    {
        $errors = array();

        if (!Zend_Validate::is($this->getQuestionAuthorName(), 'NotEmpty')) {
            $errors[] = Mage::helper('productquestions')->__('Question name can\'t be empty');
        }

        if (!Zend_Validate::is($this->getQuestionAuthorEmail(), 'EmailAddress')) {
            $errors[] = Mage::helper('productquestions')->__('Please specify valid email address');
        }

        if (!Zend_Validate::is($this->getQuestionText(), 'NotEmpty')) {
            $errors[] = Mage::helper('productquestions')->__('Question text can\'t be empty');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }
}