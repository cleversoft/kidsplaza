<?php
/**
 * @category    MT
 * @package     MT_RatingFilter
 * @copyright   Copyright (C) 2014 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_RatingFilter_Model_Catalog_Layer_Filter_Rating extends MT_Filter_Model_Layer_Filter_Abstract{
    public function _construct(){
        parent::_construct();
        $this->_requestVar = 'rating';
    }

    public function getName(){
        return Mage::helper('ratingfilter')->__('Rating');
    }

    /**
     * @return MT_RatingFilter_Model_Resource_Catalog_Layer_Filter_Rating
     */
    protected function _getResource(){
        if (is_null($this->_resource)){
            $this->_resource = Mage::getResourceModel('ratingfilter/catalog_layer_filter_rating');
        }
        return $this->_resource;
    }

    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock){
        $this->_request = $request;
        $value = $request->getParam($this->_requestVar);

        if (!$value || is_array($value)) return $this;

        $this->_getResource()->applyFilterToCollection($this, $value);
        //$this->getLayer()->getState()->addFilter($this->_createItem('Rating', $value));
        //dont skip me
        //$this->_items = array();

        return $this;
    }

    protected function _getItemsData(){
        $data = $this->_getResource()->getCount($this);
        $flag = Mage::getStoreConfigFlag('ratingfilter/general/result');

        $items = array();
        if (count($data)){
            $i = 1;
            foreach ($data as $count){
                if ($flag){
                    if ($count > 0){
                        $items[] = array(
                            'label' => sprintf('<div class="rating-box"><div class="rating" style="width:%s%%"></div></div>', $i*20),
                            'value' => $i,
                            'count' => (int)$count
                        );
                    }
                }else{
                    $items[] = array(
                        'label' => sprintf('<div class="rating-box"><div class="rating" style="width:%s%%"></div></div>', $i*20),
                        'value' => $i,
                        'count' => (int)$count
                    );
                }
                $i++;
            }
        }

        return $items;
    }
}