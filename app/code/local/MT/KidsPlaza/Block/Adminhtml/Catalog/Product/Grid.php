<?php
class MT_KidsPlaza_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    protected function _prepareColumns()
    {
        $this->addColumnAfter('groupon_enable', array(
                'header'=> $this->__('Groupon'),
                'width' => '70px',
                'index' => 'groupon_enable',
                'type'  => 'options',
                'options' => array(1 => $this->__('Yes'))
            ), 'status');
        return parent::_prepareColumns();
    }

    public function setCollection($collection)
    {
        $store = $this->_getStore();
        $priceChange = $this->getRequest()->getParam('price_change');
        if ($priceChange) {
            $today = Mage::getModel('core/date')->date();
            $maxTime = 60*24*60*60;
            $dateCheck = Mage::getModel('core/date')->date('Y-m-d H:i:s',strtotime($today) -  $maxTime);
            $collection->addAttributeToFilter('price_date', array('lteq' => $dateCheck));
            $fromPart = $collection->getSelect()->getPart('from');
            if($store->getId()){
                $fromPart['at_price_date']['joinCondition'] = str_replace(' AND (`at_price_date`.`store_id` = 0)',
                    ' AND (`at_price_date`.`store_id` = '.$store->getId().')',
                    $fromPart['at_price_date']['joinCondition']);
            }else{
                $fromPart['at_price_date']['joinCondition'] = str_replace(' AND (`at_price_date`.`store_id` = 0)',
                    '',
                    $fromPart['at_price_date']['joinCondition']);
            }
            $collection->getSelect()->setPart('from', $fromPart);
        }
        parent::setCollection($collection);
    }
}