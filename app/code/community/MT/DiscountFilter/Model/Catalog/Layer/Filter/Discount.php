<?php
/**
 * @category    MT
 * @package     MT_DiscountFilter
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_DiscountFilter_Model_Catalog_Layer_Filter_Discount extends MT_Filter_Model_Layer_Filter_Abstract{
    public function _construct(){
        parent::_construct();
        $this->_requestVar = 'discount';
    }

    public function getName(){
        return Mage::helper('discountfilter')->__('Promotion');
    }

    /**
     * @return MT_DiscountFilter_Model_Resource_Catalog_Layer_Filter_Discount
     */
    protected function _getResource(){
        if (is_null($this->_resource)){
            $this->_resource = Mage::getResourceModel('discountfilter/catalog_layer_filter_discount');
        }
        return $this->_resource;
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock){
        $this->_request = $request;
        $value = $request->getParam($this->_requestVar);

        if (!$value || is_array($value)) return $this;

        $this->_getResource()->applyFilterToCollection($this, $value);
        //$this->getLayer()->getState()->addFilter($this->_createItem('Discount', $value));
        //dont skip me
        //$this->_items = array();

        return $this;
    }

    protected function _getItemsData(){
        $data = $this->_getResource()->getCount($this);

        if (isset($data['count']) && $data['count'] > 0){
            return array(
                array(
                    'label' => Mage::helper('discountfilter')->__('Discount'),
                    'value' => 1,
                    'count' => (int)$data['count']
                )
            );
        }else{
            if (!Mage::getStoreConfigFlag('discountfilter/general/result')){
                return array(
                    array(
                        'label' => Mage::helper('discountfilter')->__('Discount'),
                        'value' => 1,
                        'count' => 0
                    )
                );
            }else return array();
        }
    }
}