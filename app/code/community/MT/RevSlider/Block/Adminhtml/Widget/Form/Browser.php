<?php
/**
 * @category    MT
 * @package     MT_RevSlider
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_RevSlider_Block_Adminhtml_Widget_Form_Browser extends MT_Extensions_Block_Adminhtml_Widget_Form_Element_Browser{
    protected function _prepareLayout(){
        parent::_prepareLayout();
        $browserBtn = $this->getLayout()->createBlock('adminhtml/widget_button', 'button',  array(
            'label'     => '...',
            'title'     => Mage::helper('mtext')->__('Click to browser media'),
            'type'      => 'button',
            'onclick'   => sprintf('MT.MediabrowserUtility.openDialog(\'%s\', \'browserVideoWindow\', null, null, \'%s\')',
                Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index', array(
                    'static_urls_allowed'   => 1,
                    'target_element_id'     => $this->getElement()->getHtmlId(),
                    'type'                  => 'media',
                    'onInsertCallback'      => 'revLayer.onSelectHtml5Video',
                    'onInsertCallbackParams'=> 'browserVideoWindow'
                )),
                Mage::helper('revslider')->__('Select Video')
            )
        ));
        $this->setChild('browserBtn', $browserBtn);
        return $this;
    }
}