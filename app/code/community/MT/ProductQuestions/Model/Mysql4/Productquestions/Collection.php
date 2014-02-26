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
class MT_ProductQuestions_Model_Mysql4_Productquestions_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        $this->_init('productquestions/productquestions');
    }

    /*
     * Initializes collection SELECT
     * @return AW_Productquestions_Model_Mysql4_Productquestions_Collection Self instance
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect();

        return $this;
    }

    /*
     * Covers original bug in Varien_Data_Collection_Db
     */
    public function getSelectCountSql(){
        $this->_renderFilters();

        return $this->getConnection()
            ->select()
            ->from($this->getSelect(), 'COUNT(*)');
    }

    /*
     * Covers original bug in Mage_Core_Model_Mysql4_Collection_Abstract
     */
    public function getAllIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->reset(Zend_Db_Select::HAVING);
        $idsSelect->from(null, 'main_table.'.$this->getResource()->getIdFieldName());

        return $this->getConnection()->fetchCol($idsSelect);
    }

    public function addProductFilter($productId)
    {
        $this->getSelect()->where('main_table.question_product_id=?', $productId);

        return $this;
    }

    public function addVisibilityFilter($visibility = MT_ProductQuestions_Model_Source_Question_Status::STATUS_PUBLIC)
    {
        $this->getSelect()->where('main_table.question_status=?', $visibility);

        return $this;
    }

    public function addQuestionFilter($questionId)
    {
        $this->getSelect()->where('main_table.parent_question_id=?', $questionId);

        return $this;
    }

    public function addAnsweredFilter($answered = true)
    {
        if($answered)
            $this->getSelect()->where('main_table.question_reply_text!=?', '');
        else
            $this->getSelect()->where('main_table.question_reply_text=?', '');

        return $this;
    }

    public function addCategoryFilter($catId)
    {
        $this->getSelect()->where('main_table.category_id=?', $catId);
        return $this;
    }

    public function addStoreFilter($storeId = null)
    {
        if(is_null($storeId))
        {
            if(Mage::app()->isSingleStoreMode()) return $this;

            $storeId = Mage::app()->getStore()->getId();
        }
        $this->getSelect()->where('find_in_set(0, question_store_ids) OR find_in_set(?, question_store_ids)', (int)$storeId);

        return $this;
    }

    public function setDateOrder($dir = 'DESC')
    {
        $this->setOrder('main_table.question_date', $dir);
        return $this;
    }

}