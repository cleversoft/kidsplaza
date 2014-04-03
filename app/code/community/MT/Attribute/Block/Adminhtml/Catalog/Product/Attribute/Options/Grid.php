<?php
/**
 * @category    MT
 * @package     MT_Attribute
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Attribute_Block_Adminhtml_Catalog_Product_Attribute_Options_Grid extends Mage_Adminhtml_Block_Widget_Grid{
    public function __construct(){
        $this->setDefaultSort('option_id');
        $this->setDefaultDir('desc');
        return parent::__construct();
    }

    protected function _prepareCollection(){
        $attributeModel = Mage::getSingleton('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'brand');
        $optionsModel = $attributeModel->getSource()->getAllOptions(false);
        $attributeModel->setOptionsModel($optionsModel);
        Mage::register('attribute', $attributeModel);
        $collection = Mage::getResourceModel('eav/entity_attribute_option_collection');
        $collection->setAttributeFilter($attributeModel->getAttributeId());
        $collection->setStoreFilter();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns(){
        $this->addColumn('option_id', array(
            'header'    => Mage::helper('mtattribute')->__('ID'),
            'index'     => 'option_id',
            'sortable'  => false,
            'filter'    => false,
            'width'     => 50
        ));
        $this->addColumn('attribute_label', array(
            'header'    => Mage::helper('mtattribute')->__('Attribute'),
            'width'     => 50,
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'mtattribute/adminhtml_widget_grid_column_renderer_attribute'
        ));
        $this->addColumn('option_label', array(
            'header'    => Mage::helper('mtattribute')->__('Option'),
            'width'     => 50,
            'sortable'  => false,
            'index'     => 'tdv.value',
            'renderer'  => 'mtattribute/adminhtml_widget_grid_column_renderer_option_label'
        ));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row){
        return $this->getUrl('*/*/edit', array('attribute_id' => $row->getAttributeId(), 'option_id' => $row->getId()));
    }
}