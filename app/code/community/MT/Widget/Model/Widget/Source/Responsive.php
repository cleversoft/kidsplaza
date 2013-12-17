<?php
/**
 * @category    MT
 * @package     MT_Widget
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */

class MT_Widget_Model_Widget_Source_Responsive{
    public function toOptionArray(){
        return array(
            array('value'=>'width', 'label'=>Mage::helper('mtwidget')->__('By Width')),
            array('value'=>'breakpoint', 'label'=>Mage::helper('mtwidget')->__('By Breakpoints'))
        );
    }
}