<?php
/**
 * @category    MT
 * @package     MT_Filter
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Filter_Model_Layer_Filter_Attribute extends Mage_Catalog_Model_Layer_Filter_Attribute{
    protected $_request;

    /**
     * Retrieve resource instance
     *
     * @return MT_Filter_Model_Resource_Layer_Filter_Attribute
     */
    protected function _getResource(){
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getResourceModel('mtfilter/layer_filter_attribute');
        }
        return $this->_resource;
    }

    /**
     * Apply attribute option filter to product collection
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Varien_Object $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Attribute
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock){
        $this->_request = $request;
        $filter = $request->getParam($this->_requestVar);
        if (is_array($filter)) {
            return $this;
        }
        $text = $this->_getOptionText($filter);
        if ($filter && strlen($text)) {
            $this->_getResource()->applyFilterToCollection($this, $filter);
            //$this->getLayer()->getState()->addFilter($this->_createItem($text, $filter));
            //remove reset current attribute filter applied
            //$this->_items = array();
        }
        return $this;
    }

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