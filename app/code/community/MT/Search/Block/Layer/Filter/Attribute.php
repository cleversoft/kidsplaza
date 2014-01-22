<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Block_Layer_Filter_Attribute extends Mage_CatalogSearch_Block_Layer_Filter_Attribute {
	/**
	 * Set filter model name
	 */
	public function __construct() {
		parent::__construct();
		$this->_filterModelName = 'mtsearch/layer_filter_attribute';
	}
}