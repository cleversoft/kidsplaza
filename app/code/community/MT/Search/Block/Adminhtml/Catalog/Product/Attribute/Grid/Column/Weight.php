<?php
/**
 * @category    MT
 * @package     MT_Search
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Search_Block_Adminhtml_Catalog_Product_Attribute_Grid_Column_Weight
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	/**
     * Renders search weight column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row) {
    	$value = $row->getData($this->getColumn()->getIndex());
    	if ($row->getData('is_searchable') && $value && is_numeric($value)) return $value;
    }
}