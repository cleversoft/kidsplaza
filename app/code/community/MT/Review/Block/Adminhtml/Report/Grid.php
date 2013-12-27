<?php
class MT_Review_Block_Adminhtml_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_reviewDetailTable;

    public function __construct()
    {
        parent::__construct();
        $this->setId('reportGrid');
        $this->setDefaultSort('report_at');
                    
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('mtreview/report')->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('review_title', array(
            'header'    => Mage::helper('mtreview')->__('Review title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        $this->addColumn('customer_name', array(
            'header'    => Mage::helper('mtreview')->__('Reported by'),
            'align'     => 'left',
            'index'     => 'customer_name',
        ));

        $this->addColumn('report_at', array(
            'header'    => Mage::helper('mtreview')->__('Report date'),
            'index'     => 'report_at',
            'type'      => 'datetime',
            'width'     => 120,
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
                        'url'       => array('base'=> '*/*/deleteAbuse'),
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

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('reivew_title');
        $this->setMassactionIdFilter('customer_name');
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/adminhtml_review/edit',
                             array('id' => $row->getReviewId(), 'ret' => 'abuse' ));
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
