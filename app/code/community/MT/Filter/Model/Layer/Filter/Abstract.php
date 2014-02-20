<?php
/**
 * @category    MT
 * @package     MT_Filter
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Filter_Model_Layer_Filter_Abstract extends Mage_Catalog_Model_Layer_Filter_Abstract{
    protected $_request;
    protected $_resource;

    /**
     * Create filter item object
     *
     * @param   string $label
     * @param   mixed $value
     * @param   int $count
     * @return  Mage_Catalog_Model_Layer_Filter_Item
     */
    protected function _createItem($label, $value, $count=0){
        return Mage::getModel('mtfilter/layer_filter_item')
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count);
    }

    /**
     * Return current filter
     *
     * @return mixed
     */
    public function getRequestValue(){
        return $this->_request->getParam($this->_requestVar);
    }
}