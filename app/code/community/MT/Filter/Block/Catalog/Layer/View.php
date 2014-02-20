<?php
/**
 * @category    MT
 * @package     MT_Filter
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Filter_Block_Catalog_Layer_View extends Mage_Catalog_Block_Layer_View{
    /**
     * Prepare child blocks
     */
    protected function _prepareLayout(){
        parent::_prepareLayout();
        Mage::dispatchEvent('mtfilter_prepare_layout', array('block' => $this));
        return $this;
    }

    /**
     * Get all layer filters
     */
    public function getFilters(){
        $filters = array();
        foreach ($this->getChild('') as $code => $child){
            if (strpos($code, '_filter') > 1 && !isset($filters[$code])){
                $filters[$code] = $child;
            }
        }
        return $filters;
    }

    /**
     * Initialize blocks names
     */
    protected function _initBlocks(){
        $this->_stateBlockName              = 'catalog/layer_state';
        $this->_categoryBlockName           = 'mtfilter/catalog_layer_filter_category';
        $this->_attributeFilterBlockName    = 'mtfilter/catalog_layer_filter_attribute';
        $this->_priceFilterBlockName        = 'mtfilter/catalog_layer_filter_price';
        $this->_decimalFilterBlockName      = 'catalog/layer_filter_decimal';
    }
}