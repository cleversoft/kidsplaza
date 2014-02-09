<?php
/**
 * @category    MT
 * @package     MT_CatalogSlider
 * @copyright   Copyright (C) 2008-2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_CatalogSlider_Block_Adminhtml_Widget_Form_Element_Images extends Mage_Adminhtml_Block_Widget
    implements Varien_Data_Form_Element_Renderer_Interface{

    protected $_element;

    public function __construct(){
        parent::__construct();
        $this->setTemplate('mt/catalogslider/widget/form/element/images.phtml');
    }

    public function getElement(){
        return $this->_element;
    }

    public function setElement(Varien_Data_Form_Element_Abstract $element){
        return $this->_element = $element;
    }

    public function render(Varien_Data_Form_Element_Abstract $element){
        $this->setElement($element);
        return $this->toHtml();
    }

    public function getAddButtonHtml(){
        return $this->getChildHtml('addBtn');
    }

    public function getDeleteButtonHtml(){
        return $this->getChildHtml('delBtn');
    }

    public function getBrowserFieldHtml(){
        return $this->getChildHtml('browser');
    }

    protected function _prepareLayout(){
        $addBtn = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'label'     => Mage::helper('mtcatalogslider')->__('Add Image'),
            'onclick'   => 'window.catalogSlider.add()',
            'class'     => 'add'
        ));
        $this->setChild('addBtn', $addBtn);

        $delBtn = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'onclick'   => 'window.catalogSlider.remove({{id}})',
            'class'     => 'delete'
        ));
        $this->setChild('delBtn', $delBtn);

        parent::_prepareLayout();
    }

    public function getItems(){
        $items = array();
        $category = $this->getData('category');
        if (!$category) return $items;
        $object = Mage::helper('core')->jsonDecode($category->getData('slider_params'));
        if (!is_array($object)) return $items;
        foreach ($object as $item){
            $items[]['url'] = $item['uri'];
        }
        return $items;
    }
}