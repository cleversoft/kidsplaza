<?php
/**
 * @category    MT
 * @package     MT_Erp
 * @copyright   Copyright (C) 2008-2013 MagentoThemes.net. All Rights Reserved.
 * @license     GNU General Public License version 2 or later
 * @author      MagentoThemes.net
 * @email       support@magentothemes.net
 */
class MT_Erp_Model_System_Config_Source_Store{
    protected $service;

    protected function _getService(){
        if (is_null($this->service)){
            $this->service = Mage::getModel('mterp/observer');
        }

        return $this->service;
    }

    protected function _getErpStores(){
        $stores = Mage::app()->loadCache('ERP_STORES');

        if (!$stores) {
            $service    = $this->_getService();
            $stores     = $service->getErpStores();
            Mage::app()->saveCache(
                Mage::helper('core')->jsonEncode($stores),
                'ERP_STORES',
                array(Mage_Core_Model_Resource_Db_Collection_Abstract::CACHE_TAG),
                3600
            );
        }else{
            $stores = Mage::helper('core')->jsonDecode($stores);
        }

        return $stores;
    }

    public function toOptionArray(){
        $stores     = $this->_getErpStores();
        $options    = array();

        foreach ($stores as $store){
            $options[] = array(
                'value' => $store['storeId'],
                'label' => $store['storeName']
            );
        }

        return $options;
    }
}