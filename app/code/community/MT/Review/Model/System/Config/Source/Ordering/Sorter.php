<?php

class MT_Review_Model_System_Config_Source_Ordering_Sorter
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'desc',
                'label' => Mage::helper('adminhtml')->__('Newest first'),
            ),
            array(
                'value' => 'asc',
                'label' => Mage::helper('adminhtml')->__('Older first'),
            ),
        );
    }
}