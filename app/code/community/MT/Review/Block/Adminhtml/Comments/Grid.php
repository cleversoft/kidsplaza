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
class MT_Review_Block_Adminhtml_Comments_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('commentGrid');
        $this->setDefaultSort('created_at');
    }

    protected function _prepareCollection()
    {
        $model = Mage::getModel('mtreview/comment');
        $collection = $model->getCommentCollection();
        $collection->addStoreData();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'        => Mage::helper('mtreview')->__('ID'),
            'align'         => 'right',
            'width'         => '50px',
            'filter_index'  => 'id',
            'index'         => 'id',
        ));

        $this->addColumn('created_at', array(
            'header'        => Mage::helper('mtreview')->__('Created On'),
            'align'         => 'left',
            'type'          => 'datetime',
            'width'         => '100px',
            'filter_index'  => 'created_at',
            'index'         => 'created_at',
        ));


        $this->addColumn('customer_name', array(
            'header'        => Mage::helper('mtreview')->__('Customer Name'),
            'align'         => 'left',
            'width'         => '100px',
            'filter_index'  => 'customer_name',
            'index'         => 'customer_name',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true,
        ));

        $this->addColumn('comments', array(
            'header'        => Mage::helper('mtreview')->__('Comment'),
            'align'         => 'left',
            'filter_index'  => 'comments',
            'index'         => 'comments',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true,
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('mtreview')->__('Store View'),
                'index'         => 'stores',
                'type'          => 'store',
                'width'         => '100px',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                => array($this, '_filterStoreCondition'),
            ));
        }


        $this->addColumn('title', array(
            'header'        => Mage::helper('mtreview')->__('Review'),
            'align'         => 'left',
            'index'         => 'title',
            'type'          => 'text',
            'width'         => '200px',
            'truncate'      => 50,
            'escape'        => true,
        ));


        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('comments');
        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('mtreview')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('mtreview')->__('Are you sure?')
        ));
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/adminhtml_comments/edit', array(
            'id' => $row->getId()
        ));
    }

}
