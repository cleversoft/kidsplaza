<?php
/**
 * @category    MT
 * @package     MT_Filter
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Filter_Block_Catalog_Layer_Filter_Category extends Mage_Catalog_Block_Layer_Filter_Category{
    public function __construct(){
        parent::__construct();
        $this->_filterModelName = 'mtfilter/layer_filter_category';
    }
}