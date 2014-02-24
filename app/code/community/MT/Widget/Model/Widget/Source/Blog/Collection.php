<?php
/**
 * @category    MT
 * @package     MT_Widget
 * @copyright   Copyright (C) 2008-2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Widget_Model_Widget_Source_Blog_Collection{
    public function toOptionArray(){
        $modes = array(
            array('value' => 'latest', 'label' => Mage::helper('mtwidget')->__('Latest Collection'))
        );
        return $modes;
    }
}
