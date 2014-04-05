<?php
/**
 * @category    MT
 * @package     MT_Location
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Location_Block_Adminhtml_Location_Grid extends Mage_Adminhtml_Block_Widget_Grid{
    protected function _prepareCollection(){
        $collection = Mage::getModel('mtlocation/location')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns(){
        $this->addColumn('id', array(
            'header'    => Mage::helper('mtlocation')->__('ID'),
            'index'     => 'id',
            'width'     => 50
        ));
        $this->addColumn('address', array(
            'header'    => Mage::helper('mtlocation')->__('Address'),
            'index'     => 'address'
        ));
        $this->addColumn('position', array(
            'header'    => Mage::helper('mtlocation')->__('Position'),
            'index'     => 'position'
        ));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row){
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}