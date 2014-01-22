<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Block_Adminhtml_Catalog_Product_Attribute_Grid
    extends Mage_Adminhtml_Block_Catalog_Product_Attribute_Grid {
	/**
	 * Prepare product attributes grid columns,
	 * add Search Weight column
	 *
	 * @return Mage_Adminhtml_Block_Catalog_Product_Attribute_Grid
	 */
	protected function _prepareColumns() {
		parent::_prepareColumns();

		$this->addColumnAfter('search_weight', array(
			'header'	=> Mage::helper('mtsearch')->__('Search Weight'),
			'sortable'	=> true,
			'filter'	=> false,
			'index'		=> 'search_weight',
			'renderer'	=> 'mtsearch/adminhtml_catalog_product_attribute_grid_column_weight'
		), 'is_searchable');

		return $this;
	}
}