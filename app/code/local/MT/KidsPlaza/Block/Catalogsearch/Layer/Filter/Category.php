<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2008-2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Block_Catalogsearch_Layer_Filter_Category extends MT_Search_Block_Layer_Filter_Category{
    public function __construct(){
        parent::__construct();
        $this->_filterModelName = 'kidsplaza/catalogsearch_layer_filter_category';
    }
}