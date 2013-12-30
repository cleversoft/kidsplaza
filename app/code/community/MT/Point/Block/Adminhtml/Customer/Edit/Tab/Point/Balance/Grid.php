<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_Block_Adminhtml_Customer_Edit_Tab_Point_Balance_Grid extends Mage_Adminhtml_Block_Widget_Grid{
    protected function _construct(){
        parent::_construct();
        $this->setUseAjax(true);
        $this->setId('pointBalanceGrid');
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
    }

    protected function getCustomer(){
        return Mage::registry('current_customer');
    }

    protected function _prepareCollection(){
        $collection = Mage::getModel('mtpoint/point')->getCollection()
            ->addCustomerFilter($this->getCustomer());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns(){
        $this->addColumn('balance', array(
            'header'    => Mage::helper('mtpoint')->__('Balance'),
            'index'     => 'balance',
            'type'      => 'number',
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'mtpoint/adminhtml_customer_edit_tab_point_balance_grid_column_renderer_balance'
        ));
    }

    public function getRowUrl($item){
        return '';
    }
}