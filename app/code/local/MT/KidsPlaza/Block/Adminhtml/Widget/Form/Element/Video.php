<?php
/**
 * @category    MT
 * @package     MT_KidsPlaza
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_KidsPlaza_Block_Adminhtml_Widget_Form_Element_Video extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface{
    protected $_element;

    public function __construct(){
        parent::__construct();
        $this->setTemplate('mt/kidsplaza/widget/form/element/video.phtml');
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
        return $this->getChildHtml('deleteBtn');
    }

    protected function _prepareLayout(){
        $addBtn = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'label'     => Mage::helper('kidsplaza')->__('Add Video'),
            'onclick'   => 'return videoForm.add()',
            'class'     => 'add'
        ));
        $this->setChild('addBtn', $addBtn);

        $delBtn = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(array(
            'onclick'   => 'return videoForm.remove({{id}})',
            'class'     => 'delete'
        ));
        $this->setChild('deleteBtn', $delBtn);

        parent::_prepareLayout();
    }

    public function getVideos(){
        $videos = array();
        $data = Mage::helper('core')->jsonDecode($this->getElement()->getValue());
        if (is_array($data)){
            foreach ($data as $video){
                $videos[] = $video;
            }
        }
        return $videos;
    }
}