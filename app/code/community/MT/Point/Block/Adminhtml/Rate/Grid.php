<?php
/**
 * @category    MT
 * @package     MT_Point
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Point_Block_Adminhtml_Rate_Grid extends Mage_Adminhtml_Block_Widget_Grid{
    protected function _prepareCollection(){
        $collection = Mage::getModel('mtpoint/rate')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns(){
        $this->addColumn('id', array(
            'header'    => Mage::helper('mtpoint')->__('ID'),
            'index'     => 'id',
            'width'     => 50
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('mtpoint')->__('Money Amount'),
            'index'     => 'amount'
        ));
        $this->addColumn('point', array(
            'header'    => Mage::helper('mtpoint')->__('Points'),
            'index'     => 'point'
        ));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row){
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}