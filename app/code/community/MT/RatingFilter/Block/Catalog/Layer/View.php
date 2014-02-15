<?php
/**
 * @category    MT
 * @package     MT_RatingFilter
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_RatingFilter_Block_Catalog_Layer_View extends MT_DiscountFilter_Block_Catalog_Layer_View{
    protected function isEnable(){
        return Mage::getStoreConfigFlag('ratingfilter/general/enable');
    }

    protected function _prepareLayout(){
        if ($this->isEnable()){
            $ratingBlock = $this->getLayout()->createBlock('ratingfilter/catalog_layer_filter_rating')
                ->setLayer($this->getLayer())
                ->init();

            $this->setChild('rating_filter', $ratingBlock);
        }
        return parent::_prepareLayout();
    }

    public function getFilters(){
        $filters = parent::getFilters();
        if ($this->isEnable()){
            $filters[] = $this->getChild('rating_filter');
        }
        return $filters;
    }
}