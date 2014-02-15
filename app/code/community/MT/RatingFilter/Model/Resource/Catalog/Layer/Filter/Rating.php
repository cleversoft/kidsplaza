<?php
/**
 * @category    MT
 * @package     MT_RatingFilter
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_RatingFilter_Model_Resource_Catalog_Layer_Filter_Rating extends Mage_Core_Model_Resource_Db_Abstract{
    protected function _construct(){
        $this->_init('rating/rating_vote_aggregated', 'primary_id');
    }

    /**
     * @param $filter Mage_Catalog_Block_Layer_View
     * @param $value mixed
     * @return $this
     */
    public function applyFilterToCollection($filter, $value){
        $select = $filter->getLayer()->getProductCollection()->getSelect();
        $connection = $this->_getReadAdapter();
        $conditions = array(
            'rating.entity_pk_value = e.entity_id',
            $connection->quoteInto('rating.store_id = ?', Mage::app()->getStore()->getId()),
            $connection->quoteInto('rating.percent_approved >= ?', ($value-1)*20),
            $connection->quoteInto('rating.percent_approved <= ?', $value*20)
        );

        $select->join(
            array('rating' => $this->getMainTable()),
            join(' AND ', $conditions),
            ''
        );

        return $this;
    }

    /**
     * @param $filter Mage_Catalog_Model_Layer_Filter_Abstract
     * @return array
     */
    public function getCount($filter){
        $connection = $this->_getReadAdapter();

        /* @var $select Varien_Db_Select */
        $select = clone $filter->getLayer()->getProductCollection()->getSelect();
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        $fromPart = $select->getPart('from');
        foreach ($fromPart as $id => $part){
            if ($id == 'rating'){
                unset($fromPart[$id]);
            }
        }
        $select->setPart('from', $fromPart);

        $conditions = array(
            'rating.entity_pk_value = e.entity_id',
            $connection->quoteInto('rating.store_id = ?', Mage::app()->getStore()->getId())
        );
        $select->join(
            array('rating' => $this->getMainTable()),
            join(' AND ', $conditions),
            array(
                's1' => 'SUM(CASE WHEN rating.percent_approved <= 20 THEN 1 ELSE 0 END)',
                's2' => 'SUM(CASE WHEN rating.percent_approved > 20 AND rating.percent_approved <= 40 THEN 1 ELSE 0 END)',
                's3' => 'SUM(CASE WHEN rating.percent_approved > 40 AND rating.percent_approved <= 60 THEN 1 ELSE 0 END)',
                's4' => 'SUM(CASE WHEN rating.percent_approved > 60 AND rating.percent_approved <= 80 THEN 1 ELSE 0 END)',
                's5' => 'SUM(CASE WHEN rating.percent_approved > 80 THEN 1 ELSE 0 END)'
            )
        );

        return $connection->fetchRow($select);
    }
}