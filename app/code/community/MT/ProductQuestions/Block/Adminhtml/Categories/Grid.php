<?php
/**
 *
 * ------------------------------------------------------------------------------
 * @category     MT
 * @package      MT_ProductQuestions
 * ------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license      GNU General Public License version 2 or later;
 * @author       MagentoThemes.net
 * @email        support@magentothemes.net
 * ------------------------------------------------------------------------------
 *
 */
Class MT_ProductQuestions_Block_Adminhtml_Categories_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('categoriesGrid');
        $this->setDefaultSort('cat_id')
             ->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection(){
        $collection = Mage::getModel('productquestions/categories')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('name', array(
            'index' => 'name',
            'header' => $this->__('Name'),
            'width' => '250px',
        ));

        $this->addColumn('status', array(
            'index' => 'status',
            'header' => $this->__('Visibility'),
            'type' => 'options',
            'options' => MT_ProductQuestions_Model_Source_Question_Status::toShortOptionArray(),
            'align' => 'left',
            'width' => '80px',
        ));


        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {

        if (!($this->getRequest()->getParam('id'))) {
            $this->setMassactionIdField('`main_table`.`cat_id`');
            $this->getMassactionBlock()->setFormFieldName('productquestions');

            $this->getMassactionBlock()->addItem('delete', array(
                'label' => $this->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete', array('ret' => $this->getRequest()->getActionName())),
                'confirm' => $this->__('Are you sure?')
            ));

            $statuses = MT_ProductQuestions_Model_Source_Question_Status::toOptionArray();

            array_unshift($statuses, array('label' => '', 'value' => ''));
            $this->getMassactionBlock()->addItem('status', array(
                'label' => $this->__('Change status'),
                'url' => $this->getUrl('*/*/massStatus', array('_current' => true, 'ret' => $this->getRequest()->getActionName())),
                'additional' => array(
                    'visibility' => array(
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => $this->__('Status'),
                        'values' => $statuses
                    )
                )
            ));
            return $this;
        }
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/adminhtml_categories/edit', array(
            'id' => $row->getCatId()
        ));
    }

}