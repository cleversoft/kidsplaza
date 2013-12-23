<?php
/**
 * @category    MT
 * @package     MT_DiscountFilter
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_DiscountFilter_Block_Catalog_Layer_Filter_Discount extends Mage_Catalog_Block_Layer_Filter_Abstract{
    public function __construct(){
        parent::__construct();
        $this->_filterModelName = 'discountfilter/catalog_layer_filter_discount';
    }
}