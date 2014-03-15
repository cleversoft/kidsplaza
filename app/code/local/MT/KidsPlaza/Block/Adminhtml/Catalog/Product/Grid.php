<?php
class MT_KidsPlaza_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    /*protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $store = $this->_getStore();
        $collection = $this->getCollection();
        $today = Mage::getModel('core/date')->date();
        $maxTime = 60*24*60*60;
        $dateCheck = Mage::getModel('core/date')->date('Y-m-d H:i:s',strtotime($today) -  $maxTime);
        $filter   = $this->getParam($this->getVarNameFilter(), null);
        $data = $this->helper('adminhtml')->prepareFilterString($filter);
        if(isset($data['price_date'])==1){
            $collection->addFieldToFilter('price_date', array('lteq' => $dateCheck));
            $fromPart = $collection->getSelect()->getPart('from');
            $wherePart = $collection->getSelect()->getPart('where');
            if (count($wherePart) >= 2){
                if (strpos($wherePart[0], 'at_price_date.value =') > -1){
                    unset($wherePart[0]);
                    $wherePart[1] = str_replace('AND', '', $wherePart[1]);
                    $collection->getSelect()->setPart('where', $wherePart);
                }
            }
            if($store->getId()>0){
                $fromPart['at_price_date']['joinCondition'] = str_replace(' AND (`at_price_date`.`store_id` = 0)',
                        ' AND (`at_price_date`.`store_id` = '.$store->getId().')',
                        $fromPart['at_price_date']['joinCondition']);
            }
            $collection->getSelect()->setPart('from', $fromPart);
        }
        Mage::log($collection->getSelect()->assemble());
    }*/

    public function setCollection($collection)
    {
        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */

        $store = $this->_getStore();
        $priceChange = $this->getRequest()->getParam('price_change');
        if ($priceChange) {
            $today = Mage::getModel('core/date')->date();
            $maxTime = 60*24*60*60;
            $dateCheck = Mage::getModel('core/date')->date('Y-m-d H:i:s',strtotime($today) -  $maxTime);
            $collection->addAttributeToFilter('price_date', array('lteq' => $dateCheck));
            $fromPart = $collection->getSelect()->getPart('from');
            $fromPart['at_price_date']['joinCondition'] = str_replace(' AND (`at_price_date`.`store_id` = 0)',
                ' AND (`at_price_date`.`store_id` = '.$store->getId().')',
                $fromPart['at_price_date']['joinCondition']);
            $collection->getSelect()->setPart('from', $fromPart);
            Mage::log($collection->getSelect()->assemble());
        }
        parent::setCollection($collection);
    }
}