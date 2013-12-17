<?php
/**
 * @category    MT
 * @package     MT_RevSlider
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_RevSlider_Block_Adminhtml_Slider_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{
    public function __construct(){
        parent::__construct();
        $this->setDestElementId('edit_form');
        $this->setId('slider');
        if ($tab = $this->getRequest()->getParam('activeTab')){
            $this->_activeTab = $tab;
        }else{
            $this->_activeTab = 'info_section';
        }
        $this->setTitle(Mage::helper('revslider')->__('Revolution Slider'));
    }

    public function _prepareLayout(){
        $this->addTabAfter('slide_section', array(
            'label' => Mage::helper('revslider')->__('Slides'),
            'url'   => $this->getUrl('*/*/slide', array('_current' => true)),
            'class' => 'ajax'
        ), 'first_section');
        return parent::_prepareLayout();
    }
}