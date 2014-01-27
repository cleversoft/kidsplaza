<?php
/**
 * @category    MT
 * @package     MT_CatalogSlider
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
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

        $browser = $this->getLayout()->createBlock('mtcatalogslider/adminhtml_widget_form_element_browser');
        $this->setChild('browser', $browser);

        parent::_prepareLayout();
    }

    public function getImages(){
        $images = array();
        return $images;
    }
}