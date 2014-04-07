<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Block_Catalog_Product_List_Combo extends Mage_Catalog_Block_Product_View
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

    }
    protected function _beforeToHtml()
    {
        return parent::_beforeToHtml();
    }

    public function getProductsComboCollection()
    {
        $groupCombo = $this->getProduct()->getProductsCombo();
        $productIds = explode(',',$groupCombo);
        $products = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', array('in' => $productIds));
        $products->load();
        return $products;
    }

}