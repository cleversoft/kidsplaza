<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_Review
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */
class MT_Review_Block_Adminhtml_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('reportGrid');
        $this->setDefaultSort('report_at');
    }

    protected function _prepareCollection()
    {
        $model = Mage::getModel('mtreview/report');
        $collection = $model->getReportCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('customer_name', array(
            'header'        => Mage::helper('mtreview')->__('Customer Name'),
            'align'         => 'left',
            'filter_index'  => 'customer_name',
            'index'         => 'customer_name',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true,
        ));


        $this->addColumn('title', array(
            'header'        => Mage::helper('mtreview')->__('Report review'),
            'align'         => 'left',
            'index'         => 'title',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true,
        ));

        $this->addColumn('report_at', array(
            'header'        => Mage::helper('mtreview')->__('Report date'),
            'align'         => 'left',
            'type'          => 'datetime',
            'width'         => '150px',
            'filter_index'  => 'report_at',
            'index'         => 'report_at',
        ));

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('mtreview')->__('Action'),
                'width'     => 80,
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('mtreview')->__('Delete'),
                        'url'       => array('base'=> '*/*/deleteReport'),
                        'field'     => 'id',
                        'confirm'   => Mage::helper('mtreview')->__('Are you sure you want to delete the abuse?'),
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            ));


        return parent::_prepareColumns();
    }
}
