<?php
/**
 * @category    MT
 * @package     MT_DiscountFilter
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_DiscountFilter_Block_Catalog_Layer_View extends Mage_Catalog_Block_Layer_View{
    protected function isEnable(){
        return Mage::getStoreConfigFlag('discountfilter/general/enable');
    }

    protected function _prepareLayout(){
        if ($this->isEnable()){
            $discountBlock = $this->getLayout()->createBlock('discountfilter/catalog_layer_filter_discount')
                ->setLayer($this->getLayer())
                ->init();

            $this->setChild('discount_filter', $discountBlock);
        }
        return parent::_prepareLayout();
    }

    public function getFilters(){
        $filters = parent::getFilters();
        if ($this->isEnable()){
            $filters[] = $this->getChild('discount_filter');
        }
        return $filters;
    }
}