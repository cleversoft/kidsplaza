<?php
/**
 * @category    MT
 * @package     MT_StockNotify
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_StockNotify_Block_Adminhtml_Notify_Grid extends Mage_Adminhtml_Block_Widget_Grid{
    protected function _construct(){
        parent::_construct();
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection(){
        $collection = Mage::getModel('mtstocknotify/notify')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns(){
        $this->addColumn('id', array(
            'header'    => Mage::helper('mtstocknotify')->__('ID'),
            'index'     => 'id',
            'width'     => 100
        ));
        $this->addColumn('product_id', array(
            'header'    => Mage::helper('mtstocknotify')->__('Product'),
            'index'     => 'product_id',
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'mtstocknotify/adminhtml_notify_grid_column_renderer_product'
        ));
        $this->addColumn('stock', array(
            'header'    => Mage::helper('mtstocknotify')->__('Stock Status'),
            'filter'    => false,
            'sortable'  => false,
            'renderer'  => 'mtstocknotify/adminhtml_notify_grid_column_renderer_stock'
        ));
        $this->addColumn('customer_email', array(
            'header'    => Mage::helper('mtstocknotify')->__('Customer'),
            'index'     => 'customer_email',
            'width'     => 300,
            'renderer'  => 'mtstocknotify/adminhtml_notify_grid_column_renderer_customer'
        ));
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('mtstocknotify')->__('Created At'),
            'index'     => 'created_at',
            'type'      => 'datetime',
            'width'     => 300
        ));
        $this->addColumn('status', array(
            'header'    => Mage::helper('mtstocknotify')->__('Status'),
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getModel('mtstocknotify/notify')->getStatuses(),
            'width'     => 100
        ));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row){
        return '';
    }

    protected function _prepareMassaction(){
        $this->setMassactionIdField('stock_notify_id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('mtstocknotify')->__('Change Status'),
            'url'   => $this->getUrl('*/*/massStatus'),
            'additional'    => array(
                'status'    => array(
                    'name'      => 'status',
                    'type'      => 'select',
                    'class'     => 'required-entry',
                    'label'     => Mage::helper('mtstocknotify')->__('Status'),
                    'values'    => Mage::getModel('mtstocknotify/notify')->getStatuses()
                )
            )
        ));
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('mtstocknotify')->__('Delete'),
            'url'   => $this->getUrl('*/*/massDelete')
        ));
        return $this;
    }
}