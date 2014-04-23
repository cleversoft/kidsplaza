<?php
/**
 * @category    MT
 * @package     MT_RatingFilter
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_RatingFilter_Model_Observer{
    protected function isEnable(){
        return Mage::getStoreConfigFlag('ratingfilter/general/enable');
    }

    public function mtfilterPrepareLayout($observer){
        $block = $observer->getEvent()->getBlock();
        if ($this->isEnable()){
            $ratingBlock = $block->getLayout()->createBlock('ratingfilter/catalog_layer_filter_rating')
                ->setLayer($block->getLayer())
                ->init();

            $block->setChild('rating_filter', $ratingBlock);
        }
    }

    public function mtsearchPrepareLayout($observer){
        $block = $observer->getEvent()->getBlock();
        if ($this->isEnable()){
            $discountBlock = $block->getLayout()->createBlock('ratingfilter/catalogSearch_layer_filter_rating')
                ->setLayer($block->getLayer())
                ->init();

            $block->setChild('rating_filter', $discountBlock);
        }
    }

    public function reindexProduct($observer){
        if ($this->isEnable()){
            $review = $observer->getEvent()->getReview();
            $productId = $review->getData('entity_pk_value');
            if ($productId) Mage::getSingleton('catalogsearch/fulltext')->rebuildIndex(null, array($productId));
        }
    }
}