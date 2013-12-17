<?php
/**
 * @category    MT
 * @package     MT_Widget
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Widget_Model_Widget_Source_Block{
    public function toOptionArray(){
        $collection = Mage::getResourceModel('cms/block_collection')->load();
        $blocks = array();
        foreach ($collection as $item){
            $blocks[] = array(
                'value' => $item->getIdentifier(),
                'label' => $item->getTitle()
            );
        }
        return $blocks;
    }
}