<?php
class MT_CatalogInventory_Block_Adminhtml_Catalog_Product_Edit_Tab_Super_Config_Grid
    extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid{

    /**
     * Prepare collection
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid
     */
    protected function _prepareCollection(){
        $allowProductTypes = array();
        foreach (Mage::helper('catalog/product_configuration')->getConfigurableAllowedTypes() as $type) {
            $allowProductTypes[] = $type->getName();
        }

        $product = $this->_getProduct();
        $collection = $product->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('type_id')
            ->addAttributeToSelect('price')
            ->addFieldToFilter('attribute_set_id',$product->getAttributeSetId())
            ->addFieldToFilter('type_id', $allowProductTypes)
            ->addFilterByRequiredOptions()
            ->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner');

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $storeId = (int) $this->getRequest()->getParam('store', 0);
            if ($storeId){
                $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
                $adapter = Mage::getSingleton('core/resource')->getConnection('core_write');
                $collection->joinTable(
                    array('ciss' => 'cataloginventory/stock_status'),
                    'product_id=entity_id',
                    array('is_saleable' => new Zend_Db_Expr($adapter->getCheckSql('ciss.stock_status IS NULL', '0', 'ciss.stock_status'))),
                    array('website_id' => $websiteId),
                    'left'
                );
            }else{
                Mage::getModel('cataloginventory/stock_item')->addCatalogInventoryToProductCollection($collection);
            }
        }

        foreach ($product->getTypeInstance(true)->getUsedProductAttributes($product) as $attribute) {
            $collection->addAttributeToSelect($attribute->getAttributeCode());
            $collection->addAttributeToFilter($attribute->getAttributeCode(), array('notnull'=>1));
        }

        $this->setCollection($collection);

        if ($this->isReadonly()) {
            $collection->addFieldToFilter('entity_id', array('in' => $this->_getSelectedProducts()));
        }

        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
        return $this;
    }

    public function getEditParamsForAssociated(){
        return array(
            'base'      =>  '*/*/edit',
            'params'    =>  array(
                'required' => $this->_getRequiredAttributesIds(),
                'popup'    => 1,
                'product'  => $this->_getProduct()->getId(),
                'store'    => $this->_getProduct()->getStoreId()
            )
        );
    }
}