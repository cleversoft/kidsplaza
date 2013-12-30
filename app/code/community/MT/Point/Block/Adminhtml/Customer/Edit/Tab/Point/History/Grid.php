<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_Block_Adminhtml_Customer_Edit_Tab_Point_History_Grid extends Mage_Adminhtml_Block_Widget_Grid{
    protected function _construct(){
        parent::_construct();
        $this->setUseAjax(true);
        $this->setId('pointHistoryGrid');
    }

    protected function _prepareCollection(){
        $collection = Mage::getModel('mtpoint/history')->getCollection()
            ->addCustomerFilter($this->getCustomerId())
            ->setOrder('id', 'desc');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns(){
        $this->addColumn('id', array(
            'type'      => 'number',
            'index'     => 'id',
            'header'    => Mage::helper('mtpoint')->__('ID')
        ));
        $this->addColumn('balance', array(
            'type'      => 'number',
            'index'     => 'balance',
            'header'    => Mage::helper('mtpoint')->__('Balance')
        ));
        $this->addColumn('delta', array(
            'type'      => 'number',
            'index'     => 'delta',
            'header'    => Mage::helper('mtpoint')->__('Delta')
        ));
        $this->addColumn('created_at', array(
            'type'      => 'date',
            'index'     => 'created_at',
            'header'    => Mage::helper('mtpoint')->__('Date')
        ));
        $this->addColumn('comment', array(
            'type'      => 'text',
            'index'     => 'comment',
            'header'    => Mage::helper('mtpoint')->__('Comment'),
            'sortable'  => false,
            'filter'    => false
        ));
        $this->addColumn('by', array(
            'type'      => 'text',
            'index'     => 'by',
            'header'    => Mage::helper('mtpoint')->__('By'),
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'mtpoint/adminhtml_customer_edit_tab_point_history_grid_column_renderer_by'
        ));
        return parent::_prepareColumns();
    }

    public function getGridUrl(){
        return $this->getUrl('mtpointadmin/customer/historyGrid', array('_current' => true));
    }

    public function getRowUrl($item){
        return '';
    }
}