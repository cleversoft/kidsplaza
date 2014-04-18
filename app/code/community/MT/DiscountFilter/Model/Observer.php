<?php
/**
 * @category    MT
 * @package     MT_DiscountFilter
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_DiscountFilter_Model_Observer{
    protected function isEnable(){
        return Mage::getStoreConfigFlag('discountfilter/general/enable');
    }

    public function mtfilterPrepareLayout($observer){
        $block = $observer->getEvent()->getBlock();
        if ($this->isEnable()){
            $discountBlock = $block->getLayout()->createBlock('discountfilter/catalog_layer_filter_discount')
                ->setLayer($block->getLayer())
                ->init();

            $block->setChild('discount_filter', $discountBlock);
        }
    }

    public function mtsearchPrepareLayout($observer){
        $block = $observer->getEvent()->getBlock();
        if ($this->isEnable()){
            $discountBlock = $block->getLayout()->createBlock('discountfilter/catalogSearch_layer_filter_discount')
                ->setLayer($block->getLayer())
                ->init();

            $block->setChild('discount_filter', $discountBlock);
        }
    }
}