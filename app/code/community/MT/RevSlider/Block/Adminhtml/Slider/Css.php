<?php
/**
 * @category    MT
 * @package     MT_RevSlider
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_RevSlider_Block_Adminhtml_Slider_Css extends Mage_Adminhtml_Block_Template{
    public function _construct(){
        parent::_construct();
        $this->setTemplate('mt/revslider/css.phtml');
    }

    public function _prepareLayout(){
        $this->setChild('save', $this->getLayout()->createBlock('adminhtml/widget_button', '', array(
            'label'     => Mage::helper('revslider')->__('Save'),
            'type'      => 'button',
            'class'     => 'save',
            'id'        => 'btnCssSave',
            'onclick'   => "revLayer.saveCss('{$this->getUrl('*/*/saveCss')}', 'editCssWindow');"
        )));
        $this->setChild('reset', $this->getLayout()->createBlock('adminhtml/widget_button', '', array(
            'label'     => Mage::helper('revslider')->__('Reset to Default'),
            'type'      => 'button',
            'class'     => 'default',
            'onclick'   => sprintf('revLayer.resetCss(\'%s\');',
                $this->getUrl('revslider/index/getCssCaptions', array('reset' => 1))
            )
        )));
        return parent::_prepareLayout();
    }

    public function getCssContent(){
        $css = Mage::getStoreConfig('revslider/config/css');
        if (!$css){
            $file = Mage::getBaseDir().'/js/mt/revslider/rs-plugin/css/captions.css';
            if (is_file($file)) $css = file_get_contents($file);
        }
        return $css;
    }
}