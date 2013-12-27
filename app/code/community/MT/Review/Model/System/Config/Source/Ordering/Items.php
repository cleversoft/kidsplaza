<?php
class MT_Review_Model_System_Config_Source_Ordering_Items
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'created_at','label'=>'Date'),
            array('value'=>'rating','label'=>'Average rating'),
            array('value'=>'helpfulness','label'=>'Helpfulness')
        );
    }
}
