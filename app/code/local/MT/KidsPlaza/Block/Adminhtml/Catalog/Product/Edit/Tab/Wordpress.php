<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Block_Adminhtml_Catalog_Product_Edit_Tab_Wordpress extends Mage_Adminhtml_Block_Widget_Grid{
    public function __construct(){
        parent::__construct();
        $this->setId('wordpress_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection(){
        $collection = Mage::getModel('wordpress/post')->getCollection();
        //foreach ($collection as $item) Mage::log($item);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns(){
        $this->addColumn('related', array(
            'header_css_class'  => 'a-center',
            'type'              => 'checkbox',
            'name'              => 'related',
            'values'            => $this->getSelectedWordpress(),
            'align'             => 'center',
            'index'             => 'ID'
        ));
        $this->addColumn('wp_ID', array(
            'header'            => Mage::helper('kidsplaza')->__('ID'),
            'name'              => 'wp_ID',
            'type'              => 'number',
            'validate_class'    => 'validate-number',
            'index'             => 'ID',
            'width'             => 100
        ));
        $this->addColumn('wp_title', array(
            'header'            => Mage::helper('kidsplaza')->__('Title'),
            'name'              => 'wp_title',
            'type'              => 'text',
            'validate_class'    => '',
            'index'             => 'post_title'
        ));
        return parent::_prepareColumns();
    }

    public function getGridUrl(){
        return $this->getUrl('*/*/wpGrid', array('_current' => true));
    }

    public function getSelectedWordpress(){
        $wordpress = $this->getRelatedWordpress();
        if (!is_array($wordpress)){
            $wordpress = explode(',', Mage::registry('current_product')->getRelatedWordpress());
        }
        return $wordpress;
    }
}